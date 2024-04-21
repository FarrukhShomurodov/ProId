<script>
import axios from "axios";

// Importing other components
import Reports from "@/components/dashboard/business/pages/Reports.vue";
import createBoxOfficeModal from "@/components/dashboard/business/modal/create/createBoxOfficeModal.vue";
import UpdateBoxOfficeModal from "@/components/dashboard/business/modal/update/UpdateBoxOfficeModal.vue";

export default {
    props: {
        business_id: 'business_id',
        showReportPage: {
            type: Function,
            required: true
        },
    },
    emits:['close'],
    components: {
        Reports,
        createBoxOfficeModal,
        UpdateBoxOfficeModal
    },
    data() {
        return {
            // Backend data
            boxOfficeDetails: [],
            bankData: [],
            box_office_id: 0,

            // Frontend state
            showReport: false,
            showCreateBoxOffice: false,
            showUpdateBoxOffice: false,
            loading: false,
        }
    },
    mounted() {
        // Fetch box office details and bank data when the component is mounted
        this.fetchBoxOffice();
        this.getBankData();
    },
    methods: {
        async fetchBoxOffice() {
            try {
                const res = await axios.get(`/api/box-office-by-business/${this.business_id}`);
                this.boxOfficeDetails = res.data;
                this.loading = true;
            } catch (error) {
                console.error('Error fetching box office:', error);
            }
        },
        async getBankData() {
            try {
                const banksResponse = await axios.get(`/api/bank-data-by-business/${this.business_id}`);

                // Update bankData object with bank data using the unique id as key

                const data = banksResponse.data;
                for (let i = 0; i < data.length; i++) {
                    this.bankData[data[i].id] = {
                        id: data[i].id,
                        name: data[i].name_of_banking_akkaunt
                    };
                }
            } catch (error) {
                console.error("Error fetching bank data:", error);
            }
        },
        close() {
            // Close modals and reload box office details
            this.showReport = this.showUpdateBoxOffice = this.showCreateBoxOffice = false;
            this.$emit('close');
            this.fetchBoxOffice();
        }
    },
}
</script>

<template>
    <!-- Box office details section -->
    <div v-if="!showReport">
        <div class="details_content">
            <h3>Кассы</h3>
        </div>
        <!-- List of box offices with transition animation -->
        <TransitionGroup name="list">
            <div class="kassa" v-if="loading" v-for="data in boxOfficeDetails" :key="data.id">
                <div class="d-row">
                    <h4>{{ data.name }}</h4>
                    <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content"
                         @click="box_office_id = data.id; showUpdateBoxOffice = true">
                </div>
                <div class="d-row">
                    <p>Сервис: {{ data.service }}</p>
                    <p class="oked">Счет : {{
                            bankData[data.bank_data.id].id === data.bank_data.id ? bankData[data.bank_data.id].name : "unalienable"
                        }}</p>
                </div>
                <div class="d-row">
                    <p class="status">Статус: {{ data.isActive === 1 ? "Активный" : "Деактивированый" }}</p>
                    <button class="status reports"
                            @click="this.showReportPage(); showReport = true; box_office_id = data.id;">Отчеты
                    </button>
                </div>
            </div>
        </TransitionGroup>
        <!-- Button to add a new box office -->
        <button class="add_box_office" @click="showCreateBoxOffice = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p>Добавить новую кассу</p>
        </button>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <!-- Modals -->
    <Reports v-if="showReport" :box_office_id="this.box_office_id" :business_id="this.business_id"
             @close="close"></Reports>
    <create-box-office-modal v-if="showCreateBoxOffice" @close="close"
                             :business_id="this.business_id"></create-box-office-modal>
    <update-box-office-modal v-if="showUpdateBoxOffice" @close="close" :box_office_id="this.box_office_id"
                             :business_id="this.business_id"></update-box-office-modal>
</template>

<style scoped>

</style>
