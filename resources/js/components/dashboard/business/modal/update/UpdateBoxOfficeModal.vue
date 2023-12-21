<script>
// Importing necessary modules and component
import axios from 'axios';
import createBanksDataModal from "@/components/dashboard/business/modal/create/createBanksDataModal.vue";

export default {
    // Component properties
    props:[
        'box_office_id',
        'business_id'
    ],
    components:{
        createBanksDataModal,
    },
    // Component data initialization
    data(){
        return{
            // Data properties for the component
            name: '',
            service: 'Сервис Cloud',
            bank_data_id: 1,
            dankData: [],
            addBanking: false,
            active: false,
            error: '',
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        // Initialize Select2 plugin for dropdowns
        $('#select').select2();

        // Capture the Vue instance
        const vm = this;

        // Change select value from string to integer "id"
        $('#select2').select2().on('change', function () {
            // Manually update Vue.js data property
            vm.bank_data_id = $(this).val();
        });

        // Fetch box office and bank data
        this.getboxOffice();
        this.getBankData();
    },
    // Component methods
    methods:{
        // Method to fetch box office data
        async getboxOffice(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            const boxOfficeResponse = await axios.get(`/api/box-offices-show/${this.box_office_id}`,{headers});
            // Populate data from the API response
            this.service = boxOfficeResponse.data.service
            this.name = boxOfficeResponse.data.name
            this.active = boxOfficeResponse.data.isActive === 1 ? true : false;
            vm.bank_data_id = boxOfficeResponse.bank_data_id
        },
        // Method to fetch bank data
        async getBankData(){
            try {
                const headers = {
                    'Authorization': `Bearer ` + localStorage.token,
                    'Content-Type': 'application/json',
                };
                const banksResponse = await axios.get(`/api/banking-data-fetch/${this.business_id}`,{headers});

                // Assuming the response contains an array of objects with 'id' and 'name_of_banking_akkaunt' properties
                this.dankData = banksResponse.data;
            } catch (error) {
                console.error("Error fetching banking data:", error);
            }
        },
        // Method to save box office data
        save(){
            const box_office_data = {
                'name': this.name,
                'service': this.service,
                'bank_data_id': this.bank_data_id
            }
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.put(`/api/box-offices/${this.box_office_id}`, box_office_data,{headers}).then( () => this.$emit('close')).catch(err => {
                this.error = err.response.data.message;
            });
        },
        // Method to deactivate box office
        disActivate(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get(`/api/box-offices-disActivate/${this.box_office_id}`,{headers}).then(() => this.$emit('close'))
        },
        // Method to activate box office
        activate(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get(`/api/box-offices-activate/${this.box_office_id}`,{headers}).then(() => this.$emit('close'))
        },
        // Method to delete box office
        destroy(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.delete(`/api/box-offices/${this.box_office_id}`,{headers}).then(() => this.$emit('close'))
        }
    },
    // Component watch property
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
        <!-- Modal transition -->
        <transition name="modal">
            <div class="modal-mask" >
                <div class="modal-wrapper">
                    <!-- Modal container for updating banking data -->
                    <div class="modal-container-update-banking">
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
                                <input type="text" v-model=name class="form-input" placeholder="Введите наименование кассы" required>
                            </div>
                            <div>
                                <label>Выберите Сервис *</label>
                                <select v-model="service" class="form-input" id="select" required>
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
                                <select v-model="bank_data_id" class="form-input" id="select2" required>
                                    <option v-for="data in dankData" :value="data.id" >{{ data.name_of_banking_akkaunt }}</option>
                                </select>
                            </div>
                            <!-- Button to add new banking data -->
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
                        <!-- Modal footer with action buttons -->
                        <div class="modal-footer">
                            <div class="d-flex">
                                <!-- Button to activate box office -->
                                <button class="modal-default-button unactive" @click="activate" v-if="!active" >
                                    Активировать
                                </button>
                                <!-- Button to deactivate box office -->
                                <button class="modal-default-button unactive" @click="disActivate" v-if="active">
                                    Деактивировать
                                </button>
                                <!-- Button to delete box office -->
                                <button class="modal-default-button delete" @click="destroy" >
                                    Удалить
                                </button>
                            </div>
                            <!-- Save button -->
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
    <!-- Render the createBanksDataModal component if addBanking is true -->
    <createBanksDataModal v-if="addBanking" @close="$emit('close')" :business_id="this.business_id"></createBanksDataModal>
</template>

<style>
/* Add Banking Data */
.add_bank{
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
.add_bank p{
    font-size: 17px;
    font-weight: 500;
    color: #000;
}
.modal-container-update-banking{
    width: 512px;
    height: 570px !important;
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
.create-business-content{
    margin-left: 17px;
}
label{
    margin-top: 10px;
    font-size: 20px;
    margin-left: 5px;
}
.form-input{
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
.unactive{
    width: 213px;
    height: 47px;
    border-radius: 9px;
    background: #AFAFAF;
}
.delete{
    border-radius: 9px;
    background: #FF5959;
    width: 213px;
    height: 47px;
    margin-left: 10px;
}
.select2-selection{
    width: 438px;
    height: 47px !important;
    border-radius: 15px !important;
    background: #FFF;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    font-size: 20px;
    margin-top: 10px;
    padding-left: 10px;
    border:none !important;
}
.select2-container--open .select2-selection{
    border-radius: 15px 15px 0 0  !important;
    box-shadow: 0 !important;
    border: 1px solid #aaa !important;
}
.select2-selection__rendered{
    padding-top: 10px;
}
.select2-selection__arrow{
    margin-top: 20px;
    margin-right: 10px;
}
.select2-search__field{
    display: none;
}
.select2-results__option--highlighted{
    background-color: rgba(0, 0, 0, 0.25) !important;
    color: black !important;
    border-radius: 15px !important;
}
.select2-results__option{
    border-radius: 10px !important;
}
.select2-dropdown{
    border-radius: 0 0 10px 10px !important;
}
.error{
    width: 438px;
}
.error p {
    font-size: 14px;
    text-align: center;
    color: #FF0000;
}
@media screen and (max-width: 500px){
    .modal-container-update-banking{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0 0;
    }
    .form-input{
        width: 345px;
    }
    .unactive{
        width: 180px;

    }
    .delete{
        width: 180px;
    }
}
</style>
