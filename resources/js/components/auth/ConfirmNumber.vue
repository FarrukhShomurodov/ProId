<script>
import router from '../../router';

export default {
    data() {
        return {
            otp: Array(6).fill(''),
            showCorrectSignal: false,
            phoneNumber: '',
            phoneNumberForSend: '',
            isAuth: ''
        };
    },
    mounted() {
        if (localStorage.phoneNumber && localStorage.isAuth) {
            this.phoneNumber = localStorage.phoneNumber;
            this.phoneNumberForSend = localStorage.phoneNumber;
            this.isAuth = localStorage.isAuth;
            localStorage.removeItem('phoneNumber');
            localStorage.removeItem('isAuth');
        }

        this.phone();
        const error = this.$refs.error
        this.$refs.otcInput[0].focus();
    },
    methods: {
        handleInput(index) {
            if (index < 6) {
                if (this.otp[index - 1] !== '' && this.otp[index] === '') {
                    this.$refs[`otcInput`][index].focus();
                }
            }
        },
        handleKeyDown(index) {
            if (index > 1 && this.otp[index - 1] === '' && event.key === 'Backspace') {
                this.$refs[`otcInput`][index - 2].focus();
            } else if (index === 6 && event.key >= '0' && event.key <= '9') {
                this.otp[index - 1] = event.key;
                event.preventDefault();
            }
        },
        verify() {
            if (this.showCorrectSignal) {
                if (this.isAuth === "true") {
                    axios.post('/api/token', {
                        phone_number: this.phoneNumberForSend
                    }).then((response)=>{
                        localStorage.token = response.data.token
                        // axios.delete('/api/deleteCode', {
                        //     phone_number: this.phoneNumberForSend
                        // }).then(res => console.log(res))
                        router.push({path: '/dashboard'});
                    })
                } else {
                    this.$router.push('/register');
                    localStorage.phoneNumber = this.phoneNumberForSend;
                }
            }
        },
        reSend() {
            axios.post('api/sendOTP', {
                phone_number: this.phoneNumberForSend
            }).then(() => {
                this.$router.replace('/confirmNumber')
                localStorage.phoneNumber = this.phone_number;
                localStorage.isAuth = this.isAuth;
            })
        },
        phone() {
            const countryCode = this.phoneNumber.slice(0, 3);
            const operatorCode = this.phoneNumber.slice(3, 5);
            const middleDigits = this.phoneNumber.slice(6, 9);
            const lastDigits = this.phoneNumber.slice(-2);

            const formattedPhoneNumber = `+${countryCode} ${operatorCode} *** ** ${lastDigits}`;
            this.phoneNumber = formattedPhoneNumber
        }
    },
    watch: {
        otp: {
            handler: function (newVal, oldVal) {
                const allDigitsFilled = newVal.every(digit => digit !== '');
                if (allDigitsFilled) {
                    const code = this.otp.join('');
                    if (code.length === 6) {
                        axios.post('api/checkCode', {
                            phoneNumber: this.phoneNumberForSend,
                            code: parseInt(code, 10)
                        }).then(() => {
                            this.showCorrectSignal = true
                        }).catch(err => {
                            this.showCorrectSignal = false

                        })
                    } else {
                        this.showCorrectSignal = false
                    }

                }
            },
            deep: true,
        },
    },
};

</script>

<template>
    <div class="page" id="verify">
        <div class="card">
            <div class="card__i">
                <img src="/images/logo/pro_id_logo.svg" alt="" srcset="">
            </div>
            <div class="card__txt">
                <span>Введите код из смс.<br> Мы отправили его <br>на номер {{ this.phoneNumber }}</span>
            </div>
            <div class="otc-form">
                <form @submit.prevent class="otc" name="one-time-code" action="#">
                    <img class='phone' src="/images/icons/phone.png" style="width: 21px; height: 21px" alt="" srcset="">
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
                    <img v-if="showCorrectSignal" src="/images/icons/correct-signal.svg" class="correct-signal" alt="">
                </form>
                <button @click="verify" v-on:keyup.enter="verify" type="submit" class="button next" :class="{'true_next': showCorrectSignal}">Далее</button>
                <button @click="reSend" class="reSend">отправить код еще раз</button>
            </div>
            <p class="card__paragraph">Продолжая использовать PRO ID,<br>я принимаю <a href="#"> условия оферты.</a></p>
            <p class="card__paragraph">PRO ID - ключ от всех сервисов</p>
        </div>
        <div class="sub-section">
            <button>Что такое PRO ID?</button>
        </div>
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
