<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    public function model()
    {
        return Event::class;
    }

    public function getWithRelations(array $relations = [], $perPage = 5)
    {
        return $this->model->with($relations)->latest()->paginate($perPage);
    }

    public function getFilteredEvents(array $filters = [], $perPage = 6)
    {
        $query = $this->model->with(['user', 'photos', 'attendees'])
            ->where('date', '>=', now());

        if(isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('location_name', 'like', "%$search%");
            });
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        if (isset($filters['has_ride_sharing']) && !empty($filters['has_ride_sharing'])) {
            $query->where('has_ride_sharing', true);
        }

        if (isset($filters['has_available_spots']) && !empty($filters['has_available_spots'])) {
            $query->where('people_count', '>', function($subQuery) {
                $subQuery->selectRaw('COALESCE(SUM(attendees_count), 0)')
                    ->from('event_attendees')
                    ->whereRaw('event_attendees.event_id = events.id')
                    ->where('status', 'accepted');
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function getPopularEvents($limit = 5)
    {
        return $this->model->withCount(['acceptedAttendees as attendees_count'])
            ->where('date', '>=', now())
            ->orderBy('attendees_count', 'desc')
            ->take($limit)
            ->get();
    }

    public function getUpcomingEvents($limit = 5)
    {
        return $this->model->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take($limit)
            ->get();
    }

    public function getUserEvents($userId, $perPage = 6)
    {
        return $this->model->where('user_id', $userId)->paginate($perPage);
    }

    public function isUserAttending($eventId, $userId)
    {
        $event = $this->model->find($eventId);
        return $event->isUserAttending($userId);
    }

    public function getAvailableSpotsCount($userId)
    {
        $event = $this->find($userId);
        return $event->getAvailableSpotsCount();
    }

    public function hasAvailableSpots($eventId)
    {
        $event = $this->find($eventId);
        return $event->hasAvailableSpots();
    }
}
