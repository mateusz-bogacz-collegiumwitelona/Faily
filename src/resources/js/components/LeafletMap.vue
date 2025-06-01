<template>
    <div class="map-container">
        <div id="map" ref="mapContainer"></div>

        <div class="search-container">
            <div class="input-group">
                <input
                    type="text"
                    class="form-control"
                    v-model="searchQuery"
                    :placeholder="$t('map.searchPlace')"
                    @keyup.enter="searchLocation"
                    @input="handleSearchInput"
                >
                <button
                    class="btn btn-outline-light"
                    type="button"
                    @click="searchLocation"
                >
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div class="search-results" v-if="showSearchResults" ref="searchResultsContainer">
                <div
                    v-for="(result, index) in searchResults"
                    :key="index"
                    class="search-result-item"
                    @click="selectSearchResult(result)"
                >
                    {{ result.display_name }}
                </div>
                <div class="search-result-item" v-if="searchResults.length === 0 && hasSearched">
                    {{ $t('map.noResults') }}
                </div>
            </div>
        </div>

        <div class="map-controls border-dark">
            <button
                class="btn btn-gradient text-white border-dark"
                @click="locateMe"
                :disabled="isLocating"
            >
                <i class="bi bi-geo-alt"></i>
                {{ isLocating ? $t('map.locating') : $t('map.myLocation') }}
            </button>
        </div>

        <div class="map-info text-white">
            <h5>{{ $t('map.mapInfo') }}</h5>
            <p>{{ $t('map.clickMapInstruction') }}</p>
            <div v-html="coordinatesInfo"></div>
        </div>
    </div>
</template>

