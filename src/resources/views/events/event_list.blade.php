<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.events') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">
@include('includes.navbar')

<main class="container py-5 text-color fade-in-up">
    <div class="row g-4">
        @include('includes.filters_menu')

        <div class="col-md-6">
            <h2 class="mb-4 text-color_2 text-center fw-bold">
                {{ __('messages.eventlist.newsSectionTitle') }}
            </h2>

        @forelse($events as $event)
                <div class="card shadow-sm mb-4 text-color lift-card">
                    <div class="card-header d-flex align-items-center gap-3">
                        <a href="{{ route('user.profile', $event->user) }}" class="text-decoration-none">
                            @if($event->user->photo_path)
                                <img src="{{ asset('storage/' . $event->user->photo_path) }}"
                                     class="rounded-circle border border-2 border-light hover-lift"
                                     alt="{{ __('messages.eventlist.profilePhotoLabel') }}"
                                     width="50" height="50"
                                     style="object-fit: cover; transition: transform 0.2s ease;">
                            @else
                                <img src="{{ asset('images/includes/default-avatar.png') }}"
                                     class="rounded-circle border border-2 border-light hover-lift"
                                     alt="{{ __('messages.eventlist.profilePhotoLabel') }}"
                                     width="50" height="50"
                                     style="object-fit: cover; transition: transform 0.2s ease;">
                            @endif
                        </a>
                        <div>
                            <a href="{{ route('user.profile', $event->user) }}" class="text-decoration-none">
                                <h6 class="mb-1 fw-bold text-color_2 hover-underline">{{ $event->user->name }}</h6>
                            </a>
                            <small class="text-light">{{ $event->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($event->photos->count())
                        <a href="{{ route('events.show', $event) }}">
                            <img src="{{ asset('storage/' . $event->photos->first()->path) }}"
                                 class="card-img-top"
                                 alt="{{ $event->title }}">
                        </a>
                    @endif

                    <div class="card-body d-flex flex-column gap-3">
                        <div>
                            <h5 class="card-title mb-2">{{ $event->title }}</h5>
                            <p class="mb-1">
                                <i class="bi bi-geo-alt"></i>{{ __('messages.eventlist.locationLabel') }} {{ $event->location_name }}
                            </p>
                            <p class="mb-3">
                                <i class="bi bi-calendar"></i>{{ __('messages.eventlist.dateLabel') }} {{ \Carbon\Carbon::parse($event->date)->format('d.m.Y H:i') }}
                            </p>
                            <p class="text-color">{{ __('messages.eventlist.eventDescriptionLabel') }}</p>
                            <p class="text-color">{{ Str::limit($event->description, 150) }}</p>
                        </div>

                        @php
                            $totalAttendees = $event->acceptedAttendees()->sum('attendees_count');
                            $availableSpots = max(0, $event->people_count - $totalAttendees);
                        @endphp

                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-info">{{ $totalAttendees }}/{{ $event->people_count }} {{ __('messages.eventlist.participantsCountLabel') }}</span>
                            @if($event->has_ride_sharing)
                                <span class="badge bg-success">{{ __('messages.eventlist.statusWithTransport') }}</span>
                            @endif
                            @if($availableSpots > 0)
                                <span class="badge bg-warning">{{ $availableSpots }} {{ __('messages.eventlist.statusFreeSpots') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('messages.eventlist.statusNoSpots') }}</span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('events.show', $event) }}"
                               class="btn btn-gradient text-color_2">
                                {{ __('messages.eventlist.detailsLabel') }}
                            </a>

                            @auth
                                @php
                                    $attendee = $event->attendees()->where('user_id', Auth::id())->first();
                                @endphp
                                @if(!$attendee && $availableSpots > 0 && Auth::id() !== $event->user_id)
                                    <a href="{{ route('events.attendees.create', $event) }}"
                                       class="btn btn-success"
                                       style="border-radius: 20px; padding: 5px 20px;">
                                        {{ __('messages.eventlist.signUpButton') }}
                                    </a>
                                @elseif($attendee)
                                    @if($attendee->status == 'pending')
                                        <span class="badge bg-warning align-self-center">{{ __('messages.eventlist.statusPending') }}</span>
                                    @elseif($attendee->status == 'accepted')
                                        <span class="badge bg-success align-self-center">{{ __('messages.eventlist.statusConfirmed') }}</span>
                                    @elseif($attendee->status == 'rejected')
                                        <span class="badge bg-danger align-self-center">{{ __('messages.eventlist.statusRejected') }}</span>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    {{ __('messages.eventlist.noEventsMessage') }}<br>
                    <a href="{{ route('events.create') }}" class="btn btn-gradient mt-2">{{ __('messages.eventlist.addFirstEventButton') }}</a>
                </div>
            @endforelse
        </div>
        @include('includes.upcoming_events')

    </div>
    <div class="d-flex flex-column align-items-center mt-4">
        <div class="mb-2 text-color">
            {{ $events->links('pagination::bootstrap-5') }}
        </div>
    </div>


</main>

@include('includes.footer')
</body>
</html>
