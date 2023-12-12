import {createRouter, createWebHistory} from 'vue-router'
import { nextTick } from 'vue'

//Auth
import Login from './components/auth/Login.vue'
import Register from "./components/auth/Register.vue"
import ConfirmNumber from "./components/auth/ConfirmNumber.vue"
import AuthFromAnotherApp from "./components/auth/AuthFromAnotherApp.vue"

//Passport
import Client from './components/passport/Client.vue';
import AuthorizedClients from './components/passport/AuthorizedClients.vue';
import PersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';

//Dashboard
import Dashboard from "./view/Dashboard.vue"
import Main from './components/dashboard/pages/Main.vue'
import Data from './components/dashboard/pages/Data.vue'
import ProBusiness from './components/dashboard/pages/ProBusiness.vue';
import Pay from './components/dashboard/pages/Pay.vue'
import Map from './view/Map.vue'

//Routes
const routes = [
    {
        path: '/',
        component: Login,
        name: 'login',
        beforeEnter(to, from, next) {
            if (token && from.name === 'dashboard'){
                next({name: 'dashboard'})
            }else{
                next()
            }
        }
    },
    {
        path: '/oauth/authorize',
        component: AuthFromAnotherApp
    },
    {
        path: '/api/login',
        component: AuthFromAnotherApp,
        name: 'api/login'
    },
    {
        path: '/confirmNumber',
        component: ConfirmNumber,
        name: 'confirmNumber',
        beforeEnter(to, from, next){
            if(from.name === 'login' || from.name === "api/login"){
                next()
            }else{
                next({name: 'login'})
            }
            if(to.name === "register"){
                next({name: 'register'})
            }
        }
    },
    {
        path: '/register',
        component: Register,
        name: 'register',
        beforeEnter(to, from, next){
            if(from.name === 'confirmNumber'){
                next()
            }else{
                next({name: 'login'})
            }
        }
    },
    {
        path: '/',
        component: Dashboard,
        name: 'dashboard',
        children: [
            {
                path: 'main',
                component: Main,
                name: 'dashboard-main', //assign unique names for navigation
            },
            {
                path: 'data',
                component: Data,
                name: 'dashboard-data', //assign unique names for navigation
            },
            {
                path: 'pay',
                component: Pay,
                name: 'dashboard-pay', //assign unique names for navigation
            },
            {
                path: 'business',
                component: ProBusiness,
                name: 'dashboard-business', //assign unique names for navigation
            },
        ],
        beforeEnter(to, from, next){
            if(from.name === 'register' || from.name === 'confirmNumber'){
                next()
            }else{
                next({name: 'login'})
            }
        }
    },
    // Passport
    {
        path: '/client',
        component: Client,
        name: 'client',
    },
    // {
    //     path: '/authorized',
    //     component: AuthorizedClients
    // },
    // {
    //     path: '/accessToken',
    //     component: PersonalAccessTokens
    // }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})


let token = localStorage.token;

// Function to check if the user is logged in
const checkLogin = async () => {
    if (token) {
        try {
            const response = await axios.get('/api/user', { headers });
            const isAdmin = response.data.is_admin;
            return response.status === 200 && isAdmin === "1";
        } catch (error) {
            console.error('Error fetching user data:', error);
            return false;
        }
    } else {
        return false;
    }
};

// Initial check on page load
router.beforeEach(async (to, from, next) => {
    const isLoggedIn = await checkLogin();

    if (to.name === 'login' && isLoggedIn) {
        next({ name: 'dashboard' });
    } else if (to.name === 'dashboard' && !isLoggedIn) {
        next({ name: 'login' });
    } else {
        next();
    }
});

// Guard for other routes
router.beforeEach(async (to, from, next) => {
    const isLoggedIn = await checkLogin();

    if (isLoggedIn) {
        next();
    } else {
        next({ name: 'login' });
    }
});


export default router;
