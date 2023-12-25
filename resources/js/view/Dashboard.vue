<script>
// Import axios for API requests
import axios from 'axios';

// Import Swiper Vue.js components
import {Swiper, SwiperSlide} from 'swiper/vue';

// Import router for navigation
import router from "@/router.js";

export default {
    components: {
        Swiper,
        SwiperSlide
    },

    computed: {
        // Dynamically determine the active section based on the current route
        activeSection() {
            switch (this.$route.name) {
                case 'dashboard-main':
                    return 'main';
                case 'dashboard-data':
                    return 'data';
                case 'dashboard-pay':
                    return 'pay';
                case 'dashboard-business':
                    return 'business';
                case 'dashboard-pro-job':
                    return 'pro-job';
            }
        },
    },

    data() {
        return {
            // User data
            name: 'Name',
            surname: 'Surname',
            avatarUrl: null,

            // Frontend
            showUserMenu: false,
            showServiceMenu: false,
        }
    },
    mounted() {
        // Fetch user data when the component is mounted
        this.fetchUser();
        // Add a click event listener to hide menus when clicking outside
        document.body.addEventListener('click', this.invisibleMenu);
    },
    beforeDestroy() {
        // Remove the click event listener when the component is destroyed
        document.body.removeEventListener('click', this.invisibleMenu);
    },
    methods: {
        // Backend: Fetch user data from the API
        fetchUser() {
            // API request to get authenticated user data
            axios.get('/api/user').then(res => {
                const data = res.data;

                // Set user data to variables
                this.name = data.name;
                this.surname = data.surname;
                this.avatarUrl = data.avatar;
            })
        },
        // Backend: Logout user
        logout() {
            axios.post('/api/logout').then(() => {
                localStorage.removeItem('token');
                router.push({name: 'login'});
            })
        },

        // Frontend: Toggle user menu visibility
        showUserAccesses() {
            this.showUserMenu = !this.showUserMenu;
            this.showServiceMenu = false;
        },
        // Frontend: Toggle service menu visibility
        showServices() {
            this.showServiceMenu = !this.showServiceMenu
            this.showUserMenu = false;
        },
        // Frontend: Hide menus when clicking outside
        invisibleMenu(event) {
            const headerNav = $('.header_nav');
            const headerNavContent = $('.header_nav_content');

            if (!$(event.target).closest(headerNav).length && !$(event.target).closest(headerNavContent).length) {
                this.showUserMenu = false;
                this.showServiceMenu = false;
            }
        },
    }
}
</script>

