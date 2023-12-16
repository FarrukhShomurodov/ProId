// Import bootstrap
import './bootstrap';

// Import create app from vue module
import { createApp } from 'vue/dist/vue.esm-bundler';

// Import router
import router from "@/router.js";

// Create vue app
const app = createApp({});

// Use router in vue app
app.use(router);

// For use components in app
app.mount('#app');
