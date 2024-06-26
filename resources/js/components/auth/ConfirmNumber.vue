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
            resend: false,
            resendTimer: 60,
            timerColor: "#000000"
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

        this.startResendTimer()
    },
    methods: {
        startResendTimer() {
            // Decrease the timer by 1 every second
            this.resendInterval = setInterval(() => {
                if (this.resendTimer > 0) {
                    this.resendTimer--;
                    if (this.resendTimer < 20) {
                        this.timerColor = "#FF5959"
                    }
                } else {
                    this.resend = true
                    clearInterval(this.resendInterval);
                }
            }, 1000);
        },
        // Handle OTP input
        handleInput(index) {
            // Restrict input to numeric characters only
            this.otp[index - 1] = this.otp[index - 1].replace(/\D/g, '');
            // Move focus to the next input when a digit is entered
            if (index < 6 && this.otp[index - 1].length > 0) {
                this.$refs[`otcInput`][index].focus();
            }
        },
        // Handle Backspace key to move focus backward
        handleKeyDown(index) {
            if (index > 1 && this.otp[index - 1] === '' && event.key === 'Backspace') {
                this.$refs[`otcInput`][index - 2].focus();
            }
        },
        // Verify the entered OTP
        verify() {
            if (this.showCorrectSignal) {
                if (this.isAuth === "true") {
                    axios.post('/api/login', {
                        phone_number: this.phoneNumberForSend
                    }).then((response) => {
                        // set access_token
                        let d = new Date();
                        d.setTime(d.getTime() + 24 * 60 * 60 * 1000);
                        let expires = "expires=" + d.toUTCString();
                        document.cookie = "accessToken=" + response.data.access_token + ";" + expires + ";path=/; SameSite=None; Secure";

                        window.axios.defaults.headers.common = {
                            'Authorization': `Bearer ` + response.data.access_token,
                        };

                        if (localStorage.getItem('users_id')) {
                            // Retrieve the array from localStorage
                            let userIds = JSON.parse(localStorage.getItem('users_id'));

                            // Push the new id into the array
                            if (!userIds.includes(response.data.user.id)) {
                                userIds.push(response.data.user.id);
                            }

                            // Set the updated array back into localStorage
                            localStorage.setItem('users_id', JSON.stringify(userIds));
                        } else {
                            // Create a new array with the id and set it into localStorage
                            localStorage.setItem('users_id', JSON.stringify([response.data.user.id]));
                        }

                        if (response.data.redirect_url === null) {
                            router.push({path: '/dashboard'});
                        } else {
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
            if (this.resend) {
                axios.post('api/sendOTP', {
                    phone_number: this.phoneNumberForSend
                }).then(() => {
                    this.timerColor = "#000000"
                    this.resendTimer = 60;
                    this.startResendTimer();
                    this.$router.replace('/confirmNumber')
                    localStorage.phoneNumber = this.phone_number;
                    localStorage.isAuth = this.isAuth;
                })
            }
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

                        axios.post('/api/checkCode', {
                            phone_number: this.phoneNumberForSend,
                            code: parseInt(code, 10)
                        }, {
                            headers: {
                                'Accept': ' application/json '
                            }
                        }).then((res) => {
                            this.showCorrectSignal = !!res.data.status;
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
            <span class="redirect_timer resend_timer" :style="{ color: timerColor }">{{ this.resendTimer }}</span>
            <!-- OTP input form -->
            <div class="otc-form">
                <form class="otc" name="one-time-code" action="#">
                    <img class='phone' src="/images/icons/phone.png" style="width: 21px; height: 21px" alt="" srcset="">
                    <!-- Display OTP input fields -->
                    <input
                        v-for="i in 6"
                        :key="i"
                        ref="otcInput"
                        v-model="otp[i-1]"
                        @input="handleInput(i)"
                        @keydown="handleKeyDown(i)"
                        type="text"
                        inputmode="numeric"
                        :placeholder="'_'"
                        :id="'otc-' + i"
                        maxlength="1"
                        required>
                    <!-- Display correct signal on successful verification -->
                    <img v-if="showCorrectSignal" src="/images/icons/correct-signal.svg" class="correct-signal" alt="">
                </form>
                <!-- Verify and resend buttons -->
                <button @click="verify" v-on:keyup.enter="verify" type="submit" class="button next"
                        :class="{'true_next': showCorrectSignal}">Далее
                </button>
                <button @click="reSend" class="reSend resend_in_auth">Отправить код еще раз</button>
            </div>
            <!-- Additional information and links -->
            <p class="card__paragraph">Продолжая использовать PRO ID,<br>я принимаю <a href="#">условия оферты.</a></p>
            <p class="card__paragraph">PRO ID - ключ от всех сервисов</p>
        </div>
        <div class="sub-section d-flex justify-content-center align-items-center" @mouseover="showPopup = true"
             @mouseleave="showPopup =  false">
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

.next {
    background-color: #333;
}

.true_next {
    background: #5FE0D8;
}
</style>
