<script>
import router from '../../router';

export default {
    data() {
        return {
            otp: Array(6).fill(''),
            showCorrectSignal: false,
            phoneNumber: '',
            phoneNumberForSend: '',
            isAuth: '',
            showPopup: false,
        };
    },
    mounted() {
        // Retrieve saved phone number and authentication status from localStorage
        if (localStorage.phoneNumber && localStorage.isAuth) {
            this.phoneNumber = localStorage.phoneNumber;
            this.phoneNumberForSend = localStorage.phoneNumber;
            this.isAuth = localStorage.isAuth;
            localStorage.removeItem('phoneNumber');
            localStorage.removeItem('isAuth');
        }

        // Focus on the first OTP input
        this.$refs.otcInput[0].focus();
    },
    methods: {
        // Handle OTP input
        handleInput(index) {
            // Move focus to the next input when a digit is entered
            if (index < 6) {
                if (this.otp[index - 1] !== '' && this.otp[index] === '') {
                    this.$refs[`otcInput`][index].focus();
                }
            }
        },
        // Handle Backspace key to move focus backward
        handleKeyDown(index) {
            if (index > 1 && this.otp[index - 1] === '' && event.key === 'Backspace') {
                this.$refs[`otcInput`][index - 2].focus();
            } else if (index === 6 && event.key >= '0' && event.key <= '9') {
                this.otp[index - 1] = event.key;
                event.preventDefault();
            }
        },
        // Verify the entered OTP
        verify() {
            if (this.showCorrectSignal) {
                if (this.isAuth === "true") {
                    axios.post('/api/token', {
                        phone_number: this.phoneNumberForSend
                    }).then((response)=>{
                        localStorage.token = response.data.token

                        // Auth Header
                        const headers = {
                            'Authorization': `Bearer ` + localStorage.token,
                            'Content-Type': 'application/json',
                        };

                        // Set auth header in axios methods
                        axios.defaults.headers.common = headers;
                        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

                        if(response.data.redirect_url === null){
                            router.push({path: '/dashboard'});
                        }else{
                            window.location.href = response.data.redirect_url;
                        }
                    })
                } else {
                    this.$router.push('/register');
                    localStorage.phoneNumber = this.phoneNumberForSend;
                }
            }
        },
        // Resend OTP
        reSend() {
            axios.post('api/sendOTP', {
                phone_number: this.phoneNumberForSend
            }).then(() => {
                this.$router.replace('/confirmNumber')
                localStorage.phoneNumber = this.phone_number;
                localStorage.isAuth = this.isAuth;
            })
        },
        // Format and display the masked phone number
        phone() {
            const countryCode = this.phoneNumber.slice(0, 3);
            const operatorCode = this.phoneNumber.slice(3, 5);
            const middleDigits = this.phoneNumber.slice(6, 9);
            const lastDigits = this.phoneNumber.slice(-2);

            const formattedPhoneNumber = `+${countryCode} ${operatorCode} *** ** ${lastDigits}`;
            this.phoneNumber = formattedPhoneNumber;
        }
    },
    watch: {
        // Watch for changes in OTP array
        otp: {
            handler: function (newVal, oldVal) {
                const allDigitsFilled = newVal.every(digit => digit !== '');
                if (allDigitsFilled) {
                    const code = this.otp.join('');
                    if (code.length === 6) {
                        // Validate the OTP code
                        axios.post('api/checkCode', {
                            phoneNumber: this.phoneNumberForSend,
                            code: parseInt(code, 10)
                        }).then(() => {
                            this.showCorrectSignal = true;
                        }).catch(err => {
                            this.showCorrectSignal = false;
                        });
                    } else {
                        this.showCorrectSignal = false;
                    }
                }
            },
            deep: true,
        },
    },
};
</script>

<template>
    <!-- Verification page content -->
    <div class="page" id="verify">
        <div class="card">
            <!-- PRO ID logo and information -->
            <div class="card__i">
                <img src="/images/logo/pro_id_logo.svg" alt="" srcset="">
            </div>
            <div class="card__txt">
                <span>Введите код из смс.<br> Мы отправили его <br>на номер {{ this.phoneNumber }}</span>
            </div>
            <!-- OTP input form -->
            <div class="otc-form">
                <form @submit.prevent class="otc" name="one-time-code" action="#">
                    <img class='phone' src="/images/icons/phone.png" style="width: 21px; height: 21px" alt="" srcset="">
                    <!-- Display OTP input fields -->
                    <input
                        v-for="i in 6"
                        :key="i"
                        ref="otcInput"
                        v-model="otp[i-1]"
                        @input="handleInput(i)"
                        @keydown="handleKeyDown(i)"
                        type="number"
                        inputmode="numeric"
                        :placeholder="'_'"
                        :id="'otc-' + i"
                        maxlength="1"
                        required>
                    <!-- Display correct signal on successful verification -->
                    <img v-if="showCorrectSignal" src="/images/icons/correct-signal.svg" class="correct-signal" alt="">
                </form>
                <!-- Verify and resend buttons -->
                <button @click="verify" v-on:keyup.enter="verify" type="submit" class="button next" :class="{'true_next': showCorrectSignal}">Далее</button>
                <button @click="reSend" class="reSend resend_in_auth">Отправить код еще раз</button>
            </div>
            <!-- Additional information and links -->
            <p class="card__paragraph">Продолжая использовать PRO ID,<br>я принимаю <a href="#">условия оферты.</a></p>
            <p class="card__paragraph">PRO ID - ключ от всех сервисов</p>
        </div>
        <div class="sub-section d-flex justify-content-center align-items-center" @mouseover="showPopup = true" @mouseleave="showPopup =  false">
            <a href="#">Что такое PRO ID</a>
            <img class="login_search" src="/images/icons/question.svg" alt="" width="18" height="18">
        </div>

        <!-- Pop-up content -->
        <div class="popup-content d-flex justify-content-center align-items-center" v-if="showPopup">
            <p>PRO ID - ключ от всех сервисов, ключ от всех сервисов ключ от всех сервисов ключ от всех сервисов.</p>
        </div>

        <!-- Placeholder for reCAPTCHA integration -->
        <div ref="recaptcha"></div>
    </div>
</template>

<style scoped>
@import "/public/style/getcode.css";
.next{
    background-color: #333;
}
.true_next{
    background: #5FE0D8;
}
</style>
