<script>
import axios from 'axios';

export default {
    props: [
        'business_id'
    ],
    emits: ['close'],
    data() {
        return {
            mfo: '',
            payment_account: '',
            name: '',
            name_of_banking_akkaunt: '',
            error: '',
            show: false,
        }
    },
    mounted() {
        // Any initialization code can be placed here
        this.show = true
    },
    methods: {
        save() {
            // HTTP POST request to save bank data
            const banks_data = {
                'business_id': this.business_id,
                'name': this.name,
                'name_of_banking_akkaunt': this.name_of_banking_akkaunt,
                'mfo': this.mfo,
                'payment_account': this.payment_account,
            }
            axios.post('/api/bank-data', banks_data).then(res => {
                this.$emit('close'); // Close the modal after successful save
            }).catch(err => {
                this.error = err.response.data.message; // Display error message if save fails
            });
        },
        mfoBank() {
            // Retrieve bank data based on MFO code
            axios.get('/api/bank-data-by-mfo').then(res => {
                for (let i = 0; i < res.data.length; i++) {
                    if (res.data[i].code == this.mfo) {
                        this.name = res.data[i].name; // Set bank name based on MFO code
                    }
                }
            })
        }
    },
};
</script>

<template>
    <div>
        <!-- Modal for adding banking information -->
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                    <div class="modal-container modal-container-bank-box-office" v-show="show">
                        <div class="header_modal">
                            <h4>Добавление банковского счета</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="create-business-content">
                            <div class="error">
                                <p>{{ error }}</p> <!-- Display error message, if any -->
                            </div>
                            <div>
                                <label>Наиминование счета *</label>
                                <input type="text" v-model=name_of_banking_akkaunt
                                       :class="{'form_input_error': error && name_of_banking_akkaunt.length === 0}"
                                       class="form_input "
                                       placeholder="Введите наименование счет" required>
                            </div>
                            <div>
                                <label>МФО *</label>
                                <input type="number" v-model=mfo class="form_input "
                                       :class="{'form_input_error': error && mfo.length === 0}"
                                       placeholder="Введите МФО" @input="mfoBank" required>
                            </div>
                            <div>
                                <label>Расчетный счет *</label>
                                <input type="number" v-model=payment_account
                                       class="form_input "
                                       :class="{'form_input_error': error && payment_account.length === 0}"
                                       placeholder="Введите Расчетный счет" required>
                            </div>
                            <div>
                                <input type="text" v-model=name class="form_input "
                                       :class="{'form_input_error': error && name.length === 0}"
                                       placeholder="Наимонование банка" readonly>
                            </div>
                        </div>
                        <div class="modal-footer">
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
