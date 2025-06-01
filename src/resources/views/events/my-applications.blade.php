<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.myApplications') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">

@include('includes.navbar')

<main class="container mt-5 mb-5">
    <h1 class="fw-bold mb-4 text-color_2">{{ __('messages.myapplications.myApplications') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <ul class="nav nav-tabs mb-4 nav-dark" id="applicationTabs" role="tablist" style="border-bottom: 1px solid #495057;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active bg-dark text-light border-secondary" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="true" style="border-color: #495057 !important;">
                <i class="bi bi-calendar-event"></i> {{ __('messages.myapplications.eventApplications') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link bg-secondary text-light border-secondary" id="rides-tab" data-bs-toggle="tab" data-bs-target="#rides" type="button" role="tab" aria-controls="rides" aria-selected="false" style="border-color: #495057 !important;">
                <i class="bi bi-car-front"></i> {{ __('messages.myapplications.rideApplications') }}
            </button>
        </li>
    </ul>

    <div class="tab-content" id="applicationTabsContent">
        <div class="tab-pane fade show active" id="events" role="tabpanel" aria-labelledby="events-tab">
            <div class="row">
                @forelse ($eventAttendees as $attendee)
                    <div class="col-md-6 mb-4 fade-in-up">
                        <div class="card h-100 shadow-sm bg-dark text-light border-secondary lift-card">
                            <a href="{{ route('events.show', $attendee->event->id) }}">
                                @if(isset($attendee->event->photos) && count($attendee->event->photos) > 0)
                                    <img src="{{ asset('storage/' . $attendee->event->photos[0]->path) }}"
                                         alt="{{ $attendee->event->title }}"
                                         class="card-img-top"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/includes/brak_zdjecia.jpg') }}"
                                         alt="Brak zdjęcia"
                                         class="card-img-top w-100"
                                         style="height: 200px; object-fit: cover;">
                                @endif
                            </a>
                            <div class="card-body">
                                <h5 class="card-title text-light">{{ $attendee->event->title }}</h5>
                                <p class="text-light"><strong>{{ __('messages.myapplications.date') }}</strong> {{ \Carbon\Carbon::parse($attendee->event->date)->format('d.m.Y H:i') }}</p>
                                <p class="text-light"><strong>{{ __('messages.myapplications.location') }}</strong> {{ $attendee->event->location_name }}</p>
                                <p class="text-light"><strong>{{ __('messages.myapplications.organizer') }}</strong> {{ $attendee->event->user->name }}</p>

                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @if($attendee->status === 'accepted')
                                        <span class="badge bg-success fs-6">
                                            <i class="bi bi-check-circle"></i> {{ __('messages.myapplications.accepted') }}
                                        </span>
                                    @elseif($attendee->status === 'rejected')
                                        <span class="badge bg-danger fs-6">
                                            <i class="bi bi-x-circle"></i> {{ __('messages.myapplications.rejected') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6">
                                            <i class="bi bi-clock"></i> {{ __('messages.myapplications.pending') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-light"><strong>{{ __('messages.myapplications.attendeesCount') }}</strong> {{ $attendee->attendees_count }}</p>

                                @if($attendee->message)
                                    <p class="text-light"><strong>{{ __('messages.myapplications.message') }}</strong></p>
                                    <p class="small text-white">{{ Str::limit($attendee->message, 100) }}</p>
                                @endif

                                <p class="small text-white">{{ __('messages.myapplications.appliedOn') }} {{ $attendee->created_at->format('d.m.Y H:i') }}</p>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('events.show', $attendee->event->id) }}" class="btn btn-outline-light">
                                        {{ __('messages.myapplications.viewEvent') }}
                                    </a>
                                    @if($attendee->status === 'pending')
                                        <form action="{{ route('events.attendees.destroy', [$attendee->event, $attendee]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                    onclick="return confirm('{{ __('messages.myapplications.cancelConfirm') }}')">
                                                {{ __('messages.myapplications.cancelApplication') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-secondary text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('messages.myapplications.noEventApplications') }}
                        </div>
                    </div>
                @endforelse
            </div>

            @if($eventAttendees && $eventAttendees->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $eventAttendees->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Ride Applications Tab -->
        <div class="tab-pane fade" id="rides" role="tabpanel" aria-labelledby="rides-tab">
            <div class="row">
                @if($rideRequests && $rideRequests->count() > 0)
                    @foreach ($rideRequests as $request)
                        <div class="col-md-6 mb-4 fade-in-up">
                            <div class="card h-100 shadow-sm bg-dark text-light border-secondary lift-card">
                                <a href="{{ route('events.show', $request->ride->event->id) }}">
                                    @if(isset($request->ride->event->photos) && count($request->ride->event->photos) > 0)
                                        <img src="{{ asset('storage/' . $request->ride->event->photos[0]->path) }}"
                                             alt="{{ $request->ride->event->title }}"
                                             class="card-img-top"
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/includes/brak_zdjecia.jpg') }}"
                                             alt="Brak zdjęcia"
                                             class="card-img-top w-100"
                                             style="height: 200px; object-fit: cover;">
                                    @endif
                                </a>
                                <div class="card-body">
                                    <h5 class="card-title text-light">{{ $request->ride->event->title }}</h5>
                                    <p class="text-light"><strong>{{ __('messages.myapplications.date') }}</strong> {{ \Carbon\Carbon::parse($request->ride->event->date)->format('d.m.Y H:i') }}</p>
                                    <p class="text-light"><strong>{{ __('messages.myapplications.driver') }}</strong> {{ $request->ride->driver->name }}</p>
                                    <p class="text-light"><strong>{{ __('messages.myapplications.vehicle') }}</strong> {{ $request->ride->vehicle_description }}</p>
                                    <p class="text-light"><strong>{{ __('messages.myapplications.meetingPoint') }}</strong> {{ $request->ride->meeting_location_name }}</p>

                                    <!-- Status Badge -->
                                    <div class="mb-3">
                                        @if($request->status === 'accepted')
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-check-circle"></i> {{ __('messages.myapplications.accepted') }}
                                            </span>
                                        @elseif($request->status === 'rejected')
                                            <span class="badge bg-danger fs-6">
                                                <i class="bi bi-x-circle"></i> {{ __('messages.myapplications.rejected') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark fs-6">
                                                <i class="bi bi-clock"></i> {{ __('messages.myapplications.pending') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($request->message)
                                        <p class="text-light"><strong>{{ __('messages.myapplications.message') }}</strong></p>
                                        <p class="small text-white">{{ Str::limit($request->message, 100) }}</p>
                                    @endif

                                    <p class="small text-white">{{ __('messages.myapplications.appliedOn') }} {{ $request->created_at->format('d.m.Y H:i') }}</p>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('events.show', $request->ride->event->id) }}" class="btn btn-outline-light">
                                            {{ __('messages.myapplications.viewEvent') }}
                                        </a>
                                        @if($request->status === 'pending')
                                            <form action="{{ route('ride-requests.destroy', $request) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                        onclick="return confirm('{{ __('messages.myapplications.cancelConfirm') }}')">
                                                    {{ __('messages.myapplications.cancelApplication') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-secondary text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('messages.myapplications.noRideApplications') }}
                        </div>
                    </div>
                @endif
            </div>

            @if($rideRequests && method_exists($rideRequests, 'hasPages') && $rideRequests->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $rideRequests->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</main>

@include('includes.footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const triggerTabList = [].slice.call(document.querySelectorAll('#applicationTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()

                document.querySelectorAll('#applicationTabs button').forEach(btn => {
                    btn.classList.remove('active', 'bg-dark')
                    btn.classList.add('bg-secondary')
                })
                this.classList.remove('bg-secondary')
                this.classList.add('active', 'bg-dark')
            })
        })

        const hash = window.location.hash
        if (hash === '#rides') {
            const ridesTab = document.querySelector('#rides-tab')
            if (ridesTab) {
                const tabTrigger = new bootstrap.Tab(ridesTab)
                tabTrigger.show()

                document.querySelectorAll('#applicationTabs button').forEach(btn => {
                    btn.classList.remove('active', 'bg-dark')
                    btn.classList.add('bg-secondary')
                })
                ridesTab.classList.remove('bg-secondary')
                ridesTab.classList.add('active', 'bg-dark')
            }
        }
    });</script>

</body>
</html>
