<script>
// Importing necessary modules and components
import axios from 'axios';

// Importing the child component for creating banks data modal
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";

export default {
    props: [
        'business_id'
    ],
    emits: ['close'],
    components: {
        createBanksDataModal,
    },
    data() {
        return {
            // Vue.js data properties
            name: '',
            service: 'Сервис Cloud',
            bank_data_id: 1,
            bankData: [],
            showAddBankingModal: false,
            show: false,
            error: '',
        }
    },
    mounted() {
        this.show = true;

        // Mounting phase tasks
        $('#select').select2();
        $('#select2').select2();

        // Fetch initial bank data
        this.getBankData();
    },
    methods: {
        // Async function to fetch bank data
        async getBankData() {
            try {

                // Fetch bank data from the API
                const banksResponse = await axios.get(`/api/bank-data-by-business/${this.business_id}`);

                // Update Vue.js data property with fetched data
                this.bankData = banksResponse.data;

                // Mounting phase tasks
                $('#select').select2();
                $('#select2').select2();

            } catch (error) {
                console.error("Error fetching banking data:", error);
            }
        },
        // Save function to handle the submission of data
        save() {
            this.bank_data_id = parseInt($('#select2').val());
            this.service = $('#select').val();

            const box_office_data = {
                'business_id': this.business_id,
                'name': this.name,
                'service': this.service,
                'bank_data_id': this.bank_data_id
            }

            // HTTP POST request to save box office data
            axios.post('/api/box-office', box_office_data).then(res => {
                this.$emit('close'); // Close the modal after successful save
            }).catch(err => {
                this.error = err.response.data.message; // Display error message if save fails
            });
        },
        // Go Back method
        go_back(){
            this.showAddBankingModal = false;
            this.getBankData();

        }
    },
};
</script>

<template>
    <div v-if="!showAddBankingModal">
        <!-- Modal for creating a box office -->
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <div class="modal-container box-office-create-modal modal-container-bank-box-office"
                             v-show="show">
                            <div class="header_modal">
                                <h4>Создание кассы</h4>
                                <img
                                    src="/images/icons/dashboard/exit.svg"
                                    @click="$emit('close')"
                                    alt="exit icon"
                                />
                            </div>
                            <div class="create-box-office-content">
                                <div class="error">
                                    <p>{{ error }}</p> <!-- Display error message, if any -->
                                </div>
                                <div>
                                    <label>Наименование кассы *</label>
                                    <input type="text" v-model=name class="form_input "
                                           :class="{'form_input_error': error && name.length === 0}"
                                           placeholder="Введите наименование кассы" required>
                                </div>
                                <div>
                                    <label>Выберите Сервис *</label>
                                    <select v-model="service" class="form_input " id="select" required>
                                        <option value="Сервис Cloud">Сервис Cloud</option>
                                        <option value="Сервис Pay">Сервис Pay</option>
                                        <option value="Сервис CRM">Сервис CRM</option>
                                        <option value="Сервис Support">Сервис Support</option>
                                        <option value="Сервис Donate">Сервис Donate</option>
                                        <option value="Сервис GoodLook">Сервис GoodLook</option>
                                    </select>
                                </div>
                                <div>
                                    <label>Выберите банковский счет *</label>
                                    <select v-model="bank_data_id" class="form_input" id="select2" required>
                                        <option v-for="data in bankData" :value="data.id">{{
                                                data.name_of_banking_akkaunt
                                            }}
                                        </option>
                                    </select>
                                </div>
                                <!-- Add new banking option -->
                                <div class="add_bank" @click="showAddBankingModal = true" style="
                                margin-top: 15px;
                                margin-bottom: 5px;
                                max-width: 438px;
                                height: 42px;
                                border-radius: 20px;
                                background: #F3F3F3;
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                padding-left: 11px;
                                cursor: pointer;">
                                    <img src="/images/icons/dashboard/add.svg" alt="" style="margin-right: 11px;">
                                    <p>Добавить новый банковский счет</p>
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
    <!-- Display createBanksDataModal component when showAddBankingModal is true -->
    <createBanksDataModal v-if="showAddBankingModal" @close="go_back"
                          :business_id="this.business_id"></createBanksDataModal>
</template>

<style scoped>
</style>
