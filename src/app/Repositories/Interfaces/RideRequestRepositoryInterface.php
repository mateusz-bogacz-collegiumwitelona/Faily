<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface RideRequestRepositoryInterface extends RepositoryInterface
{
    public function getRideRequests($rideId);
    public function hasUserRequested($rideId, $userId);
    public function countAcceptedRequests($rideId);
    public function getRequestWithRelations($requestId);
    public function getUserRideRequests($userId, $perPage = 10);

}
