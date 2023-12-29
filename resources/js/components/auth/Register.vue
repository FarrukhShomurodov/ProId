<script>
import router from "../../router.js";

export default {
    data() {
        return {
            phoneNumber: '',
            name: '',
            surname: ''
        }
    },
    mounted() {
        // Populate phoneNumber if available in localStorage
        if (localStorage.phoneNumber) {
            this.phoneNumber = localStorage.phoneNumber;
            localStorage.removeItem('phoneNumber');
        }
    },
    methods: {
        // Handle user registration
        register() {
            axios.post('api/register', {
                phone_number: this.phoneNumber,
                name: this.name,
                surname: this.surname,
            }).then((res) => {
                // Save the token and navigate to the dashboard
                localStorage.token = res.data.token;
                // Auth Header
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };

                // Set auth header in axios methods
                axios.defaults.headers.common = headers;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

                router.push({path: '/dashboard'});
            }).catch(err => {
                console.log(err);
            })
        }
    }
}
</script>

<template>
    <!-- Registration form -->
    <div class="register">
        <div class="form">
            <div class="top">
                <!-- PRO ID logo and form header -->
                <div class="entrance">
                    <div class="image">
                        <img src="/images/logo/pro_id_logo.svg" alt="">
                    </div>
                    <div class="text">
                        <p>Введите данные</p>
                    </div>
                </div>
                <!-- Main form for user registration -->
                <form @submit.prevent class="main-form">
                    <!-- Input fields for name and surname -->
                    <label for="firstName" style="font-size: 16px">Ваше имя</label>
                    <input required v-model="name" type="text" id="firstName" placeholder="Имя">

                    <label for="lastName" style="font-size: 16px">Ваша фамилия</label>
                    <input required v-model="surname" type="text" id="lastName" placeholder="Фамилия">

                    <!-- Submit button for registration -->
                    <input type="submit" name="phoneNum" class="phone-number-button" value="Далее" @click="register" v-on:keyup.enter="register">
                </form>
                <!-- Additional text and links -->
                <div class="text">
                    <p>Продолжая использовать PRO ID, я принимаю <span>условия оферты.</span></p>
                    <p>PRO ID - ключ от всех сервисов</p>
                </div>
            </div>
            <!-- Bottom section with additional link -->
            <div class="bottom">
                <a href="">Что такое PRO ID?</a>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "/public/style/dataRegister.css";
</style>
