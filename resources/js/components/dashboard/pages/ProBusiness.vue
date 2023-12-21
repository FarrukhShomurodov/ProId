<script>
import axios from "axios";

// Importing other components
import createProBusiness from "../business/modal/create/createProBusinessModal.vue"
import BusinessDetails from "../business/pages/Data.vue";

export default {
    components: {
        createProBusiness,
        BusinessDetails
    },
    data() {
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
        // Fetch user and business data when the component is mounted
        this.fetchUser();
    },
    methods: {
        // Method to fetch user and business data
        fetchUser() {
            // User
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/user', {headers}).then(res => {
                this.userId = res.data.id

                // Business
                axios.get(`/api/fetch-by-user/${this.userId}`, {headers}).then(res => {
                    this.businessData = res.data;
                    // Use setTimeout to simulate asynchronous loading
                    setTimeout(() => {
                        this.loading = true;
                    }, 1000);
                });
            })
        },
        // Method to close modal and refresh data
        close() {
            this.createBusiness = false;
            this.businessDetail = false;
            this.fetchUser();
        },
        // Method to show business details
        showBusinessDetails(id) {
            this.business_id = id;
            this.businessDetail = true;
        }
    }
}
</script>

<template>
    <!-- Main content section -->
    <div class="main_container" v-if="!businessDetail && loading">
        <!-- Business list -->
        <p>Мои бизнесы</p>
        <TransitionGroup name="list">
            <div class="business_container" v-for="data in businessData" :key="data.id">
                <!-- Clickable business block -->
                <div class="business_block" @click="showBusinessDetails(data.id)">
                    <!-- Business image -->
                    <div class="business_image" style="width: 80px; height: 80px; border-radius: 25px" :style="{
                        'background-image': `url(${data.image === '/images/icons/proBusiness/proConnect.jpg' ? data.image : '/storage/' + data.image})`,
                        'background-size': 'cover',
                        'background-position': 'center'
                    }"></div>
                    <!-- Business content -->
                    <div class="business_content">
                        <p>{{data.name_of_business}}</p>
                        <span>{{ data.address }}</span>
                    </div>
                </div>
            </div>
        </TransitionGroup>
        <!-- Add Business button -->
        <div class="add_business" @click="createBusiness = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p>Добавить новый бизнес</p>
        </div>
    </div>

    <!-- Links to Government services -->
    <div class="links_to_services" v-if="!businessDetail && loading">
        <p>Полезные ссылки</p>
        <!-- Link 1 -->
        <div class="add_business links">
            <span><a href="#">Открытие Юридического лица в Республике Узбекистан</a></span>
        </div>
        <!-- Link 2 -->
        <div class="add_business links" onclick="window.location.href='https://my.gov.uz/ru/service/357'">
            <span><a href="https://my.gov.uz/ru/service/357">Получение ЭЦП (Электронная цифровая подпись)</a></span>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <!-- Create Business modal -->
    <createProBusiness v-if="createBusiness" @close="close" :userId=userId></createProBusiness>

    <!-- Business Details modal -->
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

.list-enter-active,
.list-leave-active {
    transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}
</style>
