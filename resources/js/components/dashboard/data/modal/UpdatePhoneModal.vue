<script>
import axios from 'axios';

export default {
    props:['phoneNumber', 'userId'],
    data(){
        return{
            otp: Array(6).fill(''),
            changeNum: false,
            phoneNumberToPaste: '',
            codeLength: 0,
            showConfirmNumber: false,
            showCorrectSignal: false,
            changedPhoneNumber: '',
        }
    },
    mounted(){
        this.phoneNumberToPaste = this.phoneNumber
        this.phone()
    },
    methods:{
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
        phone() {
            const countryCode = this.phoneNumberToPaste.slice(0, 3);
            const operatorCode = this.phoneNumberToPaste.slice(3, 5);
            const middleDigits = this.phoneNumberToPaste.slice(6, 9);
            const lastDigits = this.phoneNumberToPaste.slice(-2);

            const formattedPhoneNumber = `+${countryCode} ${operatorCode} *** ** ${lastDigits}`;
            this.phoneNumberToPaste = formattedPhoneNumber
        },
        formatPhoneNumber(event) {
            const phoneNumber = event.target.value.replace(/\D/g, '');
            const isBackspace = event.inputType === 'deleteContentBackward';

            let formattedPhoneNumber = '';

            if (phoneNumber.startsWith('998')) {
                if (phoneNumber.length >= 3) {
                    formattedPhoneNumber =
                        '+998 ' +
                        phoneNumber.slice(3, 5) +
                        ' ' +
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
                event.target.value = '+998 ' + phoneNumber;
            }
            this.codeLength = phoneNumber.length
            this.changedPhoneNumber = phoneNumber
        },
        edit(){
            this.changeNum = true
            this.codeLength == 13 || this.codeLength == 12 ? this.showConfirmNumber = true : this.showConfirmNumber = false;
            if(this.showConfirmNumber){
                this.changeNum = false
            }
            if(this.showCorrectSignal){
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };
                axios.post(`/api/change-phone-number/${this.userId}`, {
                    phone_number: this.changedPhoneNumber
                },{headers}).then((res) => console.log(res))
            }
        },
        save(){
            if(this.showCorrectSignal){
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };
                axios.post(`/api/change-phone-number/${this.userId}`, {
                    phone_number: this.changedPhoneNumber
                },{headers}).then(() => $emit('close'))
            }
        },
        watch: {
            otp: {
                handler: function (newVal, oldVal) {
                    const allDigitsFilled = newVal.every(digit => digit !== '');
                    if (allDigitsFilled) {
                        const code = this.otp.join('');
                        if (code.length === 6) {
                            axios.post('/api/checkCode', {
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
    }
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-phone-number-edit">
                        <div class="header_modal">
                            <h3 class="tel">Телефон</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="edit_phone_content" v-if="!changeNum && !showConfirmNumber">
                            <h3>+{{ phoneNumber }}</h3>
                            <p>Этот номер может быть использован для<br> входа и восстановления доступа к аккаунту</p>
                        </div>
                        <input
                            required
                            type="tel"
                            class="phone-number"
                            placeholder="+998 -- --- -- -- "
                            @input="formatPhoneNumber"
                            maxlength="19"
                            v-if="changeNum"
                        />
                        <div class="otc " v-if="showConfirmNumber">
                            <p>
                                Введите код из смс.<br>
                                Мы отправили его на<br>
                                номер {{ phoneNumberToPaste }}
                            </p>
                            <div class="conf_num">
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
                            </div>

                            <button @click="reSend" class="reSend">отправить код еще раз</button>
                        </div>
                        <div class="modal-footer">
                            <button class="changeNumber modal-default-button" @click="edit">
                                    Заменить на новый номер
                            </button>
                            <slot name="footer">
                                <button class="modal-default-button" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
.modal-container-phone-number-edit{
    width: 512px;
    height: 411px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-direction: column;
    padding: 20px;
    background: #FFF;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    border-radius: 40px;
}
.modal-default-button{
    margin-top: 10px;
}
.modal-footer{
    justify-content: center;
}
.changeNumber{
    width: 437px;
    height: 47px;
    border-radius: 9px;
    background: #FFF !important;
    border: none;
    font-size: 23px;
    color: #000 !important;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
}
.edit_phone_content p{
    color: #000;
    font-size: 21px;
    margin-top: 30px;
}
.tel{
    font-weight: 500;
    font-size: 24px;
}
.phone-number{
    width: 438px;
    height: 61px;
    border-radius: 15px;
    background: #FFF;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    border:none;
    font-size: 28px;
    padding-left: 25px;
    color: #000;
}
.reSend {
    color: #545454;
    text-align: center;
    font-family: 'GT Walsheim Pro', serif;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: normal;
    border: none;
    background-color: #FFF;
}
.otc {
    display: flex;
    justify-content: space-between;
    flex-direction: column;
    align-items: center;
    max-width: 100%;
    margin: 0 auto;
}
.otc p{
    color: #000;
    text-align: center;
    font-size: 19px;
}
.conf_num{
    display: flex;
    justify-content: space-between;
    flex-direction: row;
}
.correct-signal{
    margin-left: 10px;
    margin-top: -13px;
}
.otc input {
    width: 37px;
    height: 30px;
    font-size: 20px;
    margin-left: 9px;
    text-align: center;
    justify-content: center;
    border-radius: 9px;
    border: 0.8px solid #FFF;
    background: #FFF;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    margin-bottom: 10px;
}

.otc input:focus {
    outline: none;
    box-shadow: 0 0 7px 0 #5FE0D8;
}
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

@media screen and (max-width: 500px){
    .modal-container-phone-number-edit{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
    .phone-number{
        width: 380px;
    }
}
</style>
