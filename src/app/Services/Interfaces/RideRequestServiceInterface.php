<?php

namespace App\Services\Interfaces;

use App\Models\Ride;
use App\Models\RideRequest;

interface RideRequestServiceInterface extends ServiceInterface
{
    public function getRideRequestsForDriver($rideId, $userId);
    public function canUserRequestRide(Ride $ride, $userId);
    public function createRequest(array $data, $userId);
    public function updateRequestStatus(RideRequest $rideRequest, $status, $userId);
    public function cancelRequest(RideRequest $rideRequest, $userId);
    public function getUserRideRequests($userId);
}
