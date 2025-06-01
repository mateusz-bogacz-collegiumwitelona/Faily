<template>
    <div class="main-container">
        <h2 class="mb-4">{{ $t('addevent.addEvent') }}</h2>

        <form @submit.prevent="submitForm" enctype="multipart/form-data" id="event-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">{{ $t('addevent.eventTitle') }}</label>
                        <input type="text" class="form-control" id="title" v-model="formData.title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ $t('addevent.eventDesc') }}</label>
                        <textarea class="form-control" id="description" v-model="formData.description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">{{ $t('addevent.date') }}</label>
                        <input type="datetime-local" class="form-control" id="date" v-model="formData.date" required>
                    </div>

                    <div class="mb-3">
                        <label for="peopleCount" class="form-label">{{ $t('addevent.availablePersonNumber') }}</label>
                        <input type="number" class="form-control" id="peopleCount" v-model="formData.peopleCount" min="1" required>
                    </div>

                    <div class="mb-3">
                        <input type="file" class="d-none" id="eventPhotos" ref="photoInput" multiple @change="updateFileList">
                        <label for="eventPhotos" class="btn btn-gradient text-color_2 mt-2">
                            {{ $t('addevent.addPhotos') }}
                        </label>
                        <div id="fileList" class="mt-2 small text-color">
                            <div v-if="selectedFiles.length > 0">
                                {{ $t('addevent.fileSelection') }} {{ selectedFiles.length }}
                                <ul class="mt-1 ps-3">
                                    <li v-for="(file, index) in selectedFiles" :key="index">{{ file.name }}</li>
                                </ul>
                            </div>
                            <div v-else>{{ $t('addevent.fileNotChoosen') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="location_name" class="form-label">{{ $t('addevent.locationName') }}</label>
                        <input type="text" class="form-control" id="location_name" v-model="formData.locationName" :placeholder="$t('addevent.placeName')" required>
                    </div>

                    <div class="mb-3">
                        <label for="search-address" class="form-label">{{ $t('addevent.address') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-address" v-model="searchAddress" :placeholder="$t('addevent.addressExample')" required>
                            <button class="btn btn-outline-secondary" type="button" @click="searchLocation">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        <div class="address-suggestions" v-if="suggestions.length > 0">
                            <div class="address-suggestion" v-for="(suggestion, index) in suggestions" :key="index" @click="selectSuggestion(suggestion)">
                                {{ suggestion.display_name }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div ref="mapContainer" class="map-container"></div>
                        <div class="coordinate-display">
                            {{ $t('addevent.selectedLocation') }} <strong>{{ formData.latitude.toFixed(6) }}, {{ formData.longitude.toFixed(6) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="chimkowy-switch" type="checkbox" id="has_ride_sharing" v-model="formData.hasRideSharing">
                        <label class="form-check-label" for="has_ride_sharing">{{ $t('addevent.enableCarSharing') }}</label>
                    </div>
                </div>
            </div>

            <div v-if="formData.hasRideSharing" class="mt-4 mb-4 p-3">
                <h4 class="mb-3">{{ $t('addevent.rideDetails') }}</h4>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vehicle_description" class="form-label">{{ $t('addevent.carDesc') }}</label>
                            <input type="text" class="form-control" id="vehicle_description" v-model="formData.vehicleDescription" :placeholder="$t('addevent.carDescExample')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="available_seats" class="form-label">{{ $t('addevent.availableSeats') }}</label>
                            <input type="number" class="form-control" id="available_seats" v-model="formData.availableSeats" min="1">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="meeting_location_name" class="form-label">{{ $t('addevent.meetingPlace') }}</label>
                            <input type="text" class="form-control" id="meeting_location_name" v-model="formData.meetingLocationName" :placeholder="$t('addevent.meetingPlaceExample')">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="search-meeting-address" class="form-label">{{ $t('addevent.searchMeetingPlace') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search-meeting-address" v-model="searchMeetingAddress" :placeholder="$t('addevent.enterAddress')">
                                <button class="btn btn-outline-secondary" type="button" @click="searchMeetingLocation">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <div ref="meetingMapContainer" class="map-container"></div>
                            <div class="coordinate-display">
                                {{ $t('addevent.selectedMeetingLocation') }} <strong>{{ formData.meetingLatitude.toFixed(6) }}, {{ formData.meetingLongitude.toFixed(6) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="submit" class="btn btn-lg px-4 btn-gradient text-color_2" :disabled="isSubmitting">
                    <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i v-else class="bi bi-check-circle me-2"></i>{{ $t('addevent.addEvent') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { onMounted, onUpdated, ref, reactive, watch } from 'vue';
import L from 'leaflet';

export default {
    name: 'EventForm',
    props: {
        csrfToken: {
            type: String,
            required: true
        },
        storeRoute: {
            type: String,
            required: true
        }
    },
    setup(props) {
        const customIcon = L.icon({
            iconUrl: '/images/includes/location_11111111111.png',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        const formData = reactive({
            title: '',
            description: '',
            date: '',
            peopleCount: 1,
            latitude: 52.069,
            longitude: 19.480,
            locationName: '',
            hasRideSharing: false,

            vehicleDescription: '',
            availableSeats: 1,
            meetingLocationName: '',
            meetingLatitude: 52.069,
            meetingLongitude: 19.480
        });

        const photoInput = ref(null);
        const selectedFiles = ref([]);
        const mapContainer = ref(null);
        const meetingMapContainer = ref(null);
        const isSubmitting = ref(false);
        let map = null;
        let mapMarker = null;
        let meetingMap = null;
        let meetingMapMarker = null;

        const searchAddress = ref('');
        const searchMeetingAddress = ref('');
        const suggestions = ref([]);
        const meetingSuggestions = ref([]);
        const initMap = () => {
            if (!mapContainer.value) return;

            console.log('Initialization of the event map');
            if (!map) {
                map = L.map(mapContainer.value).setView([formData.latitude, formData.longitude], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                mapMarker = L.marker([formData.latitude, formData.longitude], {
                    draggable: true,
                    icon: customIcon
                }).addTo(map);

                mapMarker.on('dragend', () => {
                    const position = mapMarker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });

                map.on('click', (e) => {
                    mapMarker.setLatLng(e.latlng);
                    updateCoordinates(e.latlng.lat, e.latlng.lng);
                });

                setTimeout(() => {
                    map.invalidateSize();
                    console.log('Map redrawn');
                }, 500);
            }
        };
        const initMeetingMap = () => {
            if (!meetingMapContainer.value) return;

            console.log('Initialization of the meeting place map');
            if (!meetingMap) {
                meetingMap = L.map(meetingMapContainer.value).setView([formData.meetingLatitude, formData.meetingLongitude], 6);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(meetingMap);

                meetingMapMarker = L.marker([formData.meetingLatitude, formData.meetingLongitude], {
                    draggable: true,
                    icon: customIcon
                }).addTo(meetingMap);

                meetingMapMarker.on('dragend', () => {
                    const position = meetingMapMarker.getLatLng();
                    updateMeetingCoordinates(position.lat, position.lng);
                });

                meetingMap.on('click', (e) => {
                    meetingMapMarker.setLatLng(e.latlng);
                    updateMeetingCoordinates(e.latlng.lat, e.latlng.lng);
                });

                setTimeout(() => {
                    meetingMap.invalidateSize();
                    console.log('Map of the meeting place redrawn');
                }, 500);
            }
        };
        const updateCoordinates = async (lat, lng) => {
            formData.latitude = lat;
            formData.longitude = lng;
            const endpoints = [
                `/api/nominatim/reverse?lat=${lat}&lon=${lng}`,
                `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
            ];

            let data = null;
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint, {
                        headers: endpoint.includes('openstreetmap') ? {
                            'User-Agent': 'Faily'
                        } : {}
                    });

                    if (response.ok) {
                        data = await response.json();
                        break;
                    }
                } catch (err) {
                    console.warn(`Error while downloading data from the ${endpoint}:`, err);
                }
            }

            if (data && data.display_name) {
                formData.locationName = data.display_name.split(',').slice(0, 3).join(', ');
            } else {
                console.warn('Failed to retrieve geolocation data');
                formData.locationName = 'Unknown location';
            }
        };
        const updateMeetingCoordinates = async (lat, lng) => {
            formData.meetingLatitude = lat;
            formData.meetingLongitude = lng;
            try {
                const endpoints = [
                    `/api/nominatim/reverse?lat=${lat}&lon=${lng}`,
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
                ];

                let data = null;
                for (const endpoint of endpoints) {
                    try {
                        const response = await fetch(endpoint, {
                            headers: endpoint.includes('openstreetmap') ? {
                                'User-Agent': 'Faily'
                            } : {}
                        });

                        if (response.ok) {
                            data = await response.json();
                            break;
                        }
                    } catch (err) {
                        console.warn(`Error while downloading data from the ${endpoint}:`, err);
                    }
                }

                if (data && data.display_name) {
                    formData.meetingLocationName = data.display_name.split(',').slice(0, 3).join(', ');
                } else {
                    console.warn('Failed to retrieve geolocation data for the meeting place');
                    formData.meetingLocationName = 'Unknown location';
                }
            } catch (error) {
                console.error('Geocoding error for the meeting place', error);
                formData.meetingLocationName = 'Unknown location';
            }
        };
        const searchLocation = async () => {
            if (!searchAddress.value.trim()) return;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchAddress.value)}`);
                const data = await response.json();

                if (data && data.length > 0) {
                    const location = data[0];
                    const lat = parseFloat(location.lat);
                    const lng = parseFloat(location.lon);

                    map.setView([lat, lng], 16);
                    mapMarker.setLatLng([lat, lng]);
                    await updateCoordinates(lat, lng);
                    suggestions.value = [];
                } else {
                    alert('Location not found. Please try again.');
                }
            } catch (error) {
                console.error('Search error', error);
                alert('An error occurred while searching for a location. Please try again.');
            }
        };
        const searchMeetingLocation = async () => {
            if (!searchMeetingAddress.value.trim()) return;

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchMeetingAddress.value)}`);
                const data = await response.json();

                if (data && data.length > 0) {
                    const location = data[0];
                    const lat = parseFloat(location.lat);
                    const lng = parseFloat(location.lon);

                    meetingMap.setView([lat, lng], 16);
                    meetingMapMarker.setLatLng([lat, lng]);
                    await updateMeetingCoordinates(lat, lng);
                } else {
                    alert('Meeting place not found. Please try again.');
                }
            } catch (error) {
                console.error('Meeting place search error', error);
                alert('An error occurred while searching for a meeting place. Please try again.');
            }
        };
        const fetchSuggestions = async (query) => {
            if (query.length < 3) {
                suggestions.value = [];
                return;
            }

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
                const data = await response.json();
                suggestions.value = data || [];
            } catch (error) {
                console.error('Error downloading suggestions', error);
                suggestions.value = [];
            }
        };
        const selectSuggestion = async (suggestion) => {
            const lat = parseFloat(suggestion.lat);
            const lng = parseFloat(suggestion.lon);

            searchAddress.value = suggestion.display_name;
            map.setView([lat, lng], 16);
            mapMarker.setLatLng([lat, lng]);
            await updateCoordinates(lat, lng);
            suggestions.value = [];
        };
        const updateFileList = () => {
            selectedFiles.value = Array.from(photoInput.value.files);
        };
        const submitForm = async () => {
            try {
                isSubmitting.value = true;
                const formDataToSubmit = new FormData();
                formDataToSubmit.append('_token', props.csrfToken);
                formDataToSubmit.append('title', formData.title);
                formDataToSubmit.append('description', formData.description);
                formDataToSubmit.append('date', formData.date);
                formDataToSubmit.append('people_count', formData.peopleCount);
                formDataToSubmit.append('latitude', formData.latitude);
                formDataToSubmit.append('longitude', formData.longitude);
                formDataToSubmit.append('location_name', formData.locationName);
                formDataToSubmit.append('has_ride_sharing', formData.hasRideSharing ? 1 : 0);

                if (formData.hasRideSharing) {
                    formDataToSubmit.append('vehicle_description', formData.vehicleDescription);
                    formDataToSubmit.append('available_seats', formData.availableSeats);
                    formDataToSubmit.append('meeting_location_name', formData.meetingLocationName);
                    formDataToSubmit.append('meeting_latitude', formData.meetingLatitude);
                    formDataToSubmit.append('meeting_longitude', formData.meetingLongitude);
                }
                for (const file of selectedFiles.value) {
                    formDataToSubmit.append('photos[]', file);
                }
                console.log('Data sent:', {
                    title: formData.title,
                    description: formData.description,
                    date: formData.date,
                    people_count: formData.peopleCount,
                    latitude: formData.latitude,
                    longitude: formData.longitude,
                    location_name: formData.locationName,
                    has_ride_sharing: formData.hasRideSharing,
                    vehicle_description: formData.vehicleDescription,
                    available_seats: formData.availableSeats,
                    meeting_location_name: formData.meetingLocationName,
                    meeting_latitude: formData.meetingLatitude,
                    meeting_longitude: formData.meetingLongitude
                });
                const response = await fetch(props.storeRoute, {
                    method: 'POST',
                    body: formDataToSubmit,
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`Server error: ${response.status}. Content: ${errorText.substring(0, 200)}...`);
                }
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert('The event has been successfully created!');
                    }
                } else {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else {
                        const text = await response.text();
                        if (text.includes('redirect')) {
                            try {
                                const matches = text.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                                if (matches && matches[1]) {
                                    window.location.href = matches[1];
                                    return;
                                }
                            } catch (e) {
                                console.warn('Error when analysing the responses', e);
                            }
                        }
                        alert('The event has been successfully created!');
                    }
                }
            } catch (error) {
                console.error('Error during form submission:', error);
                alert('There was a problem when creating the event. Please try again.');
            } finally {
                isSubmitting.value = false;
            }
        };
        watch(searchAddress, (newVal) => {
            fetchSuggestions(newVal);
        });
        watch(() => formData.hasRideSharing, (newVal) => {
            if (newVal) {
                setTimeout(() => {
                    initMeetingMap();
                }, 300);
            }
        });
        onMounted(() => {
            setTimeout(() => {
                initMap();
                if (formData.hasRideSharing) {
                    initMeetingMap();
                }
            }, 300);
        });

        onUpdated(() => {
            if (map) map.invalidateSize();
            if (meetingMap) meetingMap.invalidateSize();
        });

        return {
            formData,
            photoInput,
            selectedFiles,
            mapContainer,
            meetingMapContainer,
            searchAddress,
            searchMeetingAddress,
            suggestions,
            isSubmitting,
            updateFileList,
            searchLocation,
            searchMeetingLocation,
            selectSuggestion,
            submitForm
        };
    }
};
</script>

<style scoped>
.map-container {
    height: 300px;
    width: 100%;
    margin-bottom: 10px;
}

.address-suggestions {
    position: absolute;
    z-index: 1000;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.address-suggestion {
    padding: 8px 12px;
    cursor: pointer;
}

.address-suggestion:hover {
    background-color: #f0f0f0;
}

.coordinate-display {
    font-size: 0.9rem;
    margin-top: 5px;
}
</style>
