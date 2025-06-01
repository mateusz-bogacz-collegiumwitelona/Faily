<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\EventServiceInterface;
use Carbon\Carbon;
class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventServiceInterface $eventService)
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->eventService = $eventService;
    }

    public function index()
    {
        $events = $this->eventService->getEventsForListing();
        return view('events.my_events', compact('events'));
    }

    public function create(Request $request)
    {
        $preset_event_id = $request->input('preset_event_id');
        $preset_event = null;

        if ($preset_event_id) {
            $preset_event = $this->eventService->findById($preset_event_id);
        }

        return view('events.create', compact('preset_event'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'date' => 'required|date',
            'latitude' =>  'required|numeric',
            'longitude' =>  'required|numeric',
            'location_name' => 'required|string|max:255',
            'has_ride_sharing' => 'boolean',
            'people_count' => 'required|integer|min:1',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'vehicle_description' => 'required_if:has_ride_sharing,1|string|max:255|nullable',
            'available_seats' => 'required_if:has_ride_sharing,1|integer|min:1|nullable',
            'meeting_location_name' => 'required_if:has_ride_sharing,1|string|max:255|nullable',
            'meeting_latitude' => 'required_if:has_ride_sharing,1|numeric|nullable',
            'meeting_longitude' => 'required_if:has_ride_sharing,1|numeric|nullable',
        ]);

        $event = $this->eventService->storeWithRelations($validated, Auth::id());

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
        $event =  $this->eventService->getEventWithRelations($event->id);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        if (!$this->eventService->canUserManageEvent($event->id, Auth::id())) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to edit this event.');
        }

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        if (!$this->eventService->canUserManageEvent($event->id, Auth::id())) {
            return redirect()->route('events.index')
                ->with('error', 'You do not have permission to update this event.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'latitude' =>  'required|numeric',
            'longitude' =>  'required|numeric',
            'location_name' => 'required|string|max:255',
            'has_ride_sharing' => 'boolean',
            'people_count' => 'required|integer|min:1',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = $this->eventService->updateWithRelations($event->id, $validated, Auth::id());

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {
        if (!$this->eventService->canUserManageEvent($event->id, Auth::id())) {
            return redirect()->route('my_events')
                ->with('error', 'You do not have permission to delete this event.');
        }

        $this->eventService->delete($event->id);

        return redirect()->route('my_events')
            ->with('success', 'Event deleted successfully');
    }
    public function getEventsForFeed(Request $request)
    {
        $query = Event::with(['user', 'photos', 'attendees']);

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date', '<=', $dateTo);
        }

        if ($request->boolean('has_ride_sharing')) {
            $query->where('has_ride_sharing', true);
        }

        if ($request->boolean('has_available_spots')) {
            $query->where(function($q) {
                $q->whereColumn('people_count', '>', function($subQuery) {
                    $subQuery->selectRaw('COALESCE(SUM(attendees_count), 0)')
                        ->from('event_attendees')
                        ->whereColumn('event_attendees.event_id', 'events.id')
                        ->where('status', 'accepted');
                });
            });
        }

        $events = $query->paginate(3);

        return [
            'events' => $events,
            'popularEvents' => [],
            'upcomingEvents' => Event::where('date', '>=', now())->orderBy('date')->limit(10)->get(),
        ];
    }
    public function feed(Request $request)
    {
        $eventData = $this->eventService->getEventsForFeed($request);

        return view('events.feed', [
            'events' => $eventData['events'],
            'popularEvents' => $eventData['popularEvents'],
            'upcomingEvents' => $eventData['upcomingEvents']
        ]);
    }
    public function Events_list()
    {
        $events = $this->eventService->getEventsForListing();
        $today = Carbon::now()->startOfDay();

        $upcomingEvents = Event::whereDate('date', '>=', $today)->orderBy('date', 'asc')->get();
        return view('events.event_list', compact('events', 'upcomingEvents'));
    }
    public function myEvents()
    {
        $events = $this->eventService->getUserEvents(Auth::id());
        return view('events.my_events', compact('events'));
    }
    public function allEvents()
    {
        $today = Carbon::now()->startOfDay();
        $events =  Event::whereDate('date', '>=', $today)->orderBy('date', 'asc')->paginate(12);
        return view('events.all_events', compact('events'));
    }

}
