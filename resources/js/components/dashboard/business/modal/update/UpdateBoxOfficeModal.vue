<script>
// Importing necessary modules and component
import axios from 'axios';
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";

export default {
    // Component properties
    props: [
        'box_office_id',
        'business_id'
    ],
    emits: ['close'],

    components: {
        createBanksDataModal,
    },
    // Component data initialization
    data() {
        return {
            // Data properties for the component
            name: '',
            service: 'Сервис Cloud',
            bank_data_id: '',
            dankData: [],
            showAddBankingModal: false,
            active: false,
            show: false,
            loading: false,
            error: '',
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.show = true

        // Fetch box office and bank data
        this.getboxOffice();
        this.getBankData();
    },
    // Component methods
    methods: {
        // Method to fetch box office data
        async getboxOffice() {
            const boxOfficeResponse = await axios.get(`/api/box-offices-show/${this.box_office_id}`);
            // Populate data from the API response
            this.service = boxOfficeResponse.data.service
            this.name = boxOfficeResponse.data.name
            this.active = boxOfficeResponse.data.isActive === 1;
            this.bank_data_id = boxOfficeResponse.data.bank_data_id

            $('#select').val(this.service).trigger('change');
            $('#select2').val(this.bank_data_id).trigger('change');
        },
        // Method to fetch bank data
        async getBankData() {
            try {
                const banksResponse = await axios.get(`/api/banking-data-fetch/${this.business_id}`);

                // Assuming the response contains an array of objects with 'id' and 'name_of_banking_akkaunt' properties
                this.dankData = banksResponse.data;

                // Initialize Select2 plugin for dropdowns
                $('#select').select2();
                $('#select2').select2();

                this.loading = true
            } catch (error) {
                console.error("Error fetching banking data:", error);
            }
        },
        // Method to save box office data
        save() {
            this.bank_data_id = parseInt($('#select2').val())
            const box_office_data = {
                'name': this.name,
                'service': this.service,
                'bank_data_id': this.bank_data_id
            }
            axios.put(`/api/box-offices/${this.box_office_id}`, box_office_data).then(() => this.$emit('close')).catch(err => {
                this.error = err.response.data.message;
            });
        },
        // Method to deactivate box office
        disActivate() {
            axios.get(`/api/box-offices-disActivate/${this.box_office_id}`).then(() => this.$emit('close'))
        },
        // Method to activate box office
        activate() {
            axios.get(`/api/box-offices-activate/${this.box_office_id}`).then(() => this.$emit('close'))
        },
        // Method to delete box office
        destroy() {
            axios.delete(`/api/box-offices/${this.box_office_id}`).then(() => this.$emit('close'))
        },
        // Go Back method
        goBack(){
            this.showAddBankingModal = false;

            // Fetch box office and bank data
            this.getboxOffice();
            this.getBankData();
        }
    },
};
</script>

<template>
    <div v-if="!showAddBankingModal">
        <!-- Modal transition -->
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <!-- Modal container for updating banking data -->
                        <div class="modal-container box-office-update-modal modal-container-bank-box-office"
                             v-show="loading">
                            <!-- Header of the modal -->
                            <div class="header_modal">
                                <h4>Изменение кассы</h4>
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
                                <!-- Input fields for box office data -->
                                <div>
                                    <label>Наименование кассы *</label>
                                    <input type="text" v-model=name class="form_input "
                                           :class="{'form_input_error': error && name.length === 0}"
                                           placeholder="Введите наименование кассы" required>
                                </div>
                                <div>
                                    <label>Выберите Сервис *</label>
                                    <select v-model="service" class="form_input form_input-business-bank-box-office"
                                            id="select" required>
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
                                    <select v-model="bank_data_id"
                                            class="form_input form_input-business-bank-box-office"
                                            id="select2" required>
                                        <option v-for="data in dankData" :value="data.id">{{
                                                data.name_of_banking_akkaunt
                                            }}
                                        </option>
                                    </select>
                                </div>
                                <!-- Button to add new banking data -->
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
                                padding-left: 11px;">
                                    <img src="/images/icons/dashboard/add.svg" alt="" style="margin-right: 11px;">
                                    <p>Добавить новый банковский счет</p>
                                </div>
                            </div>
                            <!-- Modal footer with action buttons -->
                            <div class="modal-footer">
                                <div class="d-flex">
                                    <!-- Button to activate box office -->
                                    <button class="unactive modal-default-button " @click="activate" v-if="!active">
                                        Активировать
                                    </button>
                                    <!-- Button to deactivate box office -->
                                    <button class="unactive modal-default-button " @click="disActivate" v-if="active">
                                        Деактивировать
                                    </button>
                                    <!-- Button to delete box office -->
                                    <button class="modal-default-button delete" @click="destroy">
                                        Удалить
                                    </button>
                                </div>
                                <!-- Save button -->
                                <slot name="footer">
                                    <button class="modal-default-button" @click="save">
                                        Сохранить
                                    </button>
                                </slot>
                            </div>
                        </div>
                    </transition>
                    <!-- Loading indicator while data is being fetched -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
    <!-- Render the createBanksDataModal component if addBanking is true -->
    <createBanksDataModal v-if="showAddBankingModal" @close="goBack"
                          :business_id="this.business_id"></createBanksDataModal>
</template>

<style scoped>
@media screen and (max-width: 500px) {
    .unactive {
        width: 180px !important;
    }
}
</style>
