<?php

namespace App\Services\Interfaces;

use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Database\Eloquent\Collection;

interface EventAttendeeServiceInterface extends ServiceInterface
{
    public function getEventAttendees(Event $event);

    public function canUserRegisterForEvent($eventId, $userId);

    public function registerForEvent(Event $event, array $data, $userId);

    public function updateAttendeeStatus(Event $event ,EventAttendee $attendee, $status,  $userId);

    public function cancelAttendance(Event $event, EventAttendee $attendee,  $userId);
    public function getUserAttendances($userId);

    public function getUserApplicationsData($userId);

}
