<script>
import router from "../../router.js";

export default {
    data() {
        return {
            phoneNumber: '',
            name: '',
            surname: '',
            showPopup: false,
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
                // set access_token
                let d = new Date();
                d.setTime(d.getTime() + 24 * 60 * 60 * 1000);
                let expires = "expires=" + d.toUTCString();
                document.cookie = "accessToken=" + res.data.access_token + ";" + expires + ";path=/; SameSite=None; Secure";

                window.axios.defaults.headers.common = {
                    'Authorization': `Bearer ` + res.data.access_token,
                };

                // Auth Header
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };

                // Set auth header in axios methods
                axios.defaults.headers.common = headers;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

                if (localStorage.getItem('users_id')) {
                    // Retrieve the array from localStorage
                    let userIds = JSON.parse(localStorage.getItem('users_id'));

                    // Push the new id into the array
                    userIds.push(res.data.user.id);

                    // Set the updated array back into localStorage
                    localStorage.setItem('users_id', JSON.stringify(userIds));
                } else {
                    // Create a new array with the id and set it into localStorage
                    localStorage.setItem('users_id', JSON.stringify([response.data.user.id]));
                }

                if (res.data.redirect_url === null) {
                    router.push({path: '/dashboard'});
                } else {
                    window.location.href = res.data.redirect_url;
                }
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
                    <input type="submit" name="phoneNum" class="phone-number-button" value="Далее" @click="register"
                           v-on:keyup.enter="register">
                </form>
                <!-- Additional text and links -->
                <div class="text">
                    <p>Продолжая использовать PRO ID, я принимаю <span><a href="#">условия оферты.</a></span></p>
                    <p>PRO ID - ключ от всех сервисов</p>
                </div>
            </div>
            <!-- Bottom section with additional link -->
            <div class="bottom d-flex justify-content-center align-items-center" @mouseover="showPopup = true"
                 @mouseleave="showPopup = false">
                <a href="#">
                    Что такое PRO ID
                </a>
                <img class="login_search" src="/images/icons/question.svg" alt="" width="18" height="18">
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
</template>

<style scoped>
@import "/public/style/dataRegister.css";
</style>
