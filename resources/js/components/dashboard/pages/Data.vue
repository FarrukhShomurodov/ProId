<script>
import axios from "axios";

// Importing other components
import authModal from '../data/modal/update/UpdateUserDataModal.vue'
import PhoneNumber from '../data/PhoneNumber.vue'
import Email from "../data/Email.vue";
import CreateMailModal from '../data/modal/create/CreateMailModal.vue';
import CreateAddressModal from "../data/modal/create/CreateAddressModal.vue";
import UpdateAddressModal from "../data/modal/update/UpdateAddressModal.vue";
import CreateEducationModal from "../data/modal/create/CreateEducationModal.vue";
import UpdateEducationModal from "../data/modal/update/UpdateEducationModal.vue";
import Addresses from "../data/Addresses.vue";
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';

export default {
    // props: {
    //     fetchUser: {
    //         type: Function,
    //         required: true
    //     }
    // },
    components: {
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
        UpdateEducationModal,
        Addresses
    },
    data() {
        return {
            // Backend data
            userId: null,
            image: '',
            name: '',
            surname: '',
            avatarUrl: '',
            phoneNumber: '',
            mail: '',
            address_id: null,
            addresses: [],
            education_id: 0,
            educations: [],

            // Frontend state
            showAuthModal: false,
            showPhoneEditionModal: false,
            showMailModal: false,
            hasMail: false,
            showCreateAddressModal: false,
            showUpdateAddressModal: false,
            showEmailEdition: false,
            showCreateEducationModal: false,
            showUpdateEducationModal: false,
            showAddresses: false,
            loading: false,
        }
    },
    mounted() {
        this.getUser();

        // destroy ymap
        this.$nextTick(() => {
            this.destroyYandexMap();
        });
    },
    methods: {
        destroyYandexMap() {
            // Access the YandexMap component using the ref
            const yandexMap = this.$refs.yandexMap;

            // Check if the YandexMap component exists
            if (yandexMap) {
                // Call the destroy method to destroy the map
                yandexMap.destroy();
            }
        },

        // Format phone number
        formatPhoneNumber(phoneNumber) {
            // Remove any non-digit characters from the phone number
            const cleanedNumber = phoneNumber.replace(/\D/g, '');

            // Apply the desired formatting
            const formattedNumber = `+${cleanedNumber.slice(0, 3)} ${cleanedNumber.slice(3, 5)} ${cleanedNumber.slice(5, 8)} ${cleanedNumber.slice(8, 10)} ${cleanedNumber.slice(10, 12)}`;

            return formattedNumber;
        },

        // Method to fetch user data
        async getUser() {
            this.showAuthModal = false

            // Fetch user data
            await axios.get('/api/user').then(res => {
                const { id, name, surname, avatar, phone_number, email } = res.data;

                // Set user data
                this.userId = id;
                this.name = name;
                this.surname = surname;
                this.avatarUrl = avatar;
                this.phoneNumber = phone_number;

                // Format phone number
                this.phoneNumber = this.formatPhoneNumber(`+${this.phoneNumber}`);

                this.hasMail = email !== null;
                this.mail = email;

                this.getAddress();
                this.getEducation();

                this.loading = true;
            })
        },

        // Fetch user education
        async getEducation(){
            await axios.get(`/api/education/${this.userId}`).then(res => {
                this.educations = res.data;
            })
        },
        // Fetch user addresses
        async getAddress(){
            await axios.get(`/api/address/${this.userId}`).then(res => {
                this.addresses = res.data.reverse().slice(0,3)
            })
        },
        // Method to go back from subpages
        goBack() {
            this.showAddresses = this.showUpdateEducationModal = this.showCreateEducationModal = this.showEmailEdition = this.showAuthModal = this.showPhoneEditionModal = this.showMailModal = this.showCreateAddressModal = this.showUpdateAddressModal = false;
            this.getUser();
        },
    },
}
</script>

<template>
    <!-- Main content section -->
    <div v-if="loading">
        <!-- User information and actions -->
        <div class="data_container" v-if="!showPhoneEditionModal && !showEmailEdition && !showAddresses">
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
                            <yandex-map ref="yandexMap" :settings="{
                                location: {
                                    center:  JSON.parse(address.coords),
                                    zoom: 10,
                                },
                                behaviors: ['default'],
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
                        <button class="address_text" @click="showUpdateAddressModal = true; address_id = address.id">
                            <p>{{ address.name.slice(0, 30) + "..." }}</p>
                        </button>
                    </div>
                    <!-- Add new address button -->
                    <div>
                        <div class="address_container flex-column">
                            <!-- Yandex Map for the user's location -->
                            <yandex-map ref="yandexMap" :settings="{
                                location: {
                                    center:   [69.240562, 41.2800],
                                    zoom: 10,
                                },
                                behaviors: ['default'],
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
                        <button class="address_text" @click="showCreateAddressModal = true;">
                            <p>Добавить еще один адрес</p>
                        </button>
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
                    <img src="/images/icons/dashboard/next.svg" @click="showAddresses = true">
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
                                <img src="/images/icons/dashboard/edit.svg" alt="" @click="showUpdateEducationModal = true; education_id = education.id" width="25">
                                <div class="education_items flex-column">
                                    <p> Вид: <span> {{ education.type }}</span></p>
                                    <p> {{ education.name }}</p>
                                    <p> Период: <span> {{ education.started.slice(0,4) }}-{{ education.expired == null ? 'Учусь по сей день' : education.expired.slice(0,4) }}</span></p>
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
        <Addresses
            v-if="showAddresses" @goBack="goBack"
            :userId=userId
        ></Addresses>
        <CreateAddressModal
            v-if="showCreateAddressModal" @goBack="goBack"
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
