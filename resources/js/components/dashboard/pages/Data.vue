<script>
// Import necessary dependencies
import axios from "axios";

// Importing other components
import authModal from '../data/modal/UserData.vue'
import PhoneNumber from '../data/PhoneNumber.vue'
import Email from "../data/Email.vue";
import CreateMailModal from '../data/modal/CreateMailModal.vue';
import CreateAddressModal from "../data/modal/CreateAddressModal.vue";
import UpdateAddressModal from "../data/modal/UpdateAddressModal.vue";
import CreateEducationModal from "@/components/dashboard/data/modal/CreateEducationModal.vue";
import UpdateEducationModal from "@/components/dashboard/data/modal/UpdateEducationModal.vue";
import {Swiper, SwiperSlide} from "swiper/vue";
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';

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
        UpdateAddressModal,
        Email,
        YandexMap,
        YandexMapDefaultSchemeLayer,
        YandexMapDefaultFeaturesLayer,
        YandexMapMarker,
        YandexMapListener,
        CreateEducationModal,
        UpdateEducationModal
    },
    data() {
        return {
            // User data
            userId: null,
            image: '',
            name: '',
            surname: '',
            avatarUrl: '',
            phoneNumber: '',
            mail: '',
            address_id: 0,
            addresses: [],
            education_id: 0,
            educations: [],

            // Frontend state
            showAuthModal: false,
            showPhoneEditionModal: false,
            showMailModal: false,
            hasMail: false,
            addressShow: false,
            showUpdateAddressModal: false,
            showEmailEdition: false,
            showCreateEducationModal: false,
            showUpdateEducationModal: false,
            loading: false,
        }
    },
    mounted() {
        // Fetch user data when the component is mounted
        this.getUser()
    },
    methods: {
        // Format phone number
        formatPhoneNumber(phoneNumber) {
            // Remove any non-digit characters from the phone number
            const cleanedNumber = phoneNumber.replace(/\D/g, '');

            // Apply the desired formatting
            const formattedNumber = `+${cleanedNumber.slice(0, 3)} ${cleanedNumber.slice(3, 5)} ${cleanedNumber.slice(5, 8)} ${cleanedNumber.slice(8, 10)} ${cleanedNumber.slice(10, 12)}`;

            return formattedNumber;
        },

        //Destroy map
        destroyMap() {
            // Check if the map instance exists
            if (this.$refs.yandexMap) {
                // Destroy the map instance
                this.$refs.yandexMap.destroy();
            }
        },

        // Method to fetch user data
        getUser() {
            this.fetchUser()
            this.showAuthModal = false

            // Fetch user data and addresses
            axios.get('/api/user').then(res => {
                const data = res.data;

                // Set user data
                this.userId = data.id
                this.name = data.name;
                this.surname = data.surname;
                this.avatarUrl = data.avatar;
                this.phoneNumber = data.phone_number

                // format phone number
                const phoneNumber = `+${this.phoneNumber}`;
                this.phoneNumber = this.formatPhoneNumber(phoneNumber);

                this.hasMail = data.email !== null
                this.mail = data.email

                // Fetch user addresses
                axios.get(`/api/address/${this.userId}`).then(res => {
                    this.addresses = res.data
                    if (this.addresses.length !== 0) {
                        this.address_id = res.data[0].id
                    }
                    this.destroyMap();
                })

                axios.get(`/api/education/${this.userId}`).then(res => {
                    this.educations = res.data;
                    this.loading = true;
                })
            })
        },
        // Method to go back from subpages
        goBack() {
            this.showUpdateEducationModal = this.showCreateEducationModal = this.showEmailEdition = this.showAuthModal = this.showPhoneEditionModal = this.showMailModal = this.addressShow = this.showUpdateAddressModal = false;
            this.getUser();
        }

    }
}
</script>

