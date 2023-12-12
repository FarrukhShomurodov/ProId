<script>

//Import axios
import axios from 'axios';

//Import Swiper Vue.js components
import {Swiper, SwiperSlide} from 'swiper/vue';

//Import router
import router from "@/router.js";

export default {
    components: {
        Swiper,
        SwiperSlide
    },

    computed: {
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

            }
        },
    },

    data() {
        return {
            //user data
            name: 'Name',
            surname: 'Surname',
            avatarUrl: null,

            //front
            showUserMenu: false,
            showServiceMenu: false,
        }
    },
    mounted() {
        this.fetchUser();
        document.body.addEventListener('click', this.invisibleMenu);
    },
    beforeDestroy() {
        document.body.removeEventListener('click', this.invisibleMenu);
    },
    methods: {
        //Backend
        fetchUser() {
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };

            //getting auth user
            axios.get('/api/user', {headers}).then(res => {
                const data =res.data;

                //setting data to variables
                this.name = data.name;
                this.surname = data.surname;
                this.avatarUrl = data.avatar;
            })
        },
        logout() {
            axios.post('/api/logout', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
            }).then(() => {
                localStorage.removeItem('token');
                router.push({ name: 'login' });
            })
        },

        //Front
        showUserAccesses() {
            this.showUserMenu = !this.showUserMenu;
            this.showServiceMenu = false;
        },
        showServices() {
            this.showServiceMenu = !this.showServiceMenu
            this.showUserMenu = false;
        },
        invisibleMenu(event) {
            const headerNav = document.querySelector('.header_nav');
            const headerNavContent = document.querySelector('.header_nav_content');

            if (!headerNav.contains(event.target) && !headerNavContent.contains(event.target)) {
                this.showUserMenu = false;
                this.showServiceMenu = false;
            }
        },

    }
}
</script>

<template>
    <!--Header-->
    <header>
        <div class="header">
            <div class="header_nav flex-row">
                <img class="logo" src="/images/logo/pro_id_logo.svg" alt="">
                <div class="min_nav" :class="{'radiosNone': showUserMenu || showServiceMenu}">
                    <img src="/images/icons/dashboard/menu.svg" alt="" @click="showServices">
                    <div class="ava" style="width: 47px; height: 47px;" :style="{
                            'background-image': `url(${avatarUrl !== null ? '/storage/' + avatarUrl : '/images/icons/dashboard/user.svg'})`,
                            'background-size': 'cover',
                            'background-position': 'center',
                            'background-color': 'white'
                        }" @click="showUserAccesses"></div>
                </div>
            </div>

            <div class="header_nav_content" :class="{'user_data': showUserMenu}">
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
                            <a @click="logout" class="">Выйти</a>
                            <a href="#" class="">Справка</a>
                            <a href="#" class="">Обратная связь</a>
                            <hr>
                        </ul>
                    </nav>
                    <div class="secure_link">
                        <p>Пользовательское соглашение</p>
                        <p>Политика конфиденциальности</p>
                    </div>
                </div>
            </div>
            <div class="header_nav_content" :class="{'user_service': showServiceMenu}">
                <div class="header_nav_content_container">
                    <div>
                        <h4 class="pro__service">PRO Сервисы</h4>
                    </div>
                    <nav class="nav_service">
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>
                        <div class="service_block">
                            <div class="services"></div>
                            <p>Good Look</p>
                        </div>

                    </nav>
                    <div class="secure_link">
                        <p>Пользовательское соглашение</p>
                        <p>Политика конфиденциальности</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!--Content Container-->
    <div class="content_container">

        <!--Navbar-->
        <div class="nav_position">
            <nav class="navbar__menu">
                <swiper class="navbar__list"
                        :slides-per-view="3"
                        @swiper="onSwiper"
                        @slideChange="onSlideChange">
                    <swiper-slide @click="$router.push({ name: 'dashboard-main' })" :class="{'active': this.$route.name === 'dashboard-main'}" class="navbar__link">
                        Главная
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-data' })" :class="{'active': this.$route.name === 'dashboard-data'}" class="navbar__link">
                        Данные
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-pay' })" :class="{'active': this.$route.name === 'dashboard-pay'}" class="navbar__link">
                        PRO PAY
                    </swiper-slide>
                    <swiper-slide class="navbar__link">
                        Безопасность
                    </swiper-slide>
                    <swiper-slide @click="$router.push({ name: 'dashboard-business' })" :class="{'active': this.$route.name === 'dashboard-business'}" class="navbar__link">
                        PRO BUSINESS
                    </swiper-slide>
                    <swiper-slide class="navbar__link">Подписки</swiper-slide>
                    <div class="border"></div>
                    <swiper-slide class="navbar__link">Поддержка</swiper-slide>
                    <swiper-slide class="navbar__link">Справка</swiper-slide>
                </swiper>
            </nav>
        </div>

        <!--Content-->
        <div>
            <section class="main__content">
                <router-view :fetchUser="fetchUser"/>
            </section>
        </div>

        <!--Empty Area-->
        <div class="empty_area"></div>
    </div>

    <!--Footer-->
    <footer>
        <p class="d-none">Используйте режим инкогнито на чужом компьютере </p>
        <p>Справка и поддержка <span>©2021-2023 PRO GROUP</span></p>
    </footer>
</template>

<style>
@import '/public/style/dashboard/dashboard.css';
</style>
