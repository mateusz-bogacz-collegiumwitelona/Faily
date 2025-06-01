<?php

namespace App\Services;

use App\Models\Ride;
use App\Models\RideRequest;
use App\Notifications\RideRequestStatusChanged;
use App\Notifications\NewRideRequest;
use App\Repositories\Interfaces\RideRepositoryInterface;
use App\Repositories\Interfaces\RideRequestRepositoryInterface;
use App\Services\Interfaces\CacheServiceInterface;
use App\Services\Interfaces\RideRequestServiceInterface;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\Activity;

class RideRequestService extends BaseService implements RideRequestServiceInterface
{
    protected $rideRepository;
    protected $repository;
    protected $cacheService;

    public function __construct(
        RideRequestRepositoryInterface $repository,
        RideRepositoryInterface $rideRepository,
        ?CacheServiceInterface $cacheService = null
    ) {
        parent::__construct($repository, $cacheService);
        $this->rideRepository = $rideRepository;

        $this->cacheTags = ['ride_requests', 'rides'];
        $this->cachePrefix = 'ride_request';
    }

    public function getRideRequestsForDriver($rideId, $userId)
    {
        $ride = $this->rideRepository->find($rideId);

        if ($userId !== $ride->driver_id) {
            throw new \Exception('You do not have permission to view these requests.');
        }

        if (!$this->useCache()) {
            return [
                'requests' => $this->repository->getRideRequests($rideId),
                'ride' => $ride
            ];
        }

        return $this->cacheService->remember(
            "{$this->cachePrefix}.ride.{$rideId}",
            function () use ($rideId, $ride) {
                return [
                    'requests' => $this->repository->getRideRequests($rideId),
                    'ride' => $ride
                ];
            },
            $this->cacheTimes['all']
        );
    }

    public function canUserRequestRide(Ride $ride, $userId)
    {
        $result = [
            'canRequest' => true,
            'message' => null
        ];

        if ($userId === $ride->driver_id) {
            $result['canRequest'] = false;
            $result['message'] = "You can't send an application to your own ride.";
            return $result;
        }

        if ($this->repository->hasUserRequested($ride->id, $userId)) {
            $result['canRequest'] = false;
            $result['message'] = 'You have already sent an application for this passage.';
            return $result;
        }

        return $result;
    }

    public function createRequest(array $data, $userId)
    {
        $ride = $this->rideRepository->find($data['ride_id']);

        if ($userId === $ride->driver_id) {
            throw new \Exception("You can't send an application to your own ride.");
        }

        $acceptedRequests = $this->repository->countAcceptedRequests($ride->id);
        if ($acceptedRequests >= $ride->available_seats) {
            throw new \Exception('No available seats on this ride.');
        }

        $requestData = [
            'ride_id' => $data['ride_id'],
            'passenger_id' => $userId,
            'status' => 'pending',
            'message' => $data['message'] ?? null,
        ];

        $rideRequest = $this->repository->create($requestData);

        $ride->driver->notify(new NewRideRequest($rideRequest, $ride, Auth::user()));

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.ride.{$data['ride_id']}");
            $this->cacheService->forget("ride.{$data['ride_id']}.with_relations");
            $this->cacheService->flushTags(['ride_requests']);
        }

        return $rideRequest;
    }

    public function updateRequestStatus(RideRequest $rideRequest, $status, $userId)
    {
        $ride = $this->rideRepository->find($rideRequest->ride_id);

        if ($userId !== $ride->driver_id) {
            throw new \Exception('You do not have the authority to manage this request.');
        }

        if ($status === 'accepted') {
            $acceptedCount = $this->repository->countAcceptedRequests($ride->id);
            if ($acceptedCount >= $ride->available_seats) {
                throw new \Exception('No seats available on this ride.');
            }
        }

        $oldStatus = $rideRequest->status;
        $rideRequest = $this->repository->update($rideRequest->id, ['status' => $status]);

        $rideRequest->passenger->notify(new RideRequestStatusChanged($rideRequest, $ride));

        $passengerName = $rideRequest->passenger->name ?? 'Passenger';
        $driverName = $ride->driver->name ?? 'Driver';
        $eventTitle = $ride->event->title ?? 'Event';

        Activity::performedOn($rideRequest)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $status,
                'ride_id' => $ride->id,
                'event_id' => $ride->event_id,
                'event_title' => $eventTitle,
                'driver_id' => $ride->driver_id,
                'passenger_id' => $rideRequest->passenger_id
            ])
            ->log("The status of the travel request from {$passengerName} to {$driverName} for the event \"{$eventTitle}\" changed from \"{$oldStatus}\" to \"{$status}\"");

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.ride.{$rideRequest->ride_id}");
            $this->cacheService->forget("ride.{$rideRequest->ride_id}.with_relations");
            $this->cacheService->forget("{$this->cachePrefix}.{$rideRequest->id}");
            $this->cacheService->flushTags(['ride_requests']);

            if ($status === 'accepted') {
                $eventId = $ride->event_id;
                if ($eventId) {
                    $this->cacheService->forget("event.{$eventId}.with_relations");
                }
            }
        }

        return $rideRequest;
    }

    public function cancelRequest(RideRequest $rideRequest, $userId)
    {
        if ($userId !== $rideRequest->passenger_id) {
            throw new \Exception('You do not have the authority to cancel this request.');
        }

        $result = $this->repository->delete($rideRequest->id);

        if ($this->useCache()) {
            $this->cacheService->forget("{$this->cachePrefix}.ride.{$rideRequest->ride_id}");
            $this->cacheService->forget("ride.{$rideRequest->ride_id}.with_relations");
            $this->cacheService->forget("{$this->cachePrefix}.{$rideRequest->id}");
            $this->cacheService->flushTags(['ride_requests']);
        }

        return $result;
    }

    public function getUserRideRequests($userId)
    {
        if (!$this->useCache()) {
            return $this->repository->getUserRideRequests($userId);
        }

        return $this->cacheService->remember(
            "user.{$userId}.ride_requests",
            function () use ($userId) {
                return $this->repository->getUserRideRequests($userId);
            },
            $this->cacheTimes['all'] ?? 900
        );
    }
}
