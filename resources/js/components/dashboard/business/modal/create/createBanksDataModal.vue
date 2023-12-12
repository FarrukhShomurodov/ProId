<script>
import axios from 'axios';

export default {
    props:[
        'business_id'
    ],
    data(){
        return{
            mfo: '',
            payment_account: '',
            name: '',
            name_of_banking_akkaunt: '',
        }
    },
    mounted() {

    },
    methods:{
        save(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            const banks_data = {
                'business_id': this.business_id,
                'name':  this.name,
                'name_of_banking_akkaunt': this.name_of_banking_akkaunt,
                'mfo': this.mfo,
                'payment_account': this.payment_account,
            }
            axios.post('/api/banks-data', banks_data ,{headers}).then( res => {
                this.$emit('close');
            });
        },
        mfoBank(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/bank-data-by-mfo', {headers}).then(res => {
                for(let i = 0; i < res.data.length; i++){
                    if(res.data[i].code === this.mfo){
                        this.name = res.data[i].name
                    }
                }
            })
        }
    },
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-add-banking">
                        <div class="header_modal">
                            <h4>Добавление банковского счета</h4>
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
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
.modal-container-add-banking{
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
    .modal-container-add-banking{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0 0;
    }
    .form-input{
        width: 345px;
    }
}
</style>