<script>
import L from 'leaflet';
import '@/css/app.css';
export default {
    name: 'LeafletMap',

    props: {
        initialEvents: {
            type: Array,
            default: () => []
        }
    },

    data() {
        return {
            map: null,
            currentMarker: null,
            customIcon: null,
            customIcon2: null,
            events: [],
            searchQuery: '',
            searchResults: [],
            showSearchResults: false,
            hasSearched: false,
            coordinatesInfo: '',
            isLocating: false
        };
    },

    mounted() {
        this.events = this.initialEvents;

        this.initializeMap();
        this.initializeCustomIcon();
        this.initializeCustomIcon2();
        this.addEventMarkers();

        setTimeout(() => {
            if (this.map) this.map.invalidateSize();
        }, 300);

        document.addEventListener('click', this.handleOutsideClick);
        console.log(this.initialEvents)
    },

    beforeUnmount() {
        document.removeEventListener('click', this.handleOutsideClick);

        if (this.map) {
            this.map.remove();
            this.map = null;
        }
    },

    methods: {
        initializeMap() {
            this.map = L.map(this.$refs.mapContainer).setView([52.069, 19.480], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                minZoom: 5,
                maxZoom:  15,
                attribution: '&copy; OpenStreetMap'
            }).addTo(this.map);
            this.map.on('click', this.handleMapClick);
        },

        initializeCustomIcon() {
            this.customIcon = L.icon({
                iconUrl: '/images/includes/location_11111111111.png',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });
        },
        addMarker(lat, lng, popupContent) {
            if (this.currentMarker) {
                this.map.removeLayer(this.currentMarker);
            }

            this.currentMarker = L.marker([lat, lng], {
                draggable: true,
                icon: this.customIcon
            }).addTo(this.map);

            if (popupContent) {
                this.currentMarker.bindPopup(popupContent).openPopup();
            }

            this.currentMarker.on('dragend', this.handleMarkerDragEnd);
            this.updateCoordinatesInfo(lat, lng);
        },

        handleMapClick(e) {
            const {lat, lng} = e.latlng;
            this.addMarker(lat, lng);
            this.reverseGeocode(lat, lng);
        },

        handleMarkerDragEnd() {
            const pos = this.currentMarker.getLatLng();
            this.updateCoordinatesInfo(pos.lat, pos.lng);
            this.reverseGeocode(pos.lat, pos.lng);
        },

        updateCoordinatesInfo(lat, lng) {
            this.coordinatesInfo = `
                <strong>${this.$t('map.latitude')}</strong> ${lat.toFixed(6)}<br>
                <strong>${this.$t('map.longitude')}</strong> ${lng.toFixed(6)}
            `;
        },

        async reverseGeocode(lat, lng) {
            try {
                const endpoints = [
                    `/api/nominatim/reverse?lat=${lat}&lon=${lng}`,
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`
                ];

                let data = null;

                for (const endpoint of endpoints) {
                    try {
                        const response = await fetch(endpoint, {
                            headers: endpoint.includes('openstreetmap') ?
                                {'User-Agent': 'YourAppName'} : {}
                        });

                        if (response.ok) {
                            data = await response.json();
                            break;
                        }
                    } catch (err) {
                        console.warn(`Failed with endpoint ${endpoint}:`, err);
                    }
                }

                if (data && data.display_name && this.currentMarker) {
                    this.currentMarker.bindPopup(`
            <div class="event-popup">
                <h6><strong>${data.display_name}</strong></h6>
                <p class="mb-1">
                    <i class="bi bi-geo-alt"></i> <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small>
                </p>
            </div>
                    `, {
                        className: 'dark-popup'
                    }).openPopup();
                } else {
                    console.warn('No valid data returned from geocoding services');
                    this.currentMarker.bindPopup(`
                        <strong>Location</strong><br>
                        <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small>
                    `).openPopup();
                }
            } catch (error) {
                console.error('Error during reverse geocoding:', error);
                if (this.currentMarker) {
                    this.currentMarker.bindPopup(`
                        <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small>
                    `).openPopup();
                }
            }
        },

        addEventMarkers() {
            if (!this.events || !this.events.length || !this.map) return;

            this.events.forEach(event => {
                console.log('Event data:', event)

                const lat = parseFloat(event.latitude);
                const lng = parseFloat(event.longitude);
                const marker = L.marker([lat, lng], {icon: this.customIcon}).addTo(this.map);
                let formattedDate = 'Date unavailable';

                if (event.date) {
                    try {
                        const eventDate = new Date(event.date);

                        if (!isNaN(eventDate.getTime())) {
                            formattedDate = eventDate.toLocaleDateString('pl-PL', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    } catch (error) {
                        console.warn('Error parsing date for event:', event.title, error);
                    }
                }
                const eventUrl = event.id ? `/events/${event.id}` : '#';
                const eventButtonText = this.$t('map.eventButton');
                marker.bindPopup(`
            <div class="event-popup">
                <h6><strong>${event.title}</strong></h6>
                <p class="mb-1">
                    <i class="bi bi-geo-alt"></i> ${event.location_name}
                </p>
                <p class="mb-2">
                    <i class="bi bi-calendar"></i> ${formattedDate}
                </p>
                <a href="${eventUrl}" class="btn btn-gradient btn-sm" target="_blank">
                   ${eventButtonText}
                </a>
            </div>
        `, {
                    className: 'dark-popup'
                });
            });
        },

        async locateMe() {
            if (!('geolocation' in navigator)) {
                alert(this.$t('map.alertBrowserGeolocalization'));
                return;
            }

            this.isLocating = true;

            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject);
                });

                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                this.map.setView([lat, lng], 16);
                this.addMarker(lat, lng, this.$t('map.yourLocation'));
                await this.reverseGeocode(lat, lng);
            } catch (error) {
                alert(this.$t('map.alertGeolocalization'));
                console.error('Geolocation error:', error);
            } finally {
                this.isLocating = false;
            }
        },

        async searchLocation() {
            if (!this.searchQuery.trim()) return;

            try {
                const endpoints = [
                    `/api/nominatim/search?q=${encodeURIComponent(this.searchQuery)}&limit=5`,
                    `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(this.searchQuery)}&format=json&limit=5`
                ];

                let data = null;

                for (const endpoint of endpoints) {
                    try {
                        const response = await fetch(endpoint, {
                            headers: endpoint.includes('openstreetmap') ?
                                {'User-Agent': 'YourAppName'} : {}
                        });

                        if (response.ok) {
                            data = await response.json();
                            break;
                        }
                    } catch (err) {
                        console.warn(`Failed with endpoint ${endpoint}:`, err);
                    }
                }

                this.searchResults = Array.isArray(data) ? [...data] : [];
                this.showSearchResults = true;
                this.hasSearched = true;

                if (this.searchResults.length > 0) {
                    console.log(`Found ${this.searchResults.length} results`);
                    this.$nextTick(() => {
                        const searchResultsElement = this.$el.querySelector('.search-results');
                        if (searchResultsElement) {
                            searchResultsElement.style.display = 'block';
                        }
                    });
                }
                console.log('Search results:', JSON.parse(JSON.stringify(this.searchResults)));

            } catch (error) {
                console.error('Error during location search:', error);
                this.searchResults = [];
                this.hasSearched = true;
                this.showSearchResults = true;
            }
        },

        handleSearchInput() {
            if (this.searchQuery.length >= 3) {
                this.searchLocation();
            } else {
                this.showSearchResults = false;
            }
        },

        async selectSearchResult(result) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);

            this.map.setView([lat, lng], 16);
            this.addMarker(lat, lng, result.display_name);
            this.showSearchResults = false;
            this.searchQuery = result.display_name;
            await this.reverseGeocode(lat, lng);
        },

        handleOutsideClick(event) {
            const searchContainer = this.$el.querySelector('.search-container');
            if (searchContainer && !searchContainer.contains(event.target)) {
                this.showSearchResults = false;
            }
        }
    }
};
</script>

<style scoped>
.map-container {
    flex: 1;
    width: 100%;
    height: 100%;
    position: relative;
    z-index: 1;
}
#map {
    width: 100%;
    height: 100%;
    position: absolute;
}
.event-popup {
    min-width: 200px;
    font-family: inherit;
}
.event-popup h6 {
    margin-bottom: 8px;
    color: #333;
}
.event-popup p {
    margin-bottom: 4px;
    font-size: 14px;
    color: #666;
}
.event-popup .btn {
    font-size: 12px;
    padding: 4px 8px;
    text-decoration: none;
}
.event-popup i {
    margin-right: 4px;
    width: 14px;
}
@media (max-width: 768px) {
    .search-container { width: calc(100% - 120px); left: 10px; }
    .map-info { bottom: 10px; max-width: calc(100% - 20px); }
}
</style>
