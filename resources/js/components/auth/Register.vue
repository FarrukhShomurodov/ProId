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
        if (localStorage.phoneNumber) {
            this.phoneNumber = localStorage.phoneNumber;
            localStorage.removeItem('phoneNumber')
        }
    },
    methods: {
        register() {
            axios.post('api/register', {
                phone_number: this.phoneNumber,
                name: this.name,
                surname: this.surname,
            }).then((res) => {
                localStorage.token = res.data.token;
                router.push({path: '/dashboard'});
            }).catch(err => {
                console.log(err)
            })
        }
    }
}
</script>

<template>
    <div class="register">
        <div class="form">
            <div class="top">
                <div class="entrance">
                    <div class="image">
                        <img src="/images/logo/pro_id_logo.svg" alt="">
                    </div>
                    <div class="text">
                        <p>Введите данные</p>
                    </div>
                </div>
                <form @submit.prevent class="main-form">
                    <label for="firstName" style="font-size: 16px">Ваше имя</label>
                    <input required v-model="name" type="text" id="firstName"
                           placeholder="Имя">

                    <label for="lastName" style="font-size: 16px">Ваша фамилия</label>
                    <input required v-model="surname" type="text" id="lastName"
                           placeholder="Фамилия">

                    <input type="submit" name="phoneNum"
                           class="phone-number-button"
                           value="Далее" @click="register"
                           v-on:keyup.enter="register">
                </form>
                <div class="text">
                    <p>Продолжая использовать PRO ID, я принимаю <span>условия оферты.</span></p>
                    <p>PRO ID - ключ от всех сервисов</p>
                </div>
            </div>
            <div class="bottom">
                <a href="">Что такое PRO ID?</a>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "/public/style/dataRegister.css";
</style>