<template>
    <!-- Header -->
    <header>
        <div class="header flex-column">
            <div class="header_nav flex-row">
                <!-- Logo and menu icon -->
                <img class="logo" src="/images/logo/pro_id_logo.svg" alt=""
                     @click="$router.push({name: 'dashboard-main'})"
                        style="border-radius: 0;">
                <div class="min_nav" :class="{'radiosNone': showUserMenu || showServiceMenu}">
                    <img src="/images/icons/dashboard/menu.svg" alt="" @click="showServices">
                    <!-- User avatar -->
                    <div class="ava" style="width: 47px; height: 47px;" :style="{
                        'background-image': `url(${avatarUrl !== null ? '/storage/' + avatarUrl : '/images/icons/dashboard/user.svg'})`,
                        'background-size': 'cover',
                        'background-position': 'center',
                        'background-color': 'white'
                    }" @click="showUserAccesses"></div>
                </div>
            </div>

            <!-- User Data -->
            <transition :duration="{ enter: 800, leave: 800 }" name="fade">
                <div class="header_nav_content user_data" v-show=showUserMenu>
                    <div class="header_nav_content_container">
                        <div class="user_data">
                            <div class="ava" style="width: 66px; height: 66px;" :style="{
                                                'background-image': `url(${avatarUrl !== null ? '/storage/' + avatarUrl : '/images/icons/dashboard/user.svg'})`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                            <h4>{{ name }}<br>{{ surname }}</h4>
                        </div>
                        <nav>
                            <ul class="header_nav_list flex-column">
                                <a href="#" class="">Почта</a>
                                <a href="#" class="">Диск</a>
                                <a href="#" class="">Управление аккаунтом</a>
                                <hr>
                                <a href="#" class="">Внешний вид</a>
                                <hr>
                                <a href="#" @click="logout" class="">Выйти</a>
                                <a href="#" class="">Справка</a>
                                <a href="https://support.in-pro.net/help-center" class="">Обратная связь</a>
                                <hr>
                            </ul>
                        </nav>
                        <div class="secure_link">
                            <a href="#" class=""><p>Пользовательское соглашение</p></a>
                            <a href="#" class=""><p>Политика конфиденциальности</p></a>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- Service -->
            <transition :duration="{ enter: 800, leave: 800 }" name="fade">
                <div class="header_nav_content service_block" v-show="showServiceMenu">
                    <div class="header_nav_content_container">
                        <div>
                            <h4 class="pro__service">PRO Сервисы</h4>
                        </div>
                        <nav class="nav_service">
                            <div class="service_block" onclick="window.location.href='https://cloud.in-pro.net'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/Cloud.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a href="https://cloud.in-pro.net"><p>Cloud</p></a>
                            </div>
                            <div class="service_block" onclick="window.location.href='https://pay.in-pro.net'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/Pay.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a href="https://pay.in-pro.net"><p>Pay</p></a>
                            </div>
                            <div class="service_block" onclick="window.location.href='https://crm.in-pro.net'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/Crm.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a><p href="https://crm.in-pro.net">CRM</p></a>
                            </div>
                            <div class="service_block" onclick="window.location.href='https://support.in-pro.net'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/Support.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a><p href="https://support.in-pro.net">Support</p></a>
                            </div>
                            <div class="service_block" onclick="window.location.href='https://donate.in-pro.net'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/Donate.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a href="https://donate.in-pro.net"><p>Donate</p></a>
                            </div>
                            <div class="service_block" onclick="window.location.href='https://goodlook.uz'">
                                <div class="services" :style="{
                                                'background-image': `url('/images/ProEcoSystemIcons/GoodLook.png')`,
                                                'background-size': 'cover',
                                                'background-position': 'center',
                                                'background-color': 'white'
                                            }"></div>
                                <a href="https://goodlook.uz"><p>GoodLook</p></a>
                            </div>

                        </nav>
                        <div class="secure_link">
                            <a href="#" class=""><p>Пользовательское соглашение</p></a>
                            <a href="#" class=""><p>Политика конфиденциальности</p></a>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </header>

    <!-- Content Container -->
    <div class="content_container">
        <!-- Navbar -->
        <div class="nav_position">
            <nav class="navbar__menu">
                <!-- Swiper component for responsive navigation links -->
                <swiper class="navbar__list" :slides-per-view="3">
                    <!-- Navigation links -->
                    <swiper-slide @click="$router.push({ name: 'dashboard-main' })"
                                  :class="{'active': this.$route.name === 'dashboard-main'}" class="navbar__link">
                        Главная
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-data' })"
                                  :class="{'active': this.$route.name === 'dashboard-data'}" class="navbar__link">
                        Данные
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-pay' })"
                                  :class="{'active': this.$route.name === 'dashboard-pay'}" class="navbar__link">
                        PRO PAY
                    </swiper-slide>
                    <swiper-slide class="navbar__link">
                        Безопасность
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-pro-job' })"
                                  :class="{'active': this.$route.name === 'dashboard-pro-job'}" class="navbar__link">
                        PRO JOB
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-business' })"
                                  :class="{'active': this.$route.name === 'dashboard-business'}" class="navbar__link">
                        PRO BUSINESS
                    </swiper-slide>
                    <swiper-slide class="navbar__link">Подписки</swiper-slide>
                    <div class="border"></div>
                    <swiper-slide class="navbar__link" onclick="window.location.href='https://support.in-pro.net'">
                        Поддержка
                    </swiper-slide>
                    <swiper-slide class="navbar__link" onclick="window.location.href='https://support.in-pro.net'">
                        Справка
                    </swiper-slide>
                </swiper>
            </nav>
        </div>

        <!-- Content -->
        <div>
            <section class="main__content">
                <!-- Router view to render component based on the route -->
                <router-view :fetchUser="fetchUser"/>
            </section>
        </div>

        <!-- Empty Area -->
        <div class="empty_area"></div>
    </div>

    <!-- Footer -->
    <footer>
        <p class="d-none">Используйте режим инкогнито на чужом компьютере </p>
        <p class="footer_mob"><span>Справка и поддержка </span><span>©2021-2023 PRO GROUP</span></p>
    </footer>
</template>

<style>
@import '/public/style/dashboard/dashboard.css';
@import "/public/style/dashboard/modal.css";
@import "/public/style/dashboard/pro-business-details.css";
@import "/public/style/dashboard/report.css";
@import "/public/style/dashboard/data.css";
@import "/public/style/dashboard/main.css";
@import "/public/style/dashboard/pay.css";
@import "/public/style/dashboard/pro-business.css";
@import "/public/style/dashboard/pro-job.css";
</style>
