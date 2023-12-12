<script>
//Import axios
import axios from "axios";

//Components
import authModal from '../data/modal/authModal.vue'
import PhoneNumber from '../data/PhoneNumber.vue'
import CreateMailModal from '../data/modal/CreateMailModal.vue';
import CreateAddressModal from "../data/modal/CreateAddressModal.vue";
import UpdateAddressModal from "../data/modal/UpdateAddressModal.vue";
import {Swiper, SwiperSlide} from "swiper/vue";

export default {
    props: {
        fetchUser: {
            type: Function,
            required: true
        }
    },

    components: {
        SwiperSlide,
        Swiper,
        authModal,
        PhoneNumber,
        CreateMailModal,
        CreateAddressModal,
        UpdateAddressModal
    },

    data() {
        return {
            //user data
            userId: null,
            image: '',
            name: '',
            surname: '',
            avatarUrl: '',
            phoneNumber: '',
            mail: '',
            address_id: 0,
            addresses: [],

            //fontend
            showModal: false,
            phoneInfoShow: false,
            mailShow: false,
            hasMail: false,
            addressShow: false,
            showUpdateAddressModal: false,
            loading:  false,
        }
    },

    mounted() {
        this.getUser()
    },

    methods: {
        //getting user
        getUser() {
            this.fetchUser()
            this.showModal = false
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };

            //getting user data
            axios.get('/api/user', {headers}).then(res => {
                const data = res.data;

                //setting user data
                this.userId = data.id
                this.name = data.name;
                this.surname = data.surname;
                this.avatarUrl = data.avatar;
                this.phoneNumber = data.phone_number
                this.hasMail = data.email != null ? true : false
                this.mail = data.email

                //getting addresses
                axios.get(`/api/address/${this.userId}`).then(res =>
                {
                    this.addresses = res.data
                    if(this.addresses.length != 0){
                        this.address_id = res.data[0].id
                    }
                    this.loading = true;
                })
            })
        },

        //frontend
        showPhoneNumberInfo() {
            this.phoneInfoShow = true
        },
        showMain() {
            this.mailShow = true
        },
        showAddressModal(){
            this.addressShow = true;
        },
        showUpdateAddress(){
            this.showUpdateAddressModal = true;
        },
        goBack() {
            this.showModal = false
            this.phoneInfoShow = false;
            this.mailShow = false;
            this.addressShow = false;
            this.showUpdateAddressModal = false;
            this.getUser()
        },
    }
}
</script>

<template>
    <div v-if="loading">
        <div class="data_container" v-if="!mailShow && !phoneInfoShow">
            <div class="data">
                <div class="ava" :style="{
                            'background-image': `url(${avatarUrl !== null ? 'storage/' + avatarUrl : '/images/icons/dashboard/user.svg'})`,
                            'background-size': 'cover',
                            'background-position': 'center',
                             'background-color': 'white'
                        }"></div>
                <div class="info">
                    <p class="uppercase">{{ name }} {{ surname }}</p>
                    <p>Мы будем к вам обращаться так: <span class="uppercase">{{ name }} {{ surname }}</span></p>
                </div>
                <img src="/images/icons/dashboard/edit.svg" alt="" id="show-modal" @click="showModal = true">
                <authModal v-if="showModal" @close="getUser"></authModal>
            </div>
            <div class="address">
                <h3>Адреса</h3>
                <span>Для заказа в один клик и что бы не вводить в Навигаторе</span>
                <div class="address_content_container">
                        <swiper
                                :slides-per-view="3"
                                @swiper="onSwiper">
                                <swiper-slide class="swiper-address">
                                    <div class="address_container">
                                        <img src="/images/icons/dashboard/location.svg" alt="">
                                        <div class="address_text" @click="showAddressModal">
                                            <p>
                                                Добавить еще один адрес
                                            </p>
                                        </div>
                                    </div>
                                </swiper-slide>
                                <swiper-slide v-for="address in addresses">
                                    <div class="address_container">
                                        <img src="/images/icons/dashboard/location.svg" alt="">
                                        <div class="address_text" @click="showUpdateAddress">
                                            <p>
                                                {{address.name}}
                                            </p>
                                        </div>
                                    </div>
                                </swiper-slide>
                        </swiper>
                </div>
                <div class="phone__number kon_container all_addresses">
                    <div class="kon_info_template">
                        <div class="kon_icon location_center">
                            <img src="/images/icons/dashboard/location.svg">
                        </div>
                        <div>
                            <p class="delete_pro_id_akk">Все адреса</p>
                        </div>
                    </div>
                    <img src="/images/icons/dashboard/next.svg" >
                </div>
            </div>
            <div class="kontacts">
                <h3>Контакты</h3>
                <div>
                    <div class="phone__number kon_container">
                        <div class="kon_info_template">
                            <div class="kon_icon"></div>
                            <div>
                                <span>Основной номер телефона</span>
                                <p>+{{ phoneNumber }}</p>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/next.svg" @click="showPhoneNumberInfo">
                    </div>
                    <!-- Has Email -->
                    <div class="phone__number kon_container" v-if="hasMail">
                        <div class="kon_info_template">
                            <div class="kon_icon"></div>
                            <div>
                                <span>Email</span>
                                <p>{{ mail }}</p>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/next.svg">
                    </div>
                    <!-- Does Not have email -->
                    <div class="phone__number kon_container" v-if="!hasMail">
                        <div class="kon_info_template">
                            <div class="kon_icon"></div>
                            <div>
                                <p>Добавить почтовый ящик</p>
                                <span>Еще больше возможностей и безопасность</span>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/add.svg" @click="showMain">
                    </div>
                </div>
            </div>
            <div class="delete_akk">
                <h3>Управление данными</h3>
                <div class="phone__number kon_container">
                    <div class="kon_info_template">
                        <div class="kon_icon"></div>
                        <div>
                            <p class="delete_pro_id_akk">Удалить аккаунт PRO ID</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other pages -->
        <PhoneNumber v-if="phoneInfoShow" @goBack="goBack"
                     :phoneNumber=phoneNumber
                     :userId=userId
        ></PhoneNumber>
        <CreateMailModal v-if="mailShow" @goBack="goBack"
                         :phoneNumber=phoneNumber
                         :userId=userId
        ></CreateMailModal>
        <CreateAddressModal
            v-if="addressShow" @goBack="goBack"
            :phoneNumber=phoneNumber
            :userId=userId
        ></CreateAddressModal>
        <UpdateAddressModal
            v-if="showUpdateAddressModal" @goBack="goBack"
            :address_id=address_id
        ></UpdateAddressModal>
    </div>
    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
</template>

<style>
@import "/public/style/dashboard/data.css";
</style>
