import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import 'leaflet/dist/leaflet.css';

import 'bootstrap';

import { createApp } from 'vue';
import { createI18n} from "vue-i18n";
import LeafletMap from './components/LeafletMap.vue';
import MainMap from './components/MainMap.vue';
import 'leaflet/dist/leaflet.css';
import EventForm from "./components/EventForm.vue";``

import pl from './i18n/pl.json'
import en from './i18n/en.json'
import jpn from './i18n/jpn.json'
import es from './i18n/es.json'
import ua from './i18n/ua.json'

const initialEvents = window.events || [];
const app = createApp({
    data() {
        return {
            initialEvents: window.events || []
        };
    }
});
const i18n = createI18n({
    legacy: false,
    locale: window.locale || 'en',
    fallbackLocale: 'en',
    messages: {
        pl,
        en,
        jpn,
        es,
        ua
    }
});
app.component('leaflet-map', LeafletMap);
app.component('main-map', MainMap);
app.component('event-form', EventForm);
app.use(i18n);
app.mount('#app');
