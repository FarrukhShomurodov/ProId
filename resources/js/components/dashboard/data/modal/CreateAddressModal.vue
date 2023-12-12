<script>
import axios from 'axios';


export default {
    props: ['userId'],
    data() {
        return {
            address: '',
        };
    },
    mounted() {

    },
    methods: {
        save(){
            const data = {
                user_id: this.userId,
                name: this.address,
            }
            axios.post('/api/address', data).then(() => {
                this.$emit('goBack')
            })
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
                            <h3 class="add_address">Добавить адрес</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>

                        <!--Map-->
                        <div id="map" style="width: 600px; height: 400px"></div>

                        <!--Footer-->
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
    </div>
</template>


<style>
.modal-mask {
    position: fixed;
    z-index: 1 !important;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}
.search_options{
    z-index: -1;
}
.modal-container-phone-number-edit {
    width: 512px;
    height: 440px;
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

.email_content p {
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}

.add_address {
    font-weight: 500;
    font-size: 24px;
}
.map{
    margin-top: -100px;
    width: 438px;
    height: 201px;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    border-radius: 15px;
}
@media screen and (max-width: 500px) {
    .modal-container-phone-number-edit {
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
}
</style>
