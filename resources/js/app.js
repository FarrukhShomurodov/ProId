import './bootstrap';
import { createYmaps } from 'vue-yandex-maps';
import { createApp } from 'vue/dist/vue.esm-bundler';
import router from "@/router.js";

const app = createApp({});

app.use(router);
app.use(createYmaps({
    apikey: '3d92b1e0-8884-4b1f-912d-7659c829e4f7',
}))
app.mount('#app');
