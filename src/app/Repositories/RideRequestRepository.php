<?php

namespace App\Repositories;

use App\Models\RideRequest;
use App\Repositories\Interfaces\RideRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RideRequestRepository extends BaseRepository implements RideRequestRepositoryInterface
{
    public function model()
    {
        return RideRequest::class;
    }

    public function getRideRequests($rideId)
    {
        return $this->model->where('ride_id', $rideId)
            ->with('passenger')
            ->get();
    }

    public function hasUserRequested($rideId, $userId)
    {
        return $this->model->where('ride_id', $rideId)
            ->where('passenger_id', $userId)
            ->exists();
    }

    public function countAcceptedRequests($rideId)
    {
        return $this->model->where('ride_id', $rideId)
            ->where('status', 'accepted')
            ->count();
    }

    public function getRequestWithRelations($requestId)
    {
        $request = $this->find($requestId);
        $request->load(['ride.driver', 'ride.event', 'passenger']);
        return $request;
    }

    public function getUserRideRequests($userId, $perPage = 10)
    {
        return $this->model
            ->with(['ride.event.photos', 'ride.driver'])
            ->where('passenger_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
