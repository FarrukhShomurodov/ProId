<script>
import axios from 'axios';

export default {
    props:['userId'],
    data(){
        return{
            // State variables for OTP, email, and related functionality
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
        // Method to handle OTP input
        handleInput(index) {
            if (index < 6) {
                if (this.otp[index - 1] !== '' && this.otp[index] === '') {
                    this.$refs[`otcInput`][index].focus();
                }
            }
        },
        // Method to handle key down events for OTP input
        handleKeyDown(index) {
            if (index > 1 && this.otp[index - 1] === '' && event.key === 'Backspace') {
                this.$refs[`otcInput`][index - 2].focus();
            } else if (index === 6 && event.key >= '0' && event.key <= '9') {
                this.otp[index - 1] = event.key;
                event.preventDefault();
            }
        },
        // Method to save email and handle verification logic
        save(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            if(this.code == null){
                axios.post('/api/send-verify-code-email',{
                    email: this.email
                }, {headers}).then(res => {
                    this.code = res.data
                })
            }
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
        // Method to resend verification code
        reSend(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.post('/api/send-verify-code-email',{
                email: this.email
            }, {headers}).then(res => {
                this.code = res.data
            })
        }
    },
    watch: {
        // Watcher for OTP changes to show correct signal
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
    <!-- Main container for the email modal -->
    <div>
        <transition name="modal">
            <!-- Modal mask and wrapper -->
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <!-- Modal container for email editing -->
                    <div class="modal-container-phone-number-edit">
                        <!-- Modal header -->
                        <div class="header_modal">
                            <h3 class="tel">Почтовый яшик</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>
                        <!-- Content for entering email -->
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

                        <!-- Content for entering verification code -->
                        <div class="otc " v-show="showConfirmNumber">
                            <p>
                                Введите код из сообщения.<br>
                                Мы отправили его на почту<br>
                                {{ email }}
                            </p>
                            <div class="conf_num"  style="margin-top: 30px; width: 300px">
                                <!-- OTP input fields -->
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
                                <!-- Correct signal icon -->
                                <img v-if="showCorrectSignal" src="/images/icons/correct-signal.svg" class="correct-signal" alt="">
                            </div>
                            <div>
                                <!-- Resend and change email buttons -->
                                <button @click="reSend" class="reSend">отправить код еще раз</button>
                                <button @click="showConfirmNumber = false" class="reSend">Изменить почтовый адрес</button>
                            </div>
                        </div>
                        <!-- Footer with save and create PRO MAIL buttons -->
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
/* Styling for modal container */
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

/* Styling for default button in the modal */
.modal-default-button{
    margin-top: 10px;
}

/* Styling for modal footer */
.modal-footer{
    justify-content: center;
}

/* Styling for PRO MAIL input */
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

/* Styling for email content */
.email_content{
    display: flex;
    justify-content: space-around;
    flex-direction: column;
    width: 438px;
    height: 100%;
}

/* Styling for resend button */
.reSend{
    margin-left: 20px;
}

/* Styling for email content paragraph */
.email_content p{
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}

/* Styling for email input */
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

/* Styling for "Почтовый яшик" header */
.tel{
    font-weight: 500;
    font-size: 24px;
}

/* Styling for save PRO MAIL button */
.save_email{
    background: #AFAFAF !important;
}

/* Media query for smaller screens */
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
@media screen and (max-width: 390px) {
    .email{
        width: 320px;
    }
    .email_content{
        width: 320px;
    }
}
</style>
