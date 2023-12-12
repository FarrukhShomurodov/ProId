<script >
import axios from "axios";

// components
import createProBusiness from "../business/modal/create/createProBusinessModal.vue"
import BusinessDetails from "../business/pages/Data.vue";

export default {
    components: {
        createProBusiness,
        BusinessDetails
    },
    data(){
        return {
            createBusiness: false,
            userId: '',
            businessData: '',
            business_id: 0,
            businessDetail: false,
            loading: false
        }
    },
    mounted() {
        this.fetchUser();
    },
    methods:{
        fetchUser(){
            // User
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/user', {headers}).then(res => {
                this.userId = res.data.id

                // Business
                axios.get(`/api/fetch-by-user/${this.userId}`).then(res => {
                    this.businessData = res.data
                    this.loading = true;
                });
            })
        },
        close(){
            this.createBusiness = false
            this.businessDetail = false;
            this.fetchUser();
        },
        showBusinessDetails(id){
            this.business_id = id;
            this.businessDetail = true;
        }
    }
}
</script>

<template>
    <div class="main_container" v-if="!businessDetail && loading">
        <p><b>Мои бизнесы</b></p>
        <div class="business_container" v-for=" data in businessData ">
            <div class="business_block" @click="showBusinessDetails(data.id)">
                <div class="business_image" style="width: 80px; height: 80px; border-radius: 25px" :style="{
                                    'background-image': `url(${data.image === '/images/icons/proBusiness/proConnect.jpg' ? data.image : 'storage/' + data.image})`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }"></div>
                <div class="business_content">
                    <p>{{data.name_of_business}}</p>
                    <span>{{ data.address }}</span>
                </div>
            </div>
        </div>

        <!-- Add Business -->
        <div class="add_business" @click="createBusiness = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p><b>Добавить новый бизнес</b></p>
        </div>
    </div>

    <!-- Links to Government services -->
    <div class="links_to_services" v-if="!businessDetail && loading">
        <p><b>Полезные ссылки</b></p>
        <div class="add_business links">
            <span><b>Открытие Юридического лица в Республике Узбекистан</b></span>
        </div>
        <div class="add_business links">
            <span><b>Получение ЭЦП (Электронная цифровая подпись)</b></span>
        </div>
    </div>
    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
    <createProBusiness v-if="createBusiness" @close="close" :userId=userId></createProBusiness>
    <BusinessDetails v-if="businessDetail" @close="close" :business_id="business_id"></BusinessDetails>
</template>

<style scoped>
@import "/public/style/dashboard/pro-business.css";

.loading-indicator {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    z-index: 9999;
}
</style>
