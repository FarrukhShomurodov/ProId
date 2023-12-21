<script>
import axios from 'axios';

export default {
    data() {
        return {
            name: '',
            surname: '',
            notFound: '',
            authAction: 'Войти или создать PRO ID',
            auth: false,
            isAuth: false,
            phone_number: '',
            count: 0,
            limit: false,
            phoneNumber: '',
        };
    },
    methods: {
        // Format phone number input
        formatPhoneNumber(event) {
            // ... (omitted for brevity) ...
        },
        // Authenticate user
        Auth() {
            if (this.phone_number.length === 12) {
                axios.post('/api/sendOTP', {
                    phone_number: this.phone_number
                }).then(() => {
                    // Redirect to confirmation page and store phone number and authentication status
                    this.$router.replace('/confirmNumber')
                    localStorage.phoneNumber = this.phone_number
                    localStorage.isAuth = this.isAuth;
                })
            }
        },
    }
};
</script>

<template>
    <div class="login">
        <!-- Header section -->
        <div class="top">
            <div class="image">
                <img src="/images/logo/pro_id_logo.svg" alt="">
            </div>
            <div>
                <a href="" class="link">Что такое PRO ID <img src="/images/icons/question.svg"></a>
            </div>
        </div>
        <!-- Main form section -->
        <div class="bottom">
            <!-- PRO ID entrance -->
            <div class="enterence">
                <div class="text">
                    <p>Вход c помощью PRO ID</p>
                </div>
                <div class="image">
                    <img src="/images/logo/pro_id_logo_mini.svg" alt="">
                </div>
            </div>
            <!-- Login form -->
            <form @submit.prevent class="main-form">
                <p :class="{ 'not-found': notFound }">{{ surname }} {{ name !== '' ? name[0] + '.' : '' }} {{ notFound }}</p>
                <!-- Phone number input -->
                <input v-model="phoneNumber" required type="text" class="phone-number" placeholder="+998 (--) --- -- -- " @input="formatPhoneNumber" maxlength="19">
                <!-- Submit button -->
                <input @click="Auth" v-on:keyup.enter="Auth" type="submit" name="phoneNum" :class="{ 'light-button': name || auth || limit}" class="phone-number-button" :value="authAction">
                <p :class="{ 'revocer': name || auth}">Восстановить PRO ID.</p>
            </form>
            <!-- Additional text section -->
            <div class="text">
                <p>Продолжая использовать PRO ID, я принимаю <span>условия оферты.</span></p>
                <p>PRO ID - ключ от всех сервисов</p>
            </div>
        </div>
    </div>
    <!-- Bottom part (footer) -->
    <div class="bottom-part">
        <p>Используйте режим инкогнито на чужом компьютере</p>
        <p>Справка и поддержка <span>©2021-2023 PRO GROUP</span></p>
    </div>
</template>

<style scoped>
@import '/public/style/authFromAnotherApp.css';
</style>
