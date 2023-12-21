// Import bootstrap
import './bootstrap';

// Import yandexMap
import { createYmaps } from 'vue-yandex-maps';

// Import create app from vue module
import { createApp } from 'vue/dist/vue.esm-bundler';

// Import router
import router from "@/router.js";

// Create vue app
const app = createApp({});


// Use components in vue app
app.use(router);

app.use(createYmaps({
    apikey: '3d92b1e0-8884-4b1f-912d-7659c829e4f7',
}))

// For use components in app
app.mount('#app');
