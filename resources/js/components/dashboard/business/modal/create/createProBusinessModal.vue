<script>
import axios from 'axios';

export default {
    props:[
        'userId'
    ],
    data(){
        return{
            inn: '',
            name_of_business: '',
            form_of_business: '',
            name_of_bank: '',
            name_of_banking_akkaunt: '',
            oked: '',
            address: '',
            mfo: '',
            payment_account: '',
        }
    },
    mounted() {
    },
    methods:{
        save(){
            const data = {
                'user_id':this.userId,
                'inn':  this.inn,
                'name_of_business': this.name_of_business,
                'form_of_business': this.form_of_business,
                'oked': this.oked,
                'address': this.address,
            }
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.post('/api/pro-business',data).then(res => {
                const business_id = res.data.id;
                const banks_data = {
                    'business_id': business_id,
                    'name': this.name_of_bank,
                    'name_of_banking_akkaunt': this.name_of_banking_akkaunt,
                    'mfo': this.mfo,
                    'payment_account': this.payment_account
                }
                axios.post('/api/banks-data', banks_data, {headers}).then( () =>{
                    this.$emit('close');
                });
            })
        },
        mfoBank(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('api/bank-data-by-mfo',{headers}).then(res => {
                for(let i = 0; i < res.data.length; i++){
                    if(res.data[i].code === this.mfo){
                        this.name_of_bank = res.data[i].name
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
                    <div class="modal-container-create-business">
                        <div class="header_modal">
                            <h4>Реквизиты</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="create-business-content">
                            <div>
                                <label>ИНН бизнеса *</label>
                                <input type="number" v-model=inn class="form-input" placeholder="Введите ИНН" required>
                            </div>
                            <div>
                                <label>Название фирмы *</label>
                                <input type="text" v-model=name_of_business class="form-input" placeholder="Введите официальное юридическое имя" required>
                            </div>
                            <div>
                                <label>Форма бизнеса *</label>
                                <input type="text" v-model=form_of_business class="form-input" placeholder="Выберите форму бизнеса" required>
                            </div>
                            <div>
                                <label>ОКЭД бизнеса</label>
                                <input type="number" v-model=oked class="form-input" placeholder="Введите ОКЭД бизнеса" required>
                            </div>
                            <div>
                                <label>Юридический адрес</label>
                                <input type="text" v-model=address class="form-input" placeholder="Введите Юридический адрес">
                            </div>
                            <hr>
                            <h4>Банковские данные</h4>
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
                                <input type="text" v-model=name_of_bank class="form-input" placeholder="Наимование банка">
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
.modal-container-create-business {
    width: 512px;
    height: 970px !important;
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

@media screen and (max-width: 500px) {
    .modal-container-create-business {
        width: 400px; /* Set width to 100% for smaller screens */
        height: 525px !important; /* Let the height adjust based on content */
        border-radius: 25px 25px 0px 0px;
        overflow-y: auto; /* Add this line to enable vertical scrolling */
    }
    .create-business-content {
        margin-left: 2px;
    }
    /* Adjust other styles for smaller screens as needed */
    .form-input {
        width: 380px !important; /* Set width to 100% for smaller screens */
    }
    .create-business-content{
        margin-left: 0 !important;
    }
}
</style>
