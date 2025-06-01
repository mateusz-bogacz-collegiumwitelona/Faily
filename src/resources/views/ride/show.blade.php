<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.rideDetails') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-main">
@include('includes.navbar')
<main>
    <div class="container mt-5 text-color_2">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>{{ __('messages.title.rideDetails') }}</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('events.show', $ride->event) }}"
                   class="btn btn-gradient text-color_2">{{ __('messages.ridesshow.backToEvent') }}</a>
                @if(auth()->id() === $ride->driver_id)
                    <a href="{{ route('rides.edit', $ride) }}"
                       class="btn btn-gradient text-color_2 ms-2">{{ __('messages.ridesshow.editRide') }}</a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card custom-card-bg shadow-sm text-color">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridesshow.rideInfo') }}</h4>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center mt-2">
                            <a href="{{ route('user.profile', $ride->driver) }}" class="text-decoration-none">
                                @if($ride->driver->photo_path)
                                    <img src="{{ Storage::url($ride->driver->photo_path) }}"
                                         class="rounded-circle me-3 border border-2 border-light hover-lift"
                                         alt="{{ __('messages.showevent.profilePhoto') }}"
                                         width="150" height="150"
                                         style="object-fit: cover; transition: transform 0.2s ease;">
                                @else
                                    <img src="{{ asset('images/includes/default-avatar.png') }}"
                                         class="rounded-circle me-3 border border-2 border-light hover-lift"
                                         alt="{{ __('messages.showevent.profilePhoto') }}"
                                         width="150" height="150"
                                         style="object-fit: cover; transition: transform 0.2s ease;">
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('user.profile', $ride->driver) }}" class="text-decoration-none">
                                    <h5 class="mb-0 text-color_2 hover-underline">{{ __('messages.ridesshow.driver') }} {{ $ride->driver->name }}</h5>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <strong class="form-label">{{ __('messages.ridesshow.vehicle') }}</strong>
                            <p class="text-color">{{ $ride->vehicle_description }}</p>
                        </div>

                        <div class="mb-3">
                            <strong class="form-label">{{ __('messages.ridesshow.availableSeats') }}</strong>
                            @php
                                $takenSeats = $ride->requests()->where('status', 'accepted')->count();
                                $availableSeats = max(0, $ride->available_seats - $takenSeats);
                            @endphp
                            <p class="text-color">{{ $availableSeats }} / {{ $ride->available_seats }}</p>
                        </div>
                        <div class="mb-3">
                            <strong class="form-label">{{ __('messages.ridesshow.meetingLocation') }}</strong>
                            <p class="text-color">{{ $ride->meeting_location_name }}</p>
                        </div>

                        <div class="mb-4 card shadow-sm align-content-center" style="height: 400px; width: 100%;">
                            <div id="map" style="height: 90%; width: 96%;"></div>
                        </div>

                        @if(auth()->id() !== $ride->driver_id && $availableSeats > 0)
                            <div class="d-grid gap-2">
                                <form action="{{ route('rides.request', $ride) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="btn text-color btn-gradient">{{ __('messages.ridesshow.requestSeat') }}</button>
                                </form>
                            </div>
                        @elseif(auth()->id() !== $ride->driver_id && $availableSeats === 0)
                            <div class="alert alert-warning text-center">
                                {{ __('messages.ridesshow.noSeatsAvailable') }}
                            </div>
                        @endif

                    </div>

                </div>

            </div>

            <div class="col-md-4">
                <div class="card custom-card-bg text-color shadow-sm">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridesshow.eventInfo') }}</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('messages.ridesshow.eventName') }}</strong> {{ $ride->event->title }}</p>
                        <p>
                            <strong>{{ __('messages.ridesshow.eventDate') }}</strong> {{ \Carbon\Carbon::parse($ride->event->date)->format('d.m.Y H:i') }}
                        </p>
                        <p>
                            <strong>{{ __('messages.ridesshow.eventLocation') }}</strong> {{ $ride->event->location_name }}
                        </p>

                        @if($ride->event->description)
                            <div class="mt-3">
                                <strong>{{ __('messages.ridesshow.eventDescription') }}</strong>
                                <p class="text-color mt-1">{{ Str::limit($ride->event->description, 150) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@include('includes.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const latitude = {{ $ride->meeting_latitude ?? 52.2297 }};
        const longitude = {{ $ride->meeting_longitude ?? 21.0122 }};

        const map = L.map('map').setView([latitude, longitude], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const customIcon = L.icon({
            iconUrl: '/images/includes/location_11111111111.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });
        const marker = L.marker([latitude, longitude], {
            icon: customIcon,
            draggable: false
        }).addTo(map);
        marker.bindPopup(`
            <div class="text-center">
                <strong>{{ __('messages.ridesshow.meetingLocation') }}</strong><br>
                {{ $ride->meeting_location_name }}
        </div>
 `, {
            className: 'dark-popup'
        });

        @if(isset($ride->event->latitude) && isset($ride->event->longitude) && ($ride->event->latitude != $ride->meeting_latitude || $ride->event->longitude != $ride->meeting_longitude))
        const eventMarker = L.marker([{{ $ride->event->latitude }}, {{ $ride->event->longitude }}], {
            icon: customIcon
        }).addTo(map);

        eventMarker.bindPopup(`
                <div class="text-center">
                    <strong>{{ __('messages.ridesshow.eventLocation') }}</strong><br>
                    {{ $ride->event->location_name }}
        </div>
`, {
            className: 'dark-popup'
        });
        const group = new L.featureGroup([marker, eventMarker]);
        map.fitBounds(group.getBounds().pad(0.1));
        @endif
    });
</script>
</body>
</html>
