<script>
import axios from 'axios';

export default {
    props: ['business_id'],
    data() {
        return {
            inn: '',
            name_of_business: '',
            form_of_business: '',
            oked: '',
            address: '',
            id: 0,
            loading: false,
            error: '',
        }
    },
    mounted() {
        // Fetch business data when the component is mounted
        axios.get(`/api/pro-business-show/${this.business_id}`).then(res => {
            // Update component data with the retrieved business information
            const data = res.data;
            this.id = data.id;
            this.inn = data.inn;
            this.name_of_business = data.name_of_business;
            this.form_of_business = data.form_of_business;
            this.oked = data.oked;
            this.address = data.address;
            this.select()
            this.loading = true; // Set loading to true after data is fetched
        }).then(() => {
            // Initialize Select2 after the component has been fully rendered and data is fetched
            this.select();
        });
    },
    methods: {
        select(){
            $('#select').select2();
        },
        save() {
            // Save business data when the "Сохранить" button is clicked
            const data = {
                'inn': this.inn,
                'name_of_business': this.name_of_business,
                'form_of_business': this.form_of_business,
                'oked': this.oked,
                'address': this.address,
            }
            axios.put(`/api/pro-business/${this.id}`, data).then(res => {
                // Close the modal after successfully saving data
                this.$emit('close')
            }).catch(err => {
                // Handle errors and update the error message
                this.error = err.response.data.message;
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
                    <!-- Modal container for updating business information -->
                    <div class="modal-container modal-container-update-business" v-if="loading">
                        <div class="header_modal">
                            <h4>Реквизиты</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="create-business-content">
                            <div class="error">
                                <p>{{ error }}</p>
                            </div>
                            <!-- Form fields for business information -->
                            <div>
                                <label>ИНН бизнеса *</label>
                                <input type="number" v-model=inn class="form_input form_input-business"
                                       :class="{'form_input_error': error && inn.length === 0}"
                                       placeholder="Введите ИНН" required>
                            </div>
                            <div>
                                <label>Название фирмы *</label>
                                <input type="text" v-model=name_of_business class="form_input form_input-business"
                                       :class="{'form_input_error': error && name_of_business.length === 0}"
                                       placeholder="Введите официальное юридическое имя" required>
                            </div>
                            <div>
                                <label>Форма бизнеса *</label>
                                <select v-model=form_of_business  class="form_input form_input-business" id="select" required>
                                    <option value="Частное предприятие - ЧП">Частное предприятие - ЧП</option>
                                    <option value="Семейное предприятие - СП">Семейное предприятие - СП</option>
                                    <option value="Фермерское хозяйство - ФХ">Фермерское хозяйство - ФХ</option>
                                    <option value="Производственный кооператив - ПК">Производственный кооператив - ПК</option>
                                    <option value="Коммандитная товарищества - КТ">Коммандитная товарищества - КТ</option>
                                    <option value="Общество с ограниченной ответственностью - ООО">Общество с ограниченной ответственностью - ООО</option>
                                </select>
                            </div>
                            <div>
                                <label>ОКЭД бизнеса</label>
                                <input type="number" v-model=oked class="form_input form_input-business"
                                       :class="{'form_input_error': error && oked.length === 0}"
                                       placeholder="Введите ОКЭД бизнеса" required>
                            </div>
                            <div>
                                <label>Юридический адрес</label>
                                <input type="text" v-model=address class="form_input form_input-business"
                                       :class="{'form_input_error': error && address.length === 0}"
                                       placeholder="Введите Юридический адрес">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!-- Save button to trigger the save method -->
                            <slot name="footer">
                                <button class="modal-default-button" @click="save" >
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                    <!-- Loading indicator while data is being fetched -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
input{
    border: 0 solid #5FE0D8 !important;
}
</style>