<template>
    <!-- Main content section -->
    <div v-if="loading">
        <!-- User information and actions -->
        <div class="data_container" v-if="!showPhoneEditionModal && !showEmailEdition">
            <!-- User info and edit button -->
            <div class="data flex-row">
                <!-- User avatar -->
                <div class="ava" :style="{
                            'background-image': `url(${avatarUrl !== null ? '/storage/' + avatarUrl : '/images/icons/dashboard/user.svg'})`,
                            'background-size': 'cover',
                            'background-position': 'center',
                             'background-color': 'white'
                        }"></div>
                <!-- User information -->
                <div class="info">
                    <p class="uppercase">{{ name }} {{ surname }}</p>
                    <p>Мы будем к вам обращаться так: <span class="uppercase">{{ name }} {{ surname }}</span></p>
                </div>
                <!-- Edit button -->
                <img src="/images/icons/dashboard/edit.svg" alt="" id="show-modal" @click="showAuthModal = true">
                <!-- Authentication modal -->
                <authModal v-if="showAuthModal" @close="getUser"></authModal>
            </div>
            <!-- User addresses -->
            <div class="address">
                <h3>Адреса</h3>
                <span>Для заказа в один клик и чтобы не вводить в Навигаторе</span>
                <!-- Address content container -->
                <div class="address_content_container flex-row">
                    <!-- Display existing addresses -->
                    <div v-for="address in addresses">
                        <div class="address_container">
                            <!-- Yandex Map for each address -->
                            <yandex-map :settings="{
                                location: {
                                    center:  JSON.parse(address.coords),
                                    zoom: 10,
                                },
                                behaviors: ['default', 'scrollZoom'],
                            }" class="yandexDataMap">
                                <yandex-map-default-scheme-layer/>
                                <yandex-map-default-features-layer/>
                                <yandex-map-marker
                                    :settings="{
                                        coordinates:  JSON.parse(address.coords),
                                    }"
                                >
                                    <template #default>
                                        <img
                                            alt=""
                                            :src="'/images/icons/marker.svg'"
                                            :style="{
                                                width: '40px',
                                                position: 'relative',
                                                boxSizing: 'border-box',
                                                transform: 'translate(-50%, calc(-50% - 58px))',
                                                cursor: 'pointer',
                                            }"
                                        />
                                    </template>
                                </yandex-map-marker>
                            </yandex-map>
                        </div>
                        <!-- Display existing addresses text -->
                        <div class="address_text" @click="showUpdateAddressModal = true; address_id = address.id">
                            <p>{{ address.name.slice(0, 30) + "..." }}</p>
                        </div>
                    </div>
                    <!-- Add new address button -->
                    <div>
                        <div class="address_container flex-column">
                            <!-- Yandex Map for the user's location -->
                            <yandex-map :settings="{
                                location: {
                                    center:   [69.240562, 41.2800],
                                    zoom: 10,
                                },
                                behaviors: ['default', 'scrollZoom'],
                            }" class="yandexDataMap">
                                <yandex-map-default-scheme-layer/>
                                <yandex-map-default-features-layer/>
                                <yandex-map-marker
                                    :settings="{
                                        coordinates:  [69.240562, 41.2800],
                                    }"
                                >
                                    <template #default>
                                        <img
                                            alt=""
                                            :src="'/images/icons/marker.svg'"
                                            :style="{
                                                width: '40px',
                                                position: 'relative',
                                                boxSizing: 'border-box',
                                                transform: 'translate(-50%, calc(-50% - 58px))',
                                                cursor: 'pointer',
                                            }"
                                        />
                                    </template>
                                </yandex-map-marker>
                            </yandex-map>
                        </div>
                        <!-- Add new address text -->
                        <div class="address_text" @click="addressShow = true;">
                            <p>Добавить еще один адрес</p>
                        </div>
                    </div>
                </div>
                <!-- All addresses button -->
                <div class="phone__number kon_container flex-row all_addresses">
                    <div class="kon_info_template flex-row">
                        <div class="kon_icon location_center">
                            <img src="/images/icons/dashboard/location.svg">
                        </div>
                        <div>
                            <p class="delete_pro_id_akk">Все адреса</p>
                        </div>
                    </div>
                    <img src="/images/icons/dashboard/next.svg">
                </div>
            </div>
            <!-- User contacts -->
            <div class="kontacts">
                <h3>Контакты</h3>
                <div>
                    <!-- Main phone number -->
                    <div class="phone__number kon_container flex-row">
                        <div class="kon_info_template flex-row">
                            <div class="kon_icon"></div>
                            <div>
                                <span>Основной номер телефона</span>
                                <p>{{ phoneNumber }}</p>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/next.svg" @click="showPhoneEditionModal = true">
                    </div>
                    <!-- Display email if exists -->
                    <div class="phone__number kon_container flex-row" v-if="hasMail">
                        <div class="kon_info_template flex-row">
                            <div class="kon_icon"></div>
                            <div>
                                <span>Email</span>
                                <p>{{ mail }}</p>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/next.svg" @click="showEmailEdition = true">
                    </div>
                    <!-- Pro Mail (optional) -->
                    <div class="phone__number kon_container flex-row" v-if="hasMail">
                        <div class="kon_info_template flex-row">
                            <div class="kon_icon"></div>
                            <div>
                                <p>Добавить почтовый ящик в ProMail</p>
                                <span>Еще больше возможностей и безопасность</span>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/add.svg">
                    </div>
                    <!-- Add email if none exists -->
                    <div class="phone__number kon_container flex-row" v-if="!hasMail">
                        <div class="kon_info_template flex-row">
                            <div class="kon_icon"></div>
                            <div>
                                <p>Добавить почтовый ящик</p>
                                <span>Еще больше возможностей и безопасность</span>
                            </div>
                        </div>
                        <img src="/images/icons/dashboard/add.svg" @click="showMailModal = true">
                    </div>
                </div>
            </div>

            <!-- Education -->
            <div class="delete_akk">
                <h3>Образование</h3>
                <span>Храните ваши данные в едином месте</span>
                <div class="education__blocks">
                    <TransitionGroup name="list">
                        <div v-for="education in educations" :key="education.id">
                            <div class="education__container">
                                <img src="/images/icons/dashboard/edit.svg" alt="" @click="showUpdateEducationModal = true; education_id = education.id" width="25px">
                                <div class="education_items flex-column">
                                    <p> Вид: <span> {{ education.type }}</span></p>
                                    <p> {{ education.name }}</p>
                                    <p> Период: <span> {{ education.started.slice(0,4) }}-{{ education.expired.slice(0,4) }}</span></p>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                    <!-- Add Education -->
                    <div class="education__container" @click="showCreateEducationModal = true">
                        <img src="/images/icons/dashboard/add_white.svg" alt="" class="add_new_icon" >
                        <div class="education_items flex-column">
                            <p class="add_new_text"> Добавить новую
                                должность</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage account section -->
            <div class="delete_akk">
                <h3>Управление данными</h3>

                <div class="phone__number kon_container flex-row">
                    <div class="kon_info_template flex-row">
                        <div class="kon_icon"></div>
                        <div>
                            <p class="delete_pro_id_akk">Удалить аккаунт PRO ID</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subpages -->
        <!-- Edit phone number page -->
        <PhoneNumber v-if="showPhoneEditionModal" @goBack="goBack"
                     :phoneNumber=phoneNumber
                     :userId=userId
        ></PhoneNumber>
        <!-- Edit email page -->
        <Email v-if="showEmailEdition" @goBack="goBack"
               :email=mail
               :userId=userId
        ></Email>
        <!-- Add new email page -->
        <CreateMailModal v-if="showMailModal" @goBack="goBack"
                         :phoneNumber=phoneNumber
                         :userId=userId
        ></CreateMailModal>
        <!-- Add new address page -->
        <CreateAddressModal
            v-if="addressShow" @goBack="goBack"
            :phoneNumber=phoneNumber
            :userId=userId
        ></CreateAddressModal>
        <!-- Edit existing address page -->
        <UpdateAddressModal
            v-if="showUpdateAddressModal" @goBack="goBack"
            :address_id=address_id
        ></UpdateAddressModal>
        <!-- Add Education data -->
        <CreateEducationModal v-if="showCreateEducationModal" @goBack="goBack"
                     :userId=userId
        ></CreateEducationModal>
        <!-- Update Education data -->
        <UpdateEducationModal v-if="showUpdateEducationModal" @goBack="goBack"
                     :education_id=education_id
        ></UpdateEducationModal>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
</template>

<style scoped>
</style>
