<script>
import axios from 'axios';

export default {
    props:[
        'banking_id'
    ],
    data(){
        return{
            name: '',
            name_of_banking_akkaunt: '',
            mfo: '',
            payment_account: '',
            loading: false
        }
    },
    mounted() {
        const headers = {
            'Authorization': `Bearer ` + localStorage.token,
            'Content-Type': 'application/json',
        };
        axios.get(`/api/banking-data-show/${this.banking_id}`,{headers}).then(res => {
            const data = res.data;
            this.name = data.name
            this.name_of_banking_akkaunt = data.name_of_banking_akkaunt
            this.mfo = data.mfo;
            this.payment_account = data.payment_account;
            this.loading = true
        });
    },
    methods:{
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
            axios.put(`/api/banks-data/${this.banking_id}`, banks_data,{headers}).then( res => {

                this.$emit('close')
            });
        },
        mfoBank(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/bank-data-by-mfo',{headers}).then(res => {
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
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-update_banking"  v-if="loading">
                        <div class="header_modal">
                            <h4>Изменение банковского счета</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="create-business-content">
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
                                <input type="text" v-model=name class="form-input" placeholder="Наимонование банка">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
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
    height: 460px !important;
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

@media screen and (max-width: 500px){
    .modal-container-update_banking{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0 0;
    }
}
</style>
