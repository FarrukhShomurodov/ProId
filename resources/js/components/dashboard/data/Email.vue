<script>
import axios from "axios";

import DeleteEmailModal from "@/components/dashboard/data/modal/DeleteEmailModal.vue";
export default {
    props:['email', 'userId'],
    components:{
        DeleteEmailModal
    },
    data(){
        return{
            ShowEmailEdition: false
        }
    },
    methods:{
        close(){
            this.$emit('goBack')
        }
    },
};
</script>

<template>
    <div>
        <div class="phone_edition">
            <img src="/images/icons/dashboard/back.svg" alt="" @click="$emit('goBack');">
            <div class="kon_header">
                <h3>Email</h3>
                <span>Узнайте, как email помогает защитить ваши данные</span>
            </div>
        </div>
        <div class="phone_edition_container" @click="ShowEmailEdition = true">
            <div class="flex_row">
                <div>
                    <span>для защиты аккаунта</span><br>
                    <span>{{ this.email }}</span>
                </div>
                <img src="/images/icons/dashboard/menu-dots-vertical.png" alt="" >
            </div>
        </div>
    </div>
    <DeleteEmailModal v-if="ShowEmailEdition" :userId=this.userId :email=this.email @close="close"></DeleteEmailModal>
</template>

<style scoped>
@import "/public/style/dashboard/data.css";
</style>
