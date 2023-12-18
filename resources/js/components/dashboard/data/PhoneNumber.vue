<script>
import UpdatePhoneModal from './modal/UpdatePhoneModal.vue';
import axios from "axios";
export default {
    components: {
        UpdatePhoneModal
    },
    props:['userId'],
    data(){
        return{
            showModal: false,
            phoneNumber: ''
        }
    },
    mounted() {
        this.fetchUser()
    },
    methods: {
        fetchUser(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };

            //getting user data
            axios.get('/api/user', {headers}).then(res => {
                const data = res.data;
                this.phoneNumber = data.phone_number
            });
        },
        modal(){
            this.showModal = true;
        },
        close(){
            this.fetchUser()
            this.showModal = false
        }
    },
};
</script>

<template>
    <div>
        <div class="phone_edition">
            <img src="/images/icons/dashboard/back.svg" alt="" @click="$emit('goBack');">
            <div class="kon_header">
                <h3>Телефон</h3>
                <span>Узнайте, как телефон помогает защитить ваши данные</span>
            </div>
        </div>
        <div class="phone_edition_container" @click="modal">
            <div class="flex_row">
                <div>
                    <span>для защиты аккаунта</span><br>
                    <span>+{{ phoneNumber }}</span>
                </div>
                <img src="/images/icons/dashboard/menu-dots-vertical.png" alt="" >
            </div>
        </div>
    </div>
    <UpdatePhoneModal v-if="showModal" :phoneNumber=phoneNumber :userId="userId" @close="close"></UpdatePhoneModal>
</template>

<style scoped>
@import "/public/style/dashboard/data.css";
</style>
