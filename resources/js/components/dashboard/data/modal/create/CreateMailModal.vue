<script>
import axios from 'axios';

export default {
    data() {
        return {
            // State variables for OTP, email, and related functionality
            otp: Array(6).fill(''),
            email: '',
            showCorrectSignal: false,
            showConfirmNumber: false,
            code: null,
            showLoadingSignal: false,
            show: false,
        }
    },
    mounted() {
        this.show = true;
    },
    methods: {
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
        save() {
            if (this.code == null) {
                this.showLoadingSignal = true;
                axios.post('/api/send-verify-code-email', {
                    email: this.email
                }).then(res => {
                    this.code = res.data
                    this.showLoadingSignal = false;
                    this.showConfirmNumber = true;
                })
            }

            if (this.code && this.showCorrectSignal) {
                axios.post("/api/add-email", {
                    email: this.email
                }).then(() => {
                    this.$emit('goBack')
                })
            }
        },
        // Method to resend verification code
        reSend() {
            axios.post('/api/send-verify-code-email', {
                email: this.email
            }).then(res => {
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
                    axios.post('/api/send-check-verify-code-email',{
                        email: this.email,
                        code: code
                    }).then(()=>{
                        this.showCorrectSignal = true;
                    })
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
        <transition name="modal-entire">
            <!-- Modal mask and wrapper -->
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <!-- Modal container for email editing -->
                        <div class="modal-container modal-container-address-phone-mail" v-show="show">
                            <!-- Modal header -->
                            <div class="header_modal">
                                <h3 class="pochta">Почтовый яшик</h3>
                                <img
                                    src="/images/icons/dashboard/exit.svg"
                                    @click="$emit('goBack')"
                                    alt="exit icon"
                                />
                            </div>
                            <!-- Content for entering email -->
                            <div class="email_content" v-show="!showConfirmNumber && !showLoadingSignal">
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

                            <div v-show="showLoadingSignal" class="loading-indicator">
                                Loading...
                            </div>
                            <!-- Content for entering verification code -->
                            <div class="otc " v-show="showConfirmNumber">
                                <p>
                                    Введите код из сообщения.<br>
                                    Мы отправили его на почту<br>
                                    {{ email }}
                                </p>
                                <div class="conf_num" style="margin-top: 30px; width: 300px">
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
                                    <img v-if="showCorrectSignal" src="/images/icons/correct-signal.svg"
                                         class="correct-signal" alt="">
                                </div>
                                <div>
                                    <!-- Resend and change email buttons -->
                                    <button @click="reSend" class="reSend">отправить код еще раз</button>
                                    <button @click="showConfirmNumber = false" class="reSend">Изменить почтовый адрес
                                    </button>
                                </div>
                            </div>
                            <!-- Footer with save and create PRO MAIL buttons -->
                            <div class="modal-footer">
                                <slot name="footer">
                                    <button class="modal-default-button save_email" @click="save">
                                        Сохранить
                                    </button>
                                </slot>
                                <button class="create_pro_mail modal-default-button" @click="$emit('goBack')">
                                    Создать PRO MAIL
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
</style>
