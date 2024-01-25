<script>
import axios from 'axios';

export default {
    props: ['email'],
    data() {
        return {
            show: false
        }
    },
    mounted() {
        this.show = true;
    },
    methods: {
        destroy() {
            axios.delete("/api/delete-email").then(() => this.$emit('goBack'))
        },
    }
};
</script>

<template>
    <div>
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <div class="modal-container modal-container-address-phone-mail" v-show="show">
                            <div class="header_modal">
                                <h3 class="pochta">Почтовый яшик</h3>
                                <img
                                    src="/images/icons/dashboard/exit.svg"
                                    @click="$emit('goBack')"
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
                    </transition>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
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
</style>
