<script>
// Importing necessary modules and components
import axios from 'axios';

// Importing the child component for creating banks data modal
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";

export default {
    props: [
        'business_id'
    ],
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
            addBanking: false,
            error: '',
        }
    },
    mounted() {
        // Mounting phase tasks
        $('#select').select2();

        // Capture the Vue instance
        const vm = this;

        // Select2 change event to manually update Vue.js data property
        $('#select2').select2().on('change', function () {
            vm.bank_data_id = $(this).val();
        });

        // Fetch initial bank data
        this.getBankData();
    },
    methods: {
        // Async function to fetch bank data
        async getBankData() {
            try {
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };

                // Fetch bank data from the API
                const banksResponse = await axios.get(`/api/banking-data-fetch/${this.business_id}`, {headers});

                // Update Vue.js data property with fetched data
                this.bankData = banksResponse.data;
            } catch (error) {
                console.error("Error fetching banking data:", error);
            }
        },
        // Save function to handle the submission of data
        save() {
            const box_office_data = {
                'business_id': this.business_id,
                'name': this.name,
                'service': this.service,
                'bank_data_id': this.bank_data_id
            }
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };

            // HTTP POST request to save box office data
            axios.post('/api/box-offices', box_office_data, {headers}).then(res => {
                this.$emit('close'); // Close the modal after successful save
            }).catch(err => {
                this.error = err.response.data.message; // Display error message if save fails
            });
        },
    },
    watch: {
        // Watch for changes in bank_data_id and update Select2 dropdown value
        bank_data_id(newVal) {
            $('#select2').val(newVal).trigger('change');
        },
    },
};
</script>

<template>
    <div>
        <!-- Modal for creating a box office -->
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-add-banking">
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
                                <input type="text" v-model=name class="form-input"
                                       placeholder="Введите наименование кассы" required>
                            </div>
                            <div>
                                <label>Выберите Сервис *</label>
                                <select v-model="service" class="form-input" id="select" required>
                                    <option value="Сервис Cloud">Сервис Cloud</option>
                                    <!-- ... (other options) ... -->
                                </select>
                            </div>
                            <div>
                                <label>Выберите банковский счет *</label>
                                <select v-model="bank_data_id" class="form-input" id="select2" required>
                                    <option v-for="data in bankData" :value="data.id">{{
                                            data.name_of_banking_akkaunt
                                        }}
                                    </option>
                                </select>
                            </div>
                            <!-- Add new banking option -->
                            <div class="add_bank" @click="addBanking = true" style="
                                margin-top: 15px;
                                margin-bottom: 20px;
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
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save">
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Display createBanksDataModal component when addBanking is true -->
        <createBanksDataModal v-if="addBanking" @close="$emit('close')"
                              :business_id="this.business_id"></createBanksDataModal>
    </div>
</template>

<style>
.add_bank {
    margin-top: 15px;
    margin-bottom: 15px; /* Corrected property name */
    max-width: 510px;
    height: 42px;
    border-radius: 20px;
    background: #F3F3F3;
    display: flex;
    flex-direction: row;
    align-items: center;
    padding-left: 11px;
}

.add_bank p {
    font-size: 17px;
    font-weight: 500;
    color: #000;
}

.modal-container-add-banking {
    width: 512px;
    height: 520px !important;
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

.modal-default-button {
    margin-top: 10px;
}

.modal-footer {
    justify-content: center;
}

.create-box-office-content {
    margin-left: 17px !important;
}

label {
    margin-top: 10px;
    font-size: 20px;
    margin-left: 5px;
}

.form-input {
    width: 438px;
    height: 47px;
    border-radius: 15px;
    background: #FFF;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    border: none;
    padding-left: 15px;
    font-size: 20px;
    margin-top: 10px;
}

.select2-selection {
    width: 438px;
    height: 47px !important;
    border-radius: 15px !important;
    background: #FFF;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    font-size: 20px;
    margin-top: 10px;
    padding-left: 10px;
    border: none !important;
}

.select2-container--open .select2-selection {
    border-radius: 15px 15px 0 0 !important;
    box-shadow: 0 !important;
    border: 1px solid #aaa !important;
}

.select2-selection__rendered {
    padding-top: 10px;
}

.select2-selection__arrow {
    margin-top: 20px;
    margin-right: 10px;
}

.select2-search__field {
    display: none;
}

.select2-results__option--highlighted {
    background-color: rgba(0, 0, 0, 0.25) !important;
    color: black !important;
    border-radius: 15px !important;
}

.select2-results__option {
    border-radius: 10px !important;
}

.select2-dropdown {
    border-radius: 0 0 10px 10px !important;
    z-index: 9999 !important;
}

.error {
    width: 438px;
}

.error p {
    font-size: 14px;
    text-align: center;
    color: #FF0000;
}

@media screen and (max-width: 500px) {
    .modal-container-add-banking {
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0 0;
    }

    .create-box-office-content {
        margin-left: 0 !important;
    }

    .create-box-office-content div {
        width: 380px;
    }

    .form-input {
        width: 380px;
    }

    .select2-selection {
        width: 380px !important;
    }
}
@media screen and (max-width: 390px) {
    .create-box-office-content div {
        width: 340px !important;
    }

    .select2-selection {
        width: 340px !important;
    }
}
</style>
