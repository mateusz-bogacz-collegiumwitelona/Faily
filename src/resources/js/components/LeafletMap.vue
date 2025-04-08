<template>
    <div class="map-container">
        <div :id="mapId" class="map-element"></div>
    </div>
</template>

<script>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import L from 'leaflet';

// Naprawianie problemu z ikonami
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow
});

export default {
    props: {
        center: {
            type: Array,
            default: () => [51.2101, 16.1619] // Legnica
        },
        zoom: {
            type: Number,
            default: 7
        },
        markers: {
            type: Array,
            default: () => []
        }
    },
    setup(props) {
        const mapId = ref('map-' + Math.random().toString(36).substring(2, 9));
        let map = null;
        let resizeObserver = null;

        const initMap = () => {
            // Inicjalizacja mapy
            map = L.map(mapId.value, {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView(props.center, props.zoom);

            // Dodanie warstwy OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Dodanie domyślnego markera na środku mapy
            L.marker(props.center).addTo(map)
                .bindPopup('Tu jesteśmy :)')
                .openPopup();

            // Dodanie dodatkowych markerów
            if (props.markers.length > 0) {
                props.markers.forEach(markerData => {
                    const marker = L.marker([markerData.lat, markerData.lng]).addTo(map);

                    if (markerData.popup) {
                        marker.bindPopup(markerData.popup);
                    }
                });
            }

            // Nasłuchiwanie na zdarzenie resize okna
            window.addEventListener('resize', handleResize);

            // Użyj ResizeObserver dla dokładniejszego śledzenia zmian rozmiaru
            if (typeof ResizeObserver !== 'undefined') {
                resizeObserver = new ResizeObserver(() => {
                    if (map) map.invalidateSize();
                });

                const mapContainer = document.getElementById(mapId.value);
                if (mapContainer) {
                    resizeObserver.observe(mapContainer);
                }
            }

            // Przeładuj mapę po renderowaniu komponentu
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 100);
        };

        const handleResize = () => {
            if (map) {
                map.invalidateSize();
            }
        };

        onMounted(() => {
            initMap();

            // Dodatkowe opóźnione odświeżenie po pełnym renderowaniu strony
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 500);

            // Dodatkowe odświeżenie po dłuższym czasie, aby upewnić się że mapa jest prawidłowo wyświetlona
            setTimeout(() => {
                if (map) map.invalidateSize();
            }, 1500);
        });

        onUnmounted(() => {
            // Usuń nasłuchiwanie na zdarzenie resize
            window.removeEventListener('resize', handleResize);

            // Zatrzymaj obserwatora zmian rozmiaru
            if (resizeObserver) {
                resizeObserver.disconnect();
            }

            // Zniszcz mapę
            if (map) {
                map.remove();
                map = null;
            }
        });

        return {
            mapId,
            getMap: () => map
        };
    }
}
</script>

<style scoped>
.map-container {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.map-element {
    width: 100%;
    height: 100%;
}

/* Te style już istnieją w app.css, ale warto je powtórzyć w komponencie dla pewności */
:deep(.leaflet-container) {
    width: 100%;
    height: 100% !important;
    z-index: 1;
}

:deep(.leaflet-control-zoom) {
    border-radius: 4px;
}

:deep(.leaflet-popup-content-wrapper) {
    border-radius: 6px;
}
</style>
