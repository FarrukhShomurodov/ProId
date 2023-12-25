// Import bootstrap
import 'bootstrap';

// Import Axios
import axios from 'axios';

// Initiliase axios
window.axios = axios;

// Auth Header
const headers = {
    'Authorization': `Bearer ` + localStorage.token,
};

// Set auth header in axios methods
window.axios.defaults.headers.common = headers

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
