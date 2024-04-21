// Import bootstrap
import 'bootstrap';

// Import Axios
import axios from 'axios';

// Initialise axios
window.axios = axios;


let token = () => {
    let value = document.cookie.match('(^|;)\\s*accessToken\\s*=\\s*([^;]+)');
    return value ? decodeURIComponent(value.pop()) : null;
}

if(token()){
    window.axios.defaults.headers.common = {
        'Authorization': `Bearer ` + token(),
    }
}
