<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.ridesEdit') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #map {
            height: 100% !important;
            width: 100% !important;
            border-radius: 0.5rem;
            position: relative;
            z-index: 1;
        }
        .map-container {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            background: #f8f9fa;
        }
        .leaflet-control-attribution {
            max-width: 100% !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            #map {
                height: 300px !important;
            }
        }
    </style>
</head>
<body class="bg-main">
@include('includes.navbar')
<main>
    <div class="container mt-5 text-color_2">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>{{ __('messages.title.ridesEdit') }}</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('events.show', $ride->event) }}" class="btn btn-gradient text-color_2">{{ __('messages.ridesedit.backToEvent') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card custom-card-bg shadow-sm text-color">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridesedit.updateRide') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('rides.update', $ride) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="vehicle_description" class="form-label">{{ __('messages.ridesedit.vehicleDescription') }}</label>
                                <input type="text" class="form-control" id="vehicle_description" name="vehicle_description" value="{{ old('vehicle_description', $ride->vehicle_description) }}" required>
                                @error('vehicle_description')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="available_seats" class="form-label">{{ __('messages.ridesedit.availableSeats') }}</label>
                                <input type="number" class="form-control" id="available_seats" name="available_seats" value="{{ old('available_seats', $ride->available_seats) }}" min="1" required>
                                @error('available_seats')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meeting_location_name" class="form-label">{{ __('messages.ridesedit.meetingLocation') }}</label>
                                <input type="text" class="form-control" id="meeting_location_name" name="meeting_location_name" value="{{ old('meeting_location_name', $ride->meeting_location_name) }}" required>
                                @error('meeting_location_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 card shadow-sm align-content-center" style="height: 400px; width: 100%;">
                                <div id="map"></div>
                                <input type="hidden" id="meeting_latitude" name="meeting_latitude" value="{{ $ride->meeting_latitude ?? 52.2297 }}" required>
                                <input type="hidden" id="meeting_longitude" name="meeting_longitude" value="{{ $ride->meeting_longitude ?? 21.0122 }}" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn text-color_2 btn-gradient">{{ __('messages.ridesedit.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card-bg text-color shadow-sm">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridesedit.eventInfo') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $ride->driver->avatar }}" class="rounded-circle me-3" width="80" alt="{{ $ride->driver->name }}">
                            <div>
                                <h5 class="mb-0">{{ $ride->driver->name }}</h5>
                            </div>
                        </div>

                        <p><strong>{{ __('messages.ridesedit.eventName') }}</strong> {{ $ride->event->title }}</p>
                        <p><strong>{{ __('messages.ridesedit.eventDate') }}</strong> {{ \Carbon\Carbon::parse($ride->event->date)->format('d.m.Y H:i') }}</p>

                        @php
                            $takenSeats = $ride->requests()->where('status', 'accepted')->count();
                            $availableSeats = max(0, $ride->available_seats - $takenSeats);
                        @endphp
                        <p><strong>{{ __('messages.ridesedit.currentSeats') }}</strong> {{ $availableSeats }} / {{ $ride->available_seats }}</p>
                        <p><strong>{{ __('messages.ridesedit.acceptedPassengers') }}</strong> {{ $takenSeats }}</p>

                        @if($takenSeats > 0)
                            <div class="alert alert-info">
                                {{ __('messages.ridesedit.acceptedPassengersWarning') }}
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
    document.addEventListener('DOMContentLoaded', function() {
        const defaultLatitude = {{ $ride->meeting_latitude ?? 52.2297 }};
        const defaultLongitude = {{ $ride->meeting_longitude ?? 21.0122 }};

        document.getElementById('meeting_latitude').value = defaultLatitude;
        document.getElementById('meeting_longitude').value = defaultLongitude;

        const map = L.map('map').setView([defaultLatitude, defaultLongitude], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const customIcon = L.icon({
            iconUrl: '/images/includes/location_11111111111.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        let marker = L.marker([defaultLatitude, defaultLongitude], {
            icon: customIcon,
            draggable: true
        }).addTo(map);

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        marker.on('dragend', function(event) {
            const position = marker.getLatLng();
            document.getElementById('meeting_latitude').value = position.lat;
            document.getElementById('meeting_longitude').value = position.lng;
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('meeting_location_name').value = data.display_name;
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        const locationInput = document.getElementById('meeting_location_name');

        const debouncedSearch = debounce(function(query) {
            if (query.length > 3) {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);

                            document.getElementById('meeting_latitude').value = lat;
                            document.getElementById('meeting_longitude').value = lon;

                            marker.setLatLng([lat, lon]);
                            map.setView([lat, lon], 13);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }, 500);

        locationInput.addEventListener('input', function() {
            debouncedSearch(this.value);
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            const latitude = document.getElementById('meeting_latitude').value;
            const longitude = document.getElementById('meeting_longitude').value;

            if (!latitude || !longitude || latitude === '0' || longitude === '0') {
                e.preventDefault();
                alert('Please select the meeting place on the map or enter the address.');
                return false;
            }
        });
    });
</script>
</body>
</html>
