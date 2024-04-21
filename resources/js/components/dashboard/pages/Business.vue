<script>
import axios from "axios";

// Importing other components
import createBusinessModal from "../business/modal/create/createBusinessModal.vue"
import BusinessDetailModal from "../business/pages/Data.vue";

export default {
    components: {
        createBusinessModal,
        BusinessDetailModal
    },
    data() {
        return {
            // Backend data
            businessData: [],
            business_id: 0,

            // Frontend state
            showCreateBusinessModal: false,
            showBusinessDetailModal: false,
            loading: false
        }
    },
    mounted() {
        // Fetch business data when the component is mounted
        this.fetchBusiness();
    },
    methods: {
        // Method to fetch  business data
        async fetchBusiness() {
            axios.get("/api/business").then(res => {
                this.businessData = res.data;

                this.loading = true;
            });
        },
        // Method to close modal and refresh data
        close() {
            this.showCreateBusinessModal = this.showBusinessDetailModal = false;
            this.fetchBusiness();
        },
    }
}
</script>

<template>
    <!-- Main content section -->
    <div class="main_container" v-if="!showBusinessDetailModal && loading">
        <!-- Business list -->
        <h3>Мои бизнесы</h3>
        <TransitionGroup name="list">
            <div class="business_container" v-for="data in businessData" :key="data.id">
                <!-- Clickable business block -->
                <div class="business_block flex-row" @click="showBusinessDetailModal = true; business_id = data.id;">
                    <!-- Business image -->
                    <div class="business_image" style="width: 80px; height: 80px; border-radius: 25px" :style="{
                        'background-image': `url(${data.image === null ? data.image : '/storage/' + data.image})`,
                        'background-size': 'cover',
                        'background-position': 'center'
                    }"> <p>{{ data.image === null ? data.name_of_business.replace(' ','').slice(0,3) === 'OOO' ? data.name_of_business.replace(' ','').slice(3,5) : data.name_of_business.replace(' ','').slice(0,2) : '' }}</p> </div>
                    <!-- Business content -->
                    <div class="business_content">
                        <p>{{ data.name_of_business }}</p>
                        <span>{{ data.address }}</span>
                    </div>
                </div>
            </div>
        </TransitionGroup>
        <!-- Add Business button -->
        <button class="add_business flex-row" @click="showCreateBusinessModal = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p>Добавить новый бизнес</p>
        </button>
    </div>

    <!-- Links to Government services -->
    <div class="links_to_services" v-if="!showBusinessDetailModal && loading">
        <h3>Полезные ссылки</h3>
        <!-- Link 1 -->
        <div class="add_business flex-row links">
            <span><a href="#">Открытие Юридического лица в Республике Узбекистан</a></span>
        </div>
        <!-- Link 2 -->
        <div class="add_business flex-row links" onclick="window.location.href='https://my.gov.uz/ru/service/357'">
            <span><a href="https://my.gov.uz/ru/service/357">Получение ЭЦП (Электронная цифровая подпись)</a></span>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>

    <!-- Create Business modal -->
    <createBusinessModal v-if="showCreateBusinessModal" @close="close"></createBusinessModal>

    <!-- Business Details modal -->
    <BusinessDetailModal v-if="showBusinessDetailModal" @close="close" :business_id="business_id"></BusinessDetailModal>
</template>

<style scoped>
</style>
