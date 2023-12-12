import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler';

//import map
// import { createYmaps } from 'vue-yandex-maps';
// import { VueYandexMaps } from 'vue-yandex-maps';

//router
import router from "@/router.js";

// //lang и version теперь задаются по умолчанию
// const settings = VueYandexMaps.PluginSettings = {
//     apikey: '7036e6e7-9b57-475b-96c5-171d3c98c88c',
// };
//
// app.use(createYmaps(settings));

const app = createApp({});

app.use(router);

app.mount('#app');
