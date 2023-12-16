<script>
import axios from 'axios';


export default {
    props: {
        requestState: {
            type: String,
            required: true
        },
        clientId: {
            type: [String, Number],
            required: true
        },
        authToken: {
            type: String,
            required: true
        },
        clientName: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            name: '',
            surname: '',
            notFound: '',
            authAction: 'Войдите или создайте PRO ID',
            auth: false,
            isAuth: false,
            phone_number: '',
            oauth: false,
            second: 5,
            count: 0,
            limit: false
        };
    },
    mounted() {
        // timer
        this.timerInterval = setInterval(this.timer, 1000)
        if (this.second <= 0) {
            clearInterval(this.timerInterval);
            this.redirect();
        }

        // oauth
        if (this.authToken) {
            this.oauth = true

            this.getUser()
        }

    },
    methods: {
        getUser() {
            let token = localStorage.token
            const headers = {
                'Authorization': `Bearer ` + token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/user', {headers})
                .then(res => {
                    this.name = res.data.name,
                        this.surname = res.data.surname
                }).catch(err => console.log(err))
        },
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


            this.phone_number = phoneNumber;
            if (this.phone_number.length === 12) {
                axios.post('/api/login',
                    {
                        phone_number: this.phone_number,
                    }
                ).then(response => {
                    if (response.data.name && response.data.surname) {
                        this.count++
                        if(this.count < 5) {
                            this.authAction = 'Войти';
                            this.name = response.data.name;
                            this.surname = response.data.surname;
                            this.notFound = '';
                            this.auth = false;
                            this.isAuth = true;
                        }else{
                            this.authAction = 'Войти';

                            this.limit = true
                            this.name = '';
                            this.surname = '';
                        }
                    }
                })
                    .catch(error => {
                        this.authAction = 'Создать';
                        this.auth = true;
                        this.notFound = error.response.data.message;
                    });
            } else {
                this.name = '';
                this.surname = '';
                this.authAction = 'Войдите или создайте PRO ID';
                this.auth = false;
            }

        },
        Auth() {
            if (this.phone_number.length === 12) {
                axios.post('/api/sendOTP', {
                    phone_number: this.phone_number
                }).then(() => {
                    this.$router.replace('/confirmNumber')
                    localStorage.phoneNumber = this.phone_number
                    localStorage.isAuth = this.isAuth;
                })
            }
        },
        redirect() {
            axios.post('/oauth/authorize', {
                state: this.requestState,
                client_id: this.clientId,
                auth_token: this.authToken
            }).then(res => {
                console.log(res)
            }).catch(error => {
                console.error(error);
            });
        },
        timer() {
            if (this.second > 0) {
                this.second -= 1
            } else {
                clearInterval(this.timerInterval)
                this.redirect()
            }
        }
    }
};
</script>
<template>
    <div class="login">
        <div class="form">
            <div class="top" :class="{'top-none': name || notFound}">
                <div class="image">
                    <img src="/images/logo/pro_id_logo.svg" alt="">
                </div>
                <div>
                    <a href="" class="link">Что такое PRO ID <img src="/images/icons/question.svg"></a>
                </div>
            </div>
            <div class="bottom" :class="{ 'top-aqua': name, 'top-aqua-not-found': notFound }">
                <div class="enterence" :class="{'entrance_none': name || notFound}">
                    <div class="text">
                        <p>Good Look</p>
                        <p>Вход c помощью PRO ID</p>
                    </div>
                    <div class="image">
                        <img src="/images/logo/pro_id_logo_mini.svg" alt="">
                    </div>
                </div>
                <div class="log" :class="{'entrance_log': name || notFound}">
                    <div class="image">
                        <img src="/images/logo/pro_id_logo.svg" alt="">
                    </div>
                    <div class="text">
                        <p>Войдите или создайте PRO ID</p>
                    </div>
                </div>
                <form :class="{'oauth_redirect_false': oauth}" @submit.prevent class="main-form">
                    <p :class="{ 'not-found': notFound }">{{ surname }} {{ name !== '' ? name[0] + '.' : '' }} {{ notFound }}</p>
                    <input v-model="phoneNumber" required type="text" class="phone-number"
                           placeholder="+998 (--) --- -- -- " @input="formatPhoneNumber" maxlength="19">
                    <input @click="Auth" v-on:keyup.enter="Auth" type="submit" name="phoneNum"
                           :class="{ 'light-button': name || auth || limit}" class="phone-number-button" :value="authAction">
                    <div id="recaptcha-container" ref="recaptcha"></div>
                </form>

                <!-- oauth -->
                <div :class="{'oauth_redirect_true': oauth}" class="oauth_redirect">
                    <img src="/images/icons/oauthTrue.svg">
                    <p class="oauth_entrance"><span>{{ surname }} {{ name !== '' ? name[0] + '.' : '' }}</span><br> Вы успешно вошли в систему.</p>
                    <p class="redirect_timer">Через <span>{{ second }}</span> секунд вы вернетесь на сайт
                        {{ this.clientName }} </p>
                    <button class="reirect_link" @click="redirect">Перейти на сайт {{ this.clientName }}</button>
                </div>

                <div class="text">
                    <p>Продолжая использовать PRO ID,<br>я принимаю <span>условия оферты.</span></p>
                    <p>PRO ID - ключ от всех сервисов</p>
                </div>
            </div>
        </div>
        <div class="what-is-pro-id-none" :class="{'what-is-pro-id': name || notFound}">
            <a href="">Что такое PRO ID?</a>
        </div>
    </div>
    <div class="bottom-part">
        <p>Используйте режим инкогнито на чужом компьютере</p>
        <p>Справка и поддержка <span>©2021-2023 PRO GROUP</span></p>
    </div>
</template>
<style scoped>
@import '/public/style/authFromAnotherApp.css';
</style>
