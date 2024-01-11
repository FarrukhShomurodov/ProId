<script>
// import axios
import axios from "axios";

// import components
import CreateAddressModal from "../data/modal/CreateAddressModal.vue";
import UpdateAddressModal from "../data/modal/UpdateAddressModal.vue";
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';


export default {
    props: [
        'userId'
    ],
    components: {
        CreateAddressModal,
        UpdateAddressModal,
        YandexMap,
        YandexMapDefaultSchemeLayer,
        YandexMapDefaultFeaturesLayer,
        YandexMapMarker,
        YandexMapListener,
    },
    data() {
        return {
            addresses: [],
            address_id: null,
            loading: false,
            showCreateAddressModal: false,
            showUpdateAddressModal: false,

        }
    },
    mounted() {
        this.getAddressDate()
    },
    methods: {
        getAddressDate() {
            axios.get(`/api/address/${this.userId}`).then(res => {
                this.addresses = res.data
                this.loading = true;
                if (this.addresses.length !== 0) {
                    this.address_id = res.data[0].id
                }
                this.destroyMap();
            })
        },
        goBackFromModal() {
            this.showCreateAddressModal = this.showUpdateAddressModal = false;
            this.getAddressDate()

        }
    }
}

</script>

<template>
    <div class="edition">
        <img src="/images/icons/dashboard/back.svg" alt="" @click="$emit('goBack');" class="back">
        <div class="kon_header">
            <h3>Все адреса</h3>
            <span>Для заказа в один клик и чтобы не вводить в Навигаторе</span>
        </div>
    </div>
    <div class="address" v-if="loading">
        <!-- Address content container -->
        <div class="all_addresses_container">
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
                <div class="address_text" @click="showCreateAddressModal = true;">
                    <p>Добавить еще один адрес</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <CreateAddressModal
        v-if="showCreateAddressModal" @goBack="goBackFromModal"
        :userId=this.userId
    ></CreateAddressModal>
    <!-- Edit existing address page -->
    <UpdateAddressModal
        v-if="showUpdateAddressModal" @goBack="goBackFromModal"
        :address_id=address_id
    ></UpdateAddressModal>
</template>

<style scoped>
</style>
