<?php

namespace App\Repositories;
use App\Models\EventAttendee;
use App\Models\Event;
use App\Repositories\Interfaces\EventAttendeeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EventAttendeeRepository extends BaseRepository implements EventAttendeeRepositoryInterface
{
    public function model()
    {
        return EventAttendee::class;
    }

    public function getEventAttendees($eventId)
    {
        return $this->model->where('event_id', $eventId)
            ->with('user')
            ->get();
    }

    public function isUserAttending($eventId, $userId)
    {
        return $this->model->where('event_id', $eventId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function createAttendeeRequest(array $data)
    {
        return $this->model->create($data);
    }

    public function updateStatus($attendeeId, $status)
    {
        $attendee = $this->find($attendeeId);
        $attendee->status = $status;
        $attendee->save();
        return $attendee;
    }

    public function getEventOwner($eventId)
    {
       $event = Event::findOrFail($eventId);
       return $event->user_id;
    }

    public function getUserAttendees($userId)
    {
        $now = Carbon::now();
        $oneDayAgo = $now->copy()->subDay();

        return $this->model
            ->where('user_id', $userId)
            ->where(function($query) use ($oneDayAgo) {
                $query->where('status', '!=', 'rejected')
                    ->orWhere(function($q) use ($oneDayAgo) {
                        $q->where('status', 'rejected')
                            ->where('updated_at', '>=', $oneDayAgo);
                    });
            })
            ->with(['event.photos', 'event.user'])
            ->whereHas('event', function($query) use ($now) {
                $query->where('date', '>=', $now->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
