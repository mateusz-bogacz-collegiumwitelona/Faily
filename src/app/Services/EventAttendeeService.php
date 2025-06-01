<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Notifications\EventAttendeeStatusChanged;
use App\Repositories\Interfaces\EventAttendeeRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Services\Interfaces\CacheServiceInterface;
use App\Services\Interfaces\EventAttendeeServiceInterface;
use App\Services\Interfaces\RideRequestServiceInterface;

class EventAttendeeService extends BaseService implements EventAttendeeServiceInterface
{

    protected $repository;

    protected $eventRepository;

    protected $cacheService;

    public function __construct(
        EventAttendeeRepositoryInterface $repository,
        EventRepositoryInterface $eventRepository,
        RideRequestServiceInterface $rideRequestService = null,
        ?CacheServiceInterface $cacheService = null
    ) {
        parent::__construct($repository, $cacheService);

        $this->eventRepository = $eventRepository;
        $this->rideRequestService = $rideRequestService;

        $this->cacheTags = ['event_attendees', 'events'];
        $this->cachePrefix = 'event_attendee';
    }

    public function getEventAttendees(Event $event)
    {
        if (!$this->useCache()) {
            return $this->repository->getEventAttendees($event->id);
        }

        return $this->cacheService->remember(
            "{$this->cachePrefix}.event.{$event->id}",
            function () use ($event) {
                return $this->repository->getEventAttendees($event->id);
            },
            $this->cacheTimes['all']
        );
    }

    public function canUserRegisterForEvent($eventId, $userId)
    {
        $event = $this->eventRepository->find($eventId);

        $result = [
            'canRegister' => true,
            'message' => null
        ];

        if ($event->user_id === $userId) {
            $result['canRegister'] = false;
            $result['message'] = 'You are the organizer of this event.';
            return $result;
        }

        if ($this->repository->isUserAttending($eventId, $userId)) {
            $result['canRegister'] = false;
            $result['message'] = 'You are already signed up for this event.';
            return $result;
        }

        if (!$this->eventRepository->hasAvailableSpots($eventId)) {
            $result['canRegister'] = false;
            $result['message'] = 'No vacancies for this event.';
            return $result;
        }

        return $result;
    }

    public function registerForEvent(Event $event, array $data, $userId)
    {

        $availableSpots = $this->eventRepository->getAvailableSpotsCount($event->id);
        if ($availableSpots < $data['attendees_count']) {
            throw new \Exception("Not enough seats. Available seats: {$availableSpots}.");
        }

        $attendeeData = [
            'event_id' => $event->id,
            'user_id' => $userId,
            'status' => 'pending',
            'message' => $data['message'] ?? null,
            'attendees_count' => $data['attendees_count'],
        ];

        $result = $this->repository->createAttendeeRequest($attendeeData);

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.event.{$event->id}");
            $this->cacheService->forget("event.{$event->id}.with_relations");
            $this->cacheService->forget("user.{$userId}.attendances");
            $this->cacheService->flushTags(['event_attendees']);
        }

        return $result;
    }

    public function updateAttendeeStatus(Event $event, EventAttendee $attendee, $status, $userId)
    {
        if ($event->user_id !== $userId) {
            throw new \Exception('You do not have permission to manage participants.');
        }

        if ($status === 'accepted') {
            $availableSpots = $this->eventRepository->getAvailableSpotsCount($event->id);
            if ($availableSpots < $attendee->attendees_count) {
                throw new \Exception("Not enough seats available. Available seats: {$availableSpots}.");
            }
        }

        $oldStatus = $attendee->status;
        $attendee = $this->repository->updateStatus($attendee->id, $status);

        $attendee->user->notify(new EventAttendeeStatusChanged($attendee, $event, $oldStatus));

        $userName = $attendee->user->name ?? 'User';

        $statusMessage = '';
        if ($status === 'accepted') {
            $statusMessage = "Status użytkownika {$attendee->user->name} został zmieniony na 'zaakceptowany'. Użytkownik jest teraz zapisany na wydarzenie.";
        } else if ($status === 'rejected') {
            $statusMessage = "Status użytkownika {$attendee->user->name} został zmieniony na 'odrzucony'.";
        }

        activity('event_attendees')
            ->performedOn($attendee)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $attendee->status,
                'event_id' => $event->id,
                'event_title' => $event->title
            ])
            ->log("Changed the status of {$userName} participation in the \"{$event->title}\" event from \"{$oldStatus}\" to \"{$attendee->status}\"");

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.event.{$event->id}");
            $this->cacheService->forget("event.{$event->id}.with_relations");
            $this->cacheService->forget("user.{$attendee->user_id}.attendances");
            $this->cacheService->flushTags(['event_attendees']);
        }

        return $attendee;
    }

    public function cancelAttendance(Event $event, EventAttendee $attendee, $userId)
    {
        if ($userId !== $attendee->user_id && $userId !== $event->user_id) {
            throw new \Exception('You do not have permissions to cancel this application.');
        }

        $result = $this->repository->delete($attendee->id);

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.event.{$event->id}");
            $this->cacheService->forget("event.{$event->id}.with_relations");
            $this->cacheService->forget("user.{$attendee->user_id}.attendances");
            $this->cacheService->flushTags(['event_attendees']);
        }

        return $result;
    }

    public function getUserAttendances($userId)
    {
        if (!$this->useCache()) {
            return $this->repository->getUserAttendees($userId);
        }

        return $this->cacheService->remember(
            "user.{$userId}.attendances",
            function () use ($userId) {
                return $this->repository->getUserAttendees($userId);
            },
            $this->cacheTimes['all']
        );
    }

    public function getUserApplicationsData($userId)
    {
        // Pobierz zgłoszenia na wydarzenia
        $eventAttendees = $this->getUserAttendances($userId);

        // Pobierz zgłoszenia na przejazdy (jeśli service jest dostępny)
        $rideRequests = collect(); // Pusty collection jako fallback

        if ($this->rideRequestService) {
            try {
                $rideRequests = $this->rideRequestService->getUserRideRequests($userId);
            } catch (\Exception $e) {
                // W przypadku błędu, pozostaw pusty collection
                \Log::warning('Failed to fetch ride requests for user: ' . $userId, ['error' => $e->getMessage()]);
                $rideRequests = collect();
            }
        }

        return [
            'eventAttendees' => $eventAttendees,
            'rideRequests' => $rideRequests
        ];
    }
}
