import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import 'leaflet/dist/leaflet.css';

// Najpierw zaimportuj Bootstrap JS
import 'bootstrap';

// Dopiero potem Vue
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';
import LeafletMap from './components/LeafletMap.vue';

const app = createApp({});
app.component('example-component', ExampleComponent);
app.component('leaflet-map', LeafletMap);
app.mount('#app');
