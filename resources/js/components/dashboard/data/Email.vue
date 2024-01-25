<script>
// Importing other components
import DeleteEmailModal from "../data/modal/DeleteEmailModal.vue";

export default {
    props:['email'],
    emits:['goBack'],
    components:{
        DeleteEmailModal
    },
    data(){
        return{
            // Frontend state
            ShowEmailEditionModal: false
        }
    },
    methods:{
        close(){
            this.ShowEmailEdition = false;
        }
    },
};
</script>

<template>
    <div>
        <div class="edition">
            <img src="/images/icons/dashboard/back.svg" alt="" @click="$emit('goBack');" class="back">
            <div class="kon_header">
                <h3>Email</h3>
                <span>Узнайте, как email помогает защитить ваши данные</span>
            </div>
        </div>
        <div class="edition_container" @click="ShowEmailEditionModal = true">
            <div class="flex_row">
                <div>
                    <span>для защиты аккаунта</span><br>
                    <span>{{ this.email }}</span>
                </div>
                <img src="/images/icons/dashboard/exit.svg" alt="" >
            </div>
        </div>
    </div>
    <DeleteEmailModal v-if="ShowEmailEditionModal" :email=this.email @close="close" @goBack="$emit('goBack')"></DeleteEmailModal>
</template>

<style scoped>
</style>
