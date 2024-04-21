<script>
// Importing necessary modules
import axios from 'axios';

export default {
    // Component properties
    props: [
        'banking_id'
    ],
    emits: ['close'],

    // Component data initialization
    data() {
        return {
            // Data properties for the component
            name: '',
            name_of_banking_akkaunt: '',
            mfo: '',
            payment_account: '',
            loading: false,
            show: false,
            error: ''
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.show = true
        // Fetch banking data on component mount
        axios.get(`/api/bank-data/${this.banking_id}`).then(res => {
            // Populate data from the API response
            const data = res.data;
            this.name = data.name
            this.name_of_banking_akkaunt = data.name_of_banking_akkaunt
            this.mfo = data.mfo;
            this.payment_account = data.payment_account;
            this.loading = true
        });
    },
    // Component methods
    methods: {
        // Method to save banking data
        save() {
            const banks_data = {
                'mfo': this.mfo,
                'payment_account': this.payment_account,
                'name': this.name,
                'name_of_banking_akkaunt': this.name_of_banking_akkaunt
            }
            // Update banking data via API
            axios.put(`/api/bank-data/${parseInt(this.banking_id)}`, banks_data).then(res => {
                this.$emit('close'); // Close the modal after successful data update
            }).catch(err => {
                this.error = err.response.data.message;
            });
        },
        // Method to fetch bank data based on MFO
        mfoBank() {
            axios.get('/api/bank-data-by-mfo').then(res => {
                // Find and set the bank name based on MFO
                for (let i = 0; i < res.data.length; i++) {
                    if (res.data[i].code == this.mfo) {
                        this.name = res.data[i].name
                    }
                }
            })
        }
    }
};
</script>

<template>
    <div>
        <!-- Modal transition -->
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <!-- Modal container for updating banking data -->
                        <div class="modal-container modal-container-bank-box-office" v-if="loading">
                            <!-- Header of the modal -->
                            <div class="header_modal">
                                <h4>Изменение банковского счета</h4>
                                <img
                                    src="/images/icons/dashboard/exit.svg"
                                    @click="$emit('close')"
                                    alt="exit icon"
                                />
                            </div>
                            <!-- Main content of the modal -->
                            <div class="create-business-content">
                                <!-- Display error message if any -->
                                <div class="error">
                                    <p>{{ error }}</p>
                                </div>
                                <!-- Input fields for banking data -->
                                <div>
                                    <label>Наиминование счета *</label>
                                    <input type="text" v-model=name_of_banking_akkaunt class="form_input "
                                           :class="{'form_input_error': error && name_of_banking_akkaunt.length === 0}"
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
                                    <input type="number" v-model=payment_account class="form_input "
                                           :class="{'form_input_error': error && payment_account.length === 0}"
                                           placeholder="Введите Расчетный счет" required>
                                </div>
                                <div>
                                    <input type="text" v-model=name class="form_input "
                                           :class="{'form_input_error': error && name.length === 0}"
                                           placeholder="Наимонование банка" readonly>
                                </div>
                            </div>
                            <!-- Modal footer with Save button -->
                            <div class="modal-footer">
                                <slot name="footer">
                                    <button class="modal-default-button" @click="save">
                                        Сохранить
                                    </button>
                                </slot>
                            </div>
                        </div>
                    </transition>
                    <!-- Loading indicator -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
</style>

