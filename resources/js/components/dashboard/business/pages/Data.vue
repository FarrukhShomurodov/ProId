<script>
import axios from "axios";

// Importing other components
import UpdateBusinessModal from "@/components/dashboard/business/modal/update/UpdateBusinessModal.vue";
import UpdateBanksDataModal from "@/components/dashboard/business/modal/update/UpdateBanksDataModal.vue";
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";
import createImageModal from "@/components/dashboard/business/modal/create/createImageModal.vue";
import BoxOffice from "@/components/dashboard/business/pages/BoxOffice.vue";

export default {
    props: ['business_id'],
    emits:['close'],
    components: {
        UpdateBusinessModal,
        UpdateBanksDataModal,
        createBanksDataModal,
        BoxOffice,
        createImageModal
    },
    data() {
        return {
            // Backend data
            business_details: [],
            banking_details: [],
            banking_id: 0,

            // Frontend state
            updateBusiness: false,
            updateBanking: false,
            addBanking: false,
            showBoxOffice: false,
            showReport: false,
            showCreateImage: false,
            loading: false,
        }
    },
    mounted() {
        // Fetch business details and banking data when the component is mounted
        this.fetchBusinesses();
    },
    methods: {
        fetchBusinesses() {
            // Fetch business details and banking data from the API

            // Fetch business details
            axios.get(`/api/business/${this.business_id}`)
                .then(res => {
                    this.business_details = res.data;
                })

            // Fetch banking data by business
            axios.get(`/api/bank-data-by-business/${this.business_id}`)
                .then(res => {
                    this.banking_details = res.data;
                    this.loading = true;
                })
        },
        close() {
            // Close modals and reload business details and banking data
            this.showCreateImage = this.updateBusiness = this.showReport = this.updateBanking = this.addBanking = false;
            this.fetchBusinesses();
        },
        showUpdateBanksDataModal(id) {
            // Show the UpdateBanksDataModal with the selected banking ID
            this.updateBanking = true;
            this.banking_id = id;
        },
        showReportPage() {
            // Show the report page
            this.showReport = true;
        }
    }
}
</script>

<template>
    <!-- Business details container -->
    <div class="business_details_container" v-if="loading">
        <div class="business_details" v-if="!showReport">
            <!-- Business header section -->
            <div>
                <img src="/images/icons/dashboard/back.svg" class="back" alt="" @click="$emit('close')">
                <div>
                    <h3>{{ business_details.name_of_business }}</h3>
                </div>
            </div>
            <!-- Business image section -->
            <div class="business_image" :style="{
                'background-image': `url(${business_details.image === null ? business_details.image : '/storage/' + business_details.image})`,
                'background-size': 'cover',
                'background-position': 'center'
            }">
                <img src="/images/icons/dashboard/edit.svg" alt="" @click="showCreateImage = true">
                <p>{{ business_details.image === null ? business_details.name_of_business.replace(' ','').slice(0,3) === 'OOO' ? business_details.name_of_business.replace(' ','').slice(3,5) : business_details.name_of_business.replace(' ','').slice(0,2) : '' }}</p>
            </div>
        </div>
        <!-- Business details content -->
        <div class="details">
            <!-- Navigation between Data and BoxOffice sections -->
            <div class="details_nav" v-if="!showReport">
                <p :class="{'active': !showBoxOffice}" @click="showBoxOffice = false">Данные</p>
                <p :class="{'active': showBoxOffice}" @click="showBoxOffice = !showBoxOffice">Кассы</p>
            </div>
            <!-- BoxOffice component -->
            <BoxOffice v-if="showBoxOffice" :business_id="this.business_id" @close='close'
                       :showReportPage="this.showReportPage"></BoxOffice>
            <!-- Business details section -->
            <div v-if="!showBoxOffice">
                <!-- Requisites section -->
                <div class="details_content">
                    <h3>Реквизиты</h3>
                    <div class="rekvizit_container">
                        <div class="d-row">
                            <h4 class="business_name">{{ business_details.name_of_business }}</h4>
                            <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content"
                                 @click="updateBusiness = true">
                        </div>
                        <div class="d-row">
                            <p>ИНН: {{ business_details.inn }}</p>
                            <p class="oked">ОКЕД: {{ business_details.oked }}</p>
                        </div>
                        <p class="address">Адрес: {{ business_details.address }}</p>
                    </div>
                </div>
                <!-- Banking details section -->
                <div class="details_content">
                    <h3>Банковские реквизиты</h3>
                    <!-- List of banking details with transition animation -->
                    <TransitionGroup name="list">
                        <div v-for="details in banking_details" :key="details.id">
                            <!-- Banking details container -->
                            <div class="banks_rekvizit_container">
                                <div class="d-row">
                                    <h4>Счет: {{ details.name_of_banking_akkaunt }}</h4>
                                    <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content"
                                         @click="showUpdateBanksDataModal(details.id)">
                                </div>
                                <div class="d-column">
                                    <p>Банк: {{ details.name }}</p>
                                    <p>МФО: {{ details.mfo }}</p>
                                    <p>Расчетный счет: {{ details.payment_account }}</p>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>
                </div>
                <!-- Button to add a new banking data -->
                <div class="add_banking_data" @click="addBanking = true">
                    <img src="/images/icons/dashboard/add.svg" alt="">
                    <p>Добавить новый банковский счет</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <!-- Modals -->
    <createImageModal v-if="showCreateImage" @close="close" :business_id="this.business_id"></createImageModal>
    <UpdateBusinessModal v-if="updateBusiness" @close="close"
                            :business_id="this.business_id"></UpdateBusinessModal>
    <UpdateBanksDataModal v-if="updateBanking" @close="close" :banking_id="this.banking_id"></UpdateBanksDataModal>
    <createBanksDataModal v-if="addBanking" @close="close" :business_id="this.business_id"></createBanksDataModal>
</template>

<style scoped>

.active {
    background-color: #CCC !important;
}
</style>
