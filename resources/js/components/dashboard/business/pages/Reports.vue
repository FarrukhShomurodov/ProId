<script>
import axios from "axios";

// Import components
import UpdateBoxOfficeModal from "@/components/dashboard/business/modal/update/UpdateBoxOfficeModal.vue";

export default {
    components: {
        UpdateBoxOfficeModal
    },
    props: [
        'box_office_id',
        'business_id',
    ],
    data() {
        return {
            // Frontend state
            showUpdateBoxOffice: false,
            name: '',
            service: '',
            active: true,
            nameOfBankingAkkaunt: '',
            loading: false
        }
    },
    mounted() {
        // Fetch box office data and associated banking data when the component is mounted
        this.getboxOffice();
    },
    methods: {
        async getboxOffice() {
            // Fetch box office data and associated banking data from the API

            // Get box office data
            const boxOfficeResponse = await axios.get(`api/box-offices-show/${this.box_office_id}`);
            this.service = boxOfficeResponse.data.service;
            this.name = boxOfficeResponse.data.name;
            this.active = boxOfficeResponse.data.isActive === 1;

            // Get banking data by box office
            const bankResponse = await axios.get(`api/banking-data-show/${boxOfficeResponse.data.bank_data_id}`);
            this.nameOfBankingAkkaunt = bankResponse.data.name_of_banking_akkaunt;
            this.loading = true;
        },
        close() {
            // Close the UpdateBoxOfficeModal
            this.showUpdateBoxOffice = false;
        }
    },
}
</script>

<template>
    <!-- Box office details container -->
    <div v-if="loading" class="d_row">
        <!-- Back button and box office details -->
        <img src="/images/icons/dashboard/back.svg" @click="$emit('close')" class="back">
        <div class="kassa_by_one">
            <!-- Box office details -->
            <div class="d_row">
                <h3>Касса: {{ name }}</h3>
                <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content" @click="showUpdateBoxOffice = true">
            </div>
            <div class="d_row">
                <p>Сервис: {{ service }}</p>
                <p>Счет : {{ nameOfBankingAkkaunt }}</p>
            </div>
            <div class="d_row">
                <p>Статус: {{ active ? "Активный" : "Деактивированый"}}</p>
                <p>Баланс: <span class="price"><b>6 080 000</b></span></p>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <!-- Modal -->
    <update-box-office-modal v-if="showUpdateBoxOffice" @close="close" :box_office_id="this.box_office_id" :business_id="this.business_id"></update-box-office-modal>
</template>

<style scoped>
</style>
