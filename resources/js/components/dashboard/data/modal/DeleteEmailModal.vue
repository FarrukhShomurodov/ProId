<script>
import axios from 'axios';

export default {
    props:['userId', 'email'],
    data(){
        return{

        }
    },
    methods:{
        destroy(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.delete(`/api/add-delete/${this.userId}`,{headers}).then(() => this.$emit('close'))
        },
    }
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-phone-number-edit">
                        <div class="header_modal">
                            <h3 class="tel">Почтовый яшик</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="email_container">
                            <img src="/images/icons/warning.svg" alt="">
                            <p>
                                Вы собираетесь Удалить почту<br>
                                {{ this.email }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="destroy">
                                    Удалить почту
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
.modal-container-phone-number-edit{
    width: 512px;
    height: 411px;
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
.edit_phone_content p{
    color: #000;
    font-size: 21px;
    margin-top: 30px;
}
.tel{
    font-weight: 500;
    font-size: 24px;
}
.email_container{
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.email_container p{
    margin-top: 30px;
    text-align: center;
}
.otc input {
    width: 37px;
    height: 30px;
    font-size: 20px;
    margin-left: 9px;
    text-align: center;
    justify-content: center;
    border-radius: 9px;
    border: 0.8px solid #FFF;
    background: #FFF;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    margin-bottom: 10px;
}

.otc input:focus {
    outline: none;
    box-shadow: 0 0 7px 0 #5FE0D8;
}
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
@media screen and (max-width: 500px){
    .modal-container-phone-number-edit{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
}
</style>
