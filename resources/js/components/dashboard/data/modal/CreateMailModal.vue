<script>
import axios from 'axios';

export default {
    props:['userId'],
    data(){
        return{
            otp: Array(6).fill(''),
            email:'',
            showCorrectSignal: false,
            showConfirmNumber: false,
            code: null,
        }
    },
    mounted(){
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
        save(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.post('/api/send-verify-code-email',{
                email: this.email
            }, {headers}).then(res => {
                this.code = res.data
            })
            this.showConfirmNumber = true;

            if(this.code && this.showCorrectSignal){
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };
                axios.post(`/api/add-email/${this.userId}`, {
                    email: this.email
                },{headers}).then(() => {this.$emit('goBack')})
            }
        },
    },
    watch: {
        otp: {
            handler: function (newVal, oldVal) {
                const allDigitsFilled = newVal.every(digit => digit !== '');
                if (allDigitsFilled) {
                    const code = this.otp.map(digit => digit || '0').join('');
                    this.showCorrectSignal = parseInt(code) === this.code;
                }
            },
            deep: true,
        },
    },
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-phone-number-edit">
                        <div class="header_modal">
                            <h3 class="tel">Почтовый яшик</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="email_content" v-show="!showConfirmNumber">
                            <span>Введите email, который поможет восстановить доступ<br> к аккаунту</span>
                            <input
                            required
                            type="email"
                            class="email"
                            v-model=email
                            placeholder="Введите email"
                            />
                            <p>Если нету. Создайте почтовый ящик в PRO MAIL.</p>
                        </div>

                        <!--send code to Email-->
                        <div class="otc " v-show="showConfirmNumber">
                            <p>
                                Введите код из сообщения.<br>
                                Мы отправили его на почту<br>
                                {{ email }}
                            </p>
                            <div class="conf_num"  style="margin-top: 30px; width: 300px">
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
                            <div>
                                <button @click="" class="reSend">отправить код еще раз</button>
                                <button @click="" class="reSend">Изменить почтовый адрес</button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button save_email" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                            <button class="create_pro_mail modal-default-button"  @click="$emit('goBack')">
                                     Создать PRO MAIL
                            </button>
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
    height: 366px;
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
.promail{
    width: 437px;
    height: 47px;
    border-radius: 9px;
    background: #FFF !important;
    border: none;
    font-size: 23px;
    color: #000 !important;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
}
.email_content{
    display: flex;
    justify-content: space-around;
    flex-direction: column;
    width: 438px;
    height: 100%;
}
.email_content p{
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}
.email{
    width: 438px;
    height: 54px;
    border-radius: 15px;
    background: #FFF;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    border:none;
    font-size: 28px;
    padding-left: 25px;
    color: #000;
}
.tel{
    font-weight: 500;
    font-size: 24px;
}
.save_email{
    background: #AFAFAF !important;
}
@media screen and (max-width: 500px){
    .modal-container-phone-number-edit{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
    .email{
        width: 380px;
    }
    .email_content{
        width: 380px;
    }
}
</style>
