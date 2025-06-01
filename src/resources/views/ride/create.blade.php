<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.title.ridesCreate') }}</title>
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
                <h2>{{ __('messages.title.ridesCreate') }}</h2>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('events.show', $event) }}" class="btn btn-gradient text-color_2">{{ __('messages.ridescreate.backToEvent') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card custom-card-bg shadow-sm text-color">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridescreate.rideDetails') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('rides.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">

                            <div class="mb-3">
                                <label for="vehicle_description" class="form-label">{{ __('messages.ridescreate.vehicleDescription') }}</label>
                                <input type="text" class="form-control" id="vehicle_description" name="vehicle_description" value="{{ old('vehicle_description') }}" placeholder="{{ __('messages.ridescreate.vehiclePlaceholder') }}" required>
                                @error('vehicle_description')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="available_seats" class="form-label">{{ __('messages.ridescreate.availableSeats') }}</label>
                                <input type="number" class="form-control" id="available_seats" name="available_seats" value="{{ old('available_seats', 1) }}" min="1" required>
                                @error('available_seats')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meeting_location_name" class="form-label">{{ __('messages.ridescreate.meetingLocation') }}</label>
                                <input type="text" class="form-control" id="meeting_location_name" name="meeting_location_name" value="{{ old('meeting_location_name') }}" placeholder="{{ __('messages.ridescreate.meetingLocationExample') }}" required>
                                @error('meeting_location_name')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 card shadow-sm" style="height: 400px; width: 100%;">
                                <div id="map" style="height: 100%; width: 100%;"></div>
                                <input type="hidden" id="meeting_latitude" name="meeting_latitude" value="{{ old('meeting_latitude', $event->latitude ?? 52.2297) }}" required>
                                <input type="hidden" id="meeting_longitude" name="meeting_longitude" value="{{ old('meeting_longitude', $event->longitude ?? 21.0122) }}" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-gradient text-color_2">{{ __('messages.ridescreate.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card-bg text-color shadow-sm">
                    <div class="card-header text-color_2 text-center">
                        <h4>{{ __('messages.ridescreate.eventInfo') }}</h4>
                    </div>
                    <div class="card-body">
                        <h5>{{ $event->title }}</h5>
                        <p class="mb-2"><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($event->date)->format('d.m.Y H:i') }}</p>
                        <p class="mb-2"><i class="bi bi-geo-alt"></i> {{ $event->location }}</p>

                        @if($event->description)
                            <div class="mt-3">
                                <h6>{{ __('messages.ridescreate.eventDescription') }}</h6>
                                <p>{{ Str::limit($event->description, 150) }}</p>
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
        const defaultLatitude = {{ old('meeting_latitude', $event->latitude ?? 52.2297) }};
        const defaultLongitude = {{ old('meeting_longitude', $event->longitude ?? 21.0122) }};
        const map = L.map('map').setView([defaultLatitude, defaultLongitude], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        document.getElementById('meeting_latitude').value = defaultLatitude;
        document.getElementById('meeting_longitude').value = defaultLongitude;

        const customIcon = L.icon({
            iconUrl: '/images/includes/location_11111111111.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
            shadowSize: [41, 41],
            shadowAnchor: [12, 41]
        });

        let marker = L.marker([defaultLatitude, defaultLongitude], {
            icon: customIcon,
            draggable: true
        }).addTo(map);

        function updateMarkerPosition(lat, lng) {
            document.getElementById('meeting_latitude').value = lat;
            document.getElementById('meeting_longitude').value = lng;
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 13);
        }

        marker.on('dragend', function(event) {
            const position = marker.getLatLng();
            updateMarkerPosition(position.lat, position.lng);

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}&accept-language=pl`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('meeting_location_name').value = data.display_name;
                    }
                })
                .catch(error => {
                    console.error('Reverse geocoding error:', error);
                });
        });

        let timeoutId = null;

        const locationInput = document.getElementById('meeting_location_name');

        locationInput.addEventListener('input', function() {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            timeoutId = setTimeout(function() {
                const query = locationInput.value.trim();

                if (query.length > 3) {
                    searchLocation(query);
                }
            }, 500);
        });
        function searchLocation(query) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&accept-language=pl&countrycodes=pl&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);

                        updateMarkerPosition(lat, lon);
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            updateMarkerPosition(lat, lng);

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=pl`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('meeting_location_name').value = data.display_name;
                    }
                })
                .catch(error => {
                    console.error('Reverse geocoding error:', error);
                });
        });

        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const lat = document.getElementById('meeting_latitude').value;
            const lng = document.getElementById('meeting_longitude').value;

            if (!lat || !lng || lat === '0' || lng === '0') {
                e.preventDefault();
                alert('Please select a location on the map');
                return false;
            }
        });

        window.addEventListener('resize', function() {
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        });
    });
</script>
</body>
</html>
