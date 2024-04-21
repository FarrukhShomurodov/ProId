<script>
import axios from 'axios';

export default {
    props: ['phoneNumber'],
    data() {
        return {
            otp: Array(6).fill(''),
            changeNum: false,
            phoneNumberToPaste: '',
            codeLength: 0,
            showConfirmNumber: false,
            showCorrectSignal: false,
            changedPhoneNumber: '',
            show: false,
            error: '',
        };
    },
    mounted() {
        this.show = true
    },
    methods: {
        // Handle the input for the OTP input fields
        handleInput(index) {
            if (index < 6) {
                if (this.otp[index - 1] !== '' && this.otp[index] === '') {
                    this.$refs[`otcInput`][index].focus();
                }
            }
        },
        // Handle the keydown event for the OTP input fields
        handleKeyDown(index) {
            if (index > 1 && this.otp[index - 1] === '' && event.key === 'Backspace') {
                this.$refs[`otcInput`][index - 2].focus();
            } else if (index === 6 && event.key >= '0' && event.key <= '9') {
                this.otp[index - 1] = event.key;
                event.preventDefault();
            }
        },
        // Format the phone number for display
        phone() {
            const countryCode = this.phoneNumberToPaste.slice(0, 3);
            const operatorCode = this.phoneNumberToPaste.slice(3, 5);
            const middleDigits = this.phoneNumberToPaste.slice(6, 9);
            const lastDigits = this.phoneNumberToPaste.slice(-2);

            const formattedPhoneNumber = `+${countryCode} ${operatorCode} *** ** ${lastDigits}`;
            this.phoneNumberToPaste = formattedPhoneNumber;
        },
        // Format the entered phone number
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
                        this.error = ''
                    }
                }

                event.target.value = formattedPhoneNumber || phoneNumber;
            } else {
                event.target.value = '+998 ' + phoneNumber;
            }
            this.codeLength = phoneNumber.length;
            this.changedPhoneNumber = phoneNumber;
            this.phoneNumberToPaste = this.changedPhoneNumber;
        },

        // Edit phone number and show confirmation
        edit() {
            this.phone();
            this.changeNum = true;

            console.log(this.changedPhoneNumber)

            // Check Phone Number from db
            if (this.codeLength !== 0){
                axios.post('/api/has-user', {
                    phone_number: this.changedPhoneNumber,
                }).then((res) => {
                    console.log(res)
                    this.error = "Введенный номер уже существует."
                }).catch(() => {
                    if (this.codeLength === 13 || this.codeLength === 12) {
                        this.showConfirmNumber = true
                        this.changeNum = false;

                        axios.post('/api/sendOTP', {
                            phone_number: this.changedPhoneNumber
                        });
                    } else {
                        this.showConfirmNumber = false;
                    }
                })
            }
        },
        // Resend OTP
        reSend() {
            axios.post('/api/sendOTP', {
                phone_number: this.changedPhoneNumber
            });
        },
        // Save the edited phone number
        save() {
            if (this.showCorrectSignal) {
                axios.post("/api/edit-phone-number", {
                    phone_number: this.changedPhoneNumber
                }).then(() => this.$emit('close'));
            }
        },
    },
    watch: {
        // Watch for changes in the OTP array
        otp: {
            handler: function (newVal, oldVal) {
                const allDigitsFilled = newVal.every(digit => digit !== '');
                if (allDigitsFilled) {
                    const code = this.otp.join('');
                    if (code.length === 6) {
                        axios.post('/api/checkCode', {
                            phoneNumber: this.changedPhoneNumber,
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
    <div>
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <div class="modal-container update_phone_number_container" v-show="show">
                            <div class="header_modal">
                                <h3 class="tel">Телефон</h3>
                                <img
                                    src="/images/icons/dashboard/exit.svg"
                                    @click="$emit('close')"
                                    alt="exit icon"
                                />
                            </div>
                            <div class="update_phone_modal_error">
                                <p>{{ error }}</p>
                            </div>
                            <!-- Content for editing phone number -->
                            <div class="edit_phone_content" v-if="!changeNum && !showConfirmNumber">
                                <h3>{{ phoneNumber }}</h3>
                                <p>Этот номер может быть использован для<br> входа и восстановления доступа к аккаунту
                                </p>
                            </div>
                            <!-- Input field for editing phone number -->
                            <input
                                required
                                type="tel"
                                class="form_input phone-number"
                                placeholder="+998 -- --- -- -- "
                                @input="formatPhoneNumber"
                                maxlength="19"
                                v-if="changeNum"
                                :class="{'form_input_error': error}"
                            />
                            <!-- OTP input fields and confirmation message -->
                            <div class="otc " v-if="showConfirmNumber">
                                <p class="text_phone_edit">
                                    Введите код из смс.<br>
                                    Мы отправили его на<br>
                                    номер {{ phoneNumberToPaste }}
                                </p>
                                <div class="conf_num">
                                    <img class='phone' src="/images/icons/phone.png" style="width: 21px; height: 21px"
                                         alt="" srcset="">
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
                                <button @click="reSend" class="reSend resend_from_update_phone_number">отправить код еще
                                    раз
                                </button>
                            </div>
                            <!-- Footer buttons -->
                            <div class="modal-footer">
                                <button class="changeNumber modal-default-button" v-show="!showConfirmNumber"
                                        @click="edit">
                                    Заменить на новый номер
                                </button>
                                <slot name="footer">
                                    <button class="modal-default-button" @click="save">
                                        Сохранить
                                    </button>
                                </slot>
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
