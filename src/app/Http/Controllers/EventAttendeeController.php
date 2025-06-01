<?php

namespace App\Http\Controllers;

use AllowDynamicProperties;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\EventAttendeeServiceInterface;

class EventAttendeeController extends Controller
{
    public function __construct(EventAttendeeServiceInterface $eventAttendeeService)
    {
        $this->middleware('auth');
        $this->eventAttendeeService = $eventAttendeeService;
    }

    public function index(Event $event)
    {
        if (Auth::id() !== $event->user_id) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You do not have permission to view the list of participants.');
        }
        $attendees = $this->eventAttendeeService->getEventAttendees($event);

        return view('events.attendees.partials.attendees-table', compact('event', 'attendees'));
    }


    public function create(Event $event)
    {
        $result = $this->eventAttendeeService->canUserRegisterForEvent($event->id, Auth::id());

        if (!$result['canRegister']) {
            return redirect()->route('events.show', $event)
                ->with('info', $result['message']);
        }

        return view('events.attendees.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:500',
            'attendees_count' => 'required|integer|min:1|max:10',
        ]);

        try {
            $this->eventAttendeeService->registerForEvent($event, $validated, Auth::id());

            return redirect()->route('events.show', $event)
                ->with('success', 'You have been signed up for the event! Your application is awaiting approval.');
        } catch (\Exception $e) {
            return redirect()->route('events.show', $event)
                ->with('error', $e->getMessage());
        }
    }
    public function update(Request $request, Event $event, EventAttendee $attendee)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        try {
            $this->eventAttendeeService->updateAttendeeStatus($event, $attendee, $validated['status'], Auth::id());
            return redirect()->route('events.attendees.index', $event)
                ->with('success', 'Participant status has been updated!');
        } catch (\Exception $e) {
            return redirect()->route('events.attendees.index', $event)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Event $event, EventAttendee $attendee)
    {
        try {
            $this->eventAttendeeService->cancelAttendance($event, $attendee, Auth::id());

            if (Auth::id() === $event->user_id) {
                return redirect()->route('events.attendees.index', $event)
                    ->with('success', 'Participant has been deleted!');
            } else {
                return redirect()->route('events.show', $event)
                    ->with('success', 'Participant has been deleted.');
            }
        } catch (\Exception $e) {
            return redirect()->route('events.show', $event)
                ->with('error', $e->getMessage());
        }
    }

    public function myApplications()
    {
        $userId = Auth::id();

        $applicationData = $this->eventAttendeeService->getUserApplicationsData($userId);

        return view('events.my-applications', $applicationData);
    }
}

