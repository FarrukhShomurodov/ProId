<script>
// Importing necessary modules
import axios from 'axios';

export default {
    // Component properties
    props:[
        'banking_id'
    ],
    // Component data initialization
    data(){
        return{
            // Data properties for the component
            name: '',
            name_of_banking_akkaunt: '',
            mfo: '',
            payment_account: '',
            loading: false,
            error: ''
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        // Fetch banking data on component mount
        const headers = {
            'Authorization': `Bearer ` + localStorage.token,
            'Content-Type': 'application/json',
        };
        axios.get(`/api/banking-data-show/${this.banking_id}`,{headers}).then(res => {
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
    methods:{
        // Method to save banking data
        save(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            const banks_data = {
                'mfo': this.mfo,
                'payment_account': this.payment_account,
                'name': this.name,
                'name_of_banking_akkaunt': this.name_of_banking_akkaunt
            }
            // Update banking data via API
            axios.put(`/api/banks-data/${this.banking_id}`, banks_data,{headers}).then( res => {
                this.$emit('close'); // Close the modal after successful data update
            }).catch(err => {
                this.error = err.response.data.message;
            });
        },
        // Method to fetch bank data based on MFO
        mfoBank(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/bank-data-by-mfo',{headers}).then(res => {
                // Find and set the bank name based on MFO
                for(let i = 0; i < res.data.length; i++){
                    if(res.data[i].code == this.mfo){
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
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <!-- Modal container for updating banking data -->
                    <div class="modal-container-update_banking"  v-if="loading">
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
                                <input type="text" v-model=name_of_banking_akkaunt class="form-input" placeholder="Введите наименование счет" required>
                            </div>
                            <div>
                                <label>МФО *</label>
                                <input type="number" v-model=mfo class="form-input" placeholder="Введите МФО" @input="mfoBank" required>
                            </div>
                            <div>
                                <label>Расчетный счет *</label>
                                <input type="number" v-model=payment_account class="form-input" placeholder="Введите Расчетный счет" required>
                            </div>
                            <div>
                                <input type="text" v-model=name class="form-input" placeholder="Наимонование банка" readonly>
                            </div>
                        </div>
                        <!-- Modal footer with Save button -->
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                    <!-- Loading indicator -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
/* Styling for the modal component */
.loading-indicator {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    z-index: 9999;
}
.modal-container-update_banking{
    width: 512px;
    height: 500px !important;
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
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    border: none;
    padding-left: 15px;
    font-size: 20px;
    margin-top: 10px;
}
.error{
    width: 438px;
}
.error p {
    font-size: 14px;
    text-align: center;
    color: #FF0000;
}
/* Responsive styling for smaller screens */
@media screen and (max-width: 500px){
    .modal-container-update_banking{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0 0;
    }
}
@media screen and (max-width: 390px) {
    .modal-container-update_banking{
        width: 360px !important; /* Set width to 100% for smaller screens */
    }
}
</style>
