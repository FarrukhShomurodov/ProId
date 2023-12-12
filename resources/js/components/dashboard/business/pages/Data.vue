<script >
import axios from "axios";

//components
import UpdateProBusinessModal from "@/components/dashboard/business/modal/update/UpdateProBusinessModal.vue";
import UpdateBanksDataModal from "@/components/dashboard/business/modal/update/UpdateBanksDataModal.vue";
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";
import createImageModal from "@/components/dashboard/business/modal/create/createImageModal.vue"
import BoxOffice from "@/components/dashboard/business/pages/BoxOffice.vue";

export default {
    props:['business_id'],
    components:{
        UpdateProBusinessModal,
        UpdateBanksDataModal,
        createBanksDataModal,
        BoxOffice,
        createImageModal
    },
    data(){
        return {
            business_details: [],
            banking_details: [],
            updateBusiness: false,
            updateBanking: false,
            addBanking: false,
            banking_id: 0,
            showBoxOffice: false,
            loading: false,
            showReport: false,
            showCreateImage: false
        }
    },
    mounted() {
     this.fetchBusinesses();
    },
    methods:{
        fetchBusinesses(){
            axios.get(`/api/pro-business-show/${this.business_id}`).then(res => {
                this.business_details = res.data;
            })

            // fetching banking data by business
            axios.get(`/api/banking-data-fetch/${this.business_id}` ).then(res => {
                this.banking_details = res.data
                this.loading = true
            })
        },
        close(){
            this.showCreateImage = false;
            this.showReport = false
            this.updateBusiness = false
            this.updateBanking = false
            this.addBanking = false
            this.fetchBusinesses();
        },
        showUpdateBanksDataModal(id){
            this.updateBanking = true
            this.banking_id = id
        },
        showReportPage(){
            this.showReport = true
        }
    }
}
</script>

<template>
    <div class="business_details_container" v-if="loading">
        <div class="business_details" v-if="!showReport">
            <div>
                <img src="/images/icons/dashboard/back.svg" class="back" alt="" @click="$emit('close')">
                <div class="">
                    <h3>{{business_details.name_of_business}}</h3>
                </div>
            </div>
            <div class="business_image" :style="{
                                    'background-image': `url(${business_details.image === '/images/icons/proBusiness/proConnect.jpg' ? business_details.image : 'storage/' + business_details.image})`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }">
                <img src="/images/icons/dashboard/edit.svg" alt="" @click="showCreateImage = true">
            </div>
        </div>
        <div class="details">
            <div class="details_nav" v-if="!showReport">
                <p :class="{'active': !showBoxOffice}"  @click="showBoxOffice = false">Данные</p>
                <p :class="{'active': showBoxOffice}" @click="showBoxOffice = !showBoxOffice">Кассы</p>
            </div>
            <BoxOffice v-if="showBoxOffice" :business_id="this.business_id" @close='close' :showReportPage="this.showReportPage"></BoxOffice>
            <div v-if="!showBoxOffice">
                <div class="details_content">
                    <h3>Реквизиты</h3>
                    <div class="rekvizit_container">
                        <div class="d-row">
                            <h4 class="business_name">{{business_details.name_of_business}}</h4>
                            <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content" @click="updateBusiness = true">
                        </div>
                        <div class="d-row">
                            <p>ИНН: {{business_details.inn}}</p>
                            <p class="oked">ОКЕД: {{business_details.oked}}</p>
                        </div>
                        <p class="address">Адрес: {{business_details.address}}</p>
                    </div>
                </div>
                <div class="details_content">
                    <h3>Банкоские реквизиты</h3>
                    <div v-for="details in banking_details">
                        <div class="banks_rekvizit_container">
                            <div class="d-row">
                                <h4>Счет : Основной</h4>
                                <img src="/images/icons/dashboard/edit.svg" alt="" class="edit_content" @click="showUpdateBanksDataModal(details.id)">
                            </div>
                            <div class="d-column">
                                <p>Банк: {{ details.name }}</p>
                                <p>МФО: {{ details.mfo }}</p>
                                <p>Расчетный счет: {{ details.payment_account }}</p>
                            </div>
                        </div>
                    </div>
                </div>

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
    <UpdateProBusinessModal v-if="updateBusiness" @close="close" :business_id="this.business_id"></UpdateProBusinessModal>
    <UpdateBanksDataModal v-if="updateBanking" @close="close" :banking_id="this.banking_id"></UpdateBanksDataModal>
    <createBanksDataModal v-if="addBanking" @close="close" :business_id="this.business_id"></createBanksDataModal>
</template>

<style scoped>
@import "/public/style/dashboard/pro-business-details.css";
.active{
    background-color: #CCC !important;
}
</style>
