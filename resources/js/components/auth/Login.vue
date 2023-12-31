<script>
export default {
    data() {
        return {
            name: '',
            surname: '',
            notFound: '',
            authAction: 'Войти',
            auth: false,
            isAuth: false,
            phone_number: '',
            id: '',
            count: 0,
            showPopup: false,
            limit: false
        };
    },
    mounted() {
        // This lifecycle hook is empty
    },
    methods: {
        // Format phone number input
        formatPhoneNumber(event) {
            const phoneNumber = event.target.value.replace(/\D/g, '');
            const isBackspace = event.inputType === 'deleteContentBackward';

            let formattedPhoneNumber = '';

            if (phoneNumber.startsWith('998')) {
                if (phoneNumber.length >= 3) {
                    formattedPhoneNumber =
                        '+998 (' +
                        phoneNumber.slice(3, 5) +
                        ') ' +
                        phoneNumber.slice(5, 8) +
                        ' ' +
                        phoneNumber.slice(8, 10) +
                        ' ' +
                        phoneNumber.slice(10, 12);

                    if (isBackspace) {
                        let lastChar = formattedPhoneNumber.charAt(formattedPhoneNumber.length - 1);
                        while (lastChar === ' ' || isNaN(lastChar)) {
                            formattedPhoneNumber = formattedPhoneNumber.slice(0, -1);
                            lastChar = formattedPhoneNumber.charAt(formattedPhoneNumber.length - 1);
                        }
                        this.notFound = '';
                    }
                }

                event.target.value = formattedPhoneNumber || phoneNumber;
            } else {
                event.target.value = '+998 (' + phoneNumber;
            }

            // Save the formatted phone number
            this.phone_number = phoneNumber;

            // Make a login request when the phone number is complete
            if (this.phone_number.length === 12) {
                axios.post('/api/login', {
                    phone_number: this.phone_number,
                }).then(response => {
                    if (response.data.name && response.data.surname) {
                        this.count++;
                        if (this.count < 5) {
                            this.name = response.data.name;
                            this.surname = response.data.surname;
                            this.notFound = '';
                            this.isAuth = true;
                            this.authAction = 'Войти';
                        } else {
                            this.limit = true;
                            this.name = '';
                            this.surname = '';
                        }
                    }
                }).catch(error => {
                    this.notFound = error.response.data.message;
                    this.authAction = 'Создать';
                    this.auth = true;
                });
            } else {
                this.name = '';
                this.surname = '';
                this.auth = false;
                this.authAction = 'Войти';
            }
        },
        // Handle authentication action
        Auth() {
            if (this.phone_number.length === 12) {
                axios.post('api/sendOTP', {
                    phone_number: this.phone_number
                }).then(() => {
                    this.$router.replace('/confirmNumber');
                    localStorage.phoneNumber = this.phone_number;
                    localStorage.isAuth = this.isAuth;
                });
            }
        },
    }
};
</script>

<template>
    <!-- Login/Registration form -->
    <div class="register">
        <div class="form">
            <!-- Top section with PRO ID logo and form -->
            <div class="top" :class="{ 'top-aqua': name }">
                <div class="entrance">
                    <div class="image">
                        <img src="/images/logo/pro_id_logo.svg" alt="">
                    </div>
                    <div class="text">
                        <p>Войдите или создайте PRO ID</p>
                    </div>
                </div>
                <!-- Main form for phone number input -->
                <form @submit.prevent class="main-form">
                    <p :class="{ 'not-found': notFound }">{{ surname }} {{ name !== '' ? name[0] + '.' : '' }}  {{ notFound }}</p>
                    <input
                        required
                        type="tel"
                        class="phone-number"
                        placeholder="+998 (--) --- -- -- "
                        @input="formatPhoneNumber"
                        maxlength="19"
                    />
                    <input
                        :class="{ 'light-button': name || auth || limit}"
                        type="submit"
                        name="phoneNum"
                        class="phone-number-button"
                        :value="authAction"
                        @click="Auth"
                        v-on:keyup.enter="Auth"
                    />
                    <p :class="{ 'recover': name || auth || limit}"><a href="#">Восстановить PRO ID.</a></p>
                </form>
                <!-- Additional text and links -->
                <div class="text">
                    <p>Продолжая использовать PRO ID, я принимаю <span><a href="#">условия оферты.</a></span></p>
                    <p>PRO ID - ключ от всех сервисов</p>
                </div>
            </div>
            <!-- Bottom section with additional link -->
            <div class="bottom d-flex justify-content-center align-items-center"  @mouseover="showPopup = true" @mouseleave="showPopup = false">
                <a href="#">
                    Что такое PRO ID
                </a>
                <img class="login_search" src="/images/icons/question.svg" alt="" width="18" height="18px">
            </div>
        </div>
    </div>

    <!-- Pop-up content -->
    <div class="popup-content d-flex justify-content-center align-items-center" v-if="showPopup">
            <p>PRO ID - ключ от всех сервисов, ключ от всех сервисов ключ от всех сервисов ключ от всех сервисов.</p>
    </div>

    <!-- Bottom part with disclaimers and support information -->
    <div class="bottom-part">
        <p class="d-none">Используйте режим инкогнито на чужом компьютере </p>
        <p class="footer_mob"><span>Справка и поддержка </span><span>©2021-2023 PRO GROUP</span></p>
    </div>

    <!-- Placeholder for reCAPTCHA integration -->
    <div id="recaptcha-container" ref="recaptcha"></div>
</template>

<style scoped>
@import '/public/style/login.css';
</style>
