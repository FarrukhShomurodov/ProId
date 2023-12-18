    <script>
    import axios from 'axios';


    export default {
        data() {
            return {
                name: '',
                surname: '',
                notFound: '',
                authAction: 'Войдите или создайте PRO ID',
                auth: false,
                isAuth: false,
                phone_number: '',
                count: 0,
                limit: false,
                phoneNumber: '',
            };
        },
        methods: {
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
        }
    };
    </script>
    <template>
        <div class="login">
            <div class="form">
                <div class="top" >
                    <div class="image">
                        <img src="/images/logo/pro_id_logo.svg" alt="">
                    </div>
                    <div>
                        <a href="" class="link">Что такое PRO ID <img src="/images/icons/question.svg"></a>
                    </div>
                </div>
                <div class="bottom">
                    <div class="enterence">
                        <div class="text">
    <!--                        <p>Good Look</p>-->
                            <p>Вход c помощью PRO ID</p>
                        </div>
                        <div class="image">
                            <img src="/images/logo/pro_id_logo_mini.svg" alt="">
                        </div>
                    </div>

                    <form @submit.prevent class="main-form">
                        <p :class="{ 'not-found': notFound }">{{ surname }} {{ name !== '' ? name[0] + '.' : '' }} {{ notFound }}</p>
                        <input v-model="phoneNumber" required type="text" class="phone-number"
                               placeholder="+998 (--) --- -- -- " @input="formatPhoneNumber" maxlength="19">
                        <input @click="Auth" v-on:keyup.enter="Auth" type="submit" name="phoneNum"
                               :class="{ 'light-button': name || auth || limit}" class="phone-number-button" :value="authAction">
                        <p :class="{ 'revocer': name || auth}">Восстановить PRO ID.</p>
                    </form>
                    <div class="text">
                        <p>Продолжая использовать PRO ID, я принимаю <span>условия оферты.</span></p>
                        <p>PRO ID - ключ от всех сервисов</p>
                    </div>
                </div>
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
