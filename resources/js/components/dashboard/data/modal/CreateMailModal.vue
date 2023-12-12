<script>
import axios from 'axios';

export default {
    props:['userId'],
    data(){
        return{
            mail:''
        }
    },
    mounted(){
    },
    methods:{
        save(){
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.post(`/api/add-mail/${this.userId}`, {
                email: this.mail
            },{headers}).then(() => {

                this.$emit('goBack')
            } )
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
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="email_content">
                            <span>Введите email, который поможет восстановить доступ<br> к аккаунту</span>
                            <input
                            required
                            type="email"
                            class="email"
                            v-model=mail
                            placeholder="Введите email"
                            />
                            <p>Если нету. Создайте почтовый ящик в PRO MAIL.</p>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button save_email" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                            <button class="create_pro_mail modal-default-button"  @click="$emit('goBack')">
                                     Создать PRO MAIL
                            </button>
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
    height: 366px;
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
.promail{
    width: 437px;
    height: 47px;
    border-radius: 9px;
    background: #FFF !important;
    border: none;
    font-size: 23px;
    color: #000 !important;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
}
.email_content{
    display: flex;
    justify-content: space-around;
    flex-direction: column;
    width: 438px;
    height: 100%;
}
.email_content p{
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}
.email{
    width: 438px;
    height: 54px;
    border-radius: 15px;
    background: #FFF;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    border:none;
    font-size: 28px;
    padding-left: 25px;
    color: #000;
}
.tel{
    font-weight: 500;
    font-size: 24px;
}
.save_email{
    background: #AFAFAF !important;
}
@media screen and (max-width: 500px){
    .modal-container-phone-number-edit{
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
    .email{
        width: 380px;
    }
    .email_content{
        width: 380px;
    }
}
</style>
