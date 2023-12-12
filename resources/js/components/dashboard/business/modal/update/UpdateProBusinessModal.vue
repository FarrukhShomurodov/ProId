<script>
import axios from 'axios';

export default {
    props:['business_id'],
    data(){
        return{
            inn: '',
            name_of_business: '',
            form_of_business: '',
            oked: '',
            address: '',
            id: 0,
            loading: false,
        }
    },
    mounted() {
        axios.get(`/api/pro-business-show/${this.business_id}`).then(res => {
            const data = res.data;
            this.id = data.id;
            this.inn = data.inn;
            this.name_of_business = data.name_of_business;
            this.form_of_business = data.form_of_business;
            this.oked = data.oked;
            this.address = data.address;
            this.loading = true
        })
    },
    methods:{
        save(){
            const data = {
                'inn':  this.inn,
                'name_of_business': this.name_of_business,
                'form_of_business': this.form_of_business,
                'oked': this.oked,
                'address': this.address,
            }
            axios.put(`/api/pro-business/${this.id}`,data).then(res => {

                this.$emit('close')
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
                    <div class="modal-container-update-business" v-if="loading">
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
.modal-container-update-business{
    width: 512px;
    height: 587px;
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
    .modal-container-update-business{
        width: 406px;
        height: 600px;
        border-radius: 25px 25px 0 0;
    }
}
</style>
