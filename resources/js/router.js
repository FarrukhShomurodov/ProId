import {createRouter, createWebHistory} from 'vue-router'

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
import Business from './components/dashboard/pages/Business.vue';
import Pay from './components/dashboard/pages/Pay.vue'
import Job from "./components/dashboard/pages/Job.vue";

//Routes
const routes = [
    {
        path: '/',
        component: Login,
        name: 'login',
        beforeEnter(to, from, next) {
            if (from.matched[0].name === 'dashboard') {
                next()
            } else {
                if (token()) {
                    next({name: 'dashboard'})
                } else {
                    next()
                }
            }
        }
    },
    // {
    //     path: '/oauth/authorize',
    //     component: AuthFromAnotherApp
    // },
    {
        path: '/api/login',
        component: AuthFromAnotherApp,
        name: 'api/login'
    },
    {
        path: '/confirmNumber',
        component: ConfirmNumber,
        name: 'confirmNumber',
        beforeEnter(to, from, next) {
            if (from.name === 'login' || from.name === "api/login") {
                next()
            } else {
                next({name: 'login'})
            }
            if (to.name === "register") {
                next({name: 'register'})
            }
        }
    },
    {
        path: '/register',
        component: Register,
        name: 'register',
        beforeEnter(to, from, next) {
            if (from.name === 'confirmNumber') {
                next()
            } else {
                next({name: 'login'})
            }
        }
    },
    {
        path: '/dashboard',
        component: Dashboard,
        redirect: {name: 'dashboard-main'},
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
                component: Business,
                name: 'dashboard-business', //assign unique names for navigation
            },
            {
                path: 'job',
                component: Job,
                name: 'dashboard-job', //assign unique names for navigation
            },
        ],
        beforeEnter(to, from, next) {
            if (token()) {
                next()
            } else {
                if (from.name === 'register' || from.name === 'confirmNumber') {
                    next()
                } else {
                    next({name: 'login'})
                }
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

let token = () => {
    let value = document.cookie.match('(^|;)\\s*accessToken\\s*=\\s*([^;]+)');
    return value ? decodeURIComponent(value.pop()) : null;
}

// function deleteAccessTokenCookie() {
//     document.cookie = "accessToken=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
// }

router.beforeEach(async (to, from, next) => {
    if (token() !== null) {
        try {
            const response = await axios.get('/api/user', {
                headers: {
                    'Authorization': 'Bearer ' + token()
                }
            });
            const isAdmin = response.data.is_admin;
            const isLogin = response.status === 200;

            if (isLogin) {
                if (to.name === 'client' && isAdmin !== "1") {
                    next({name: 'dashboard'});
                } else {
                    next();
                }
            } else {
                next({name: 'login'});
            }
        } catch (error) {
            console.error('Error fetching user data:', error);
            // deleteAccessTokenCookie()
            next({name: 'login'});
        }
    } else {
        next();
    }
});


export default router;
