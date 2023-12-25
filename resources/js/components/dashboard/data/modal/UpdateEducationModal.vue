<script>
// Importing necessary modules
import axios from 'axios';

export default {
    // Component properties
    props:['education_id'],
    // Component data initialization
    data(){
        return{
            // Data properties for the component
            type: 'Частное предприятие - ЧП',
            name: '',
            started: '',
            expired: '',
            isStudying: false,
            error: '',
            loading: false,
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        $('#select').select2(); this.$nextTick(() => {
            $('#select').select2();
        });
        axios.get(`/api/education-show/${this.education_id}`).then(res => {
            const data = res.data;
            this.type = data.type
            this.name = data.name
            this.started = data.started
            this.expired = data.expired
            this.isStudying = data.is_studying
            this.loading = true;
        })
    },
    // Component methods
    methods:{
        // Method to save education
        save(){
            this.type = $('#select').val()
            const data = {
                'type': this.type,
                'name': this.name,
                'started': this.started,
                'expired': this.expired,
                'is_studying': this.isStudying
            }

            axios.put(`/api/education/${this.education_id}`, data).then(() => {
                this.$emit('goBack');
            }).catch( err => {
                this.error = err.response.data.message;
            })
        },
    },
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container modal-container-education" v-if="loading">
                        <div class="header_modal">
                            <h3 class="education_text">Добавить информацию об образование</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>
                        <div class="education_content">
                            <div class="error">
                                <p>{{ error }}</p>
                            </div>
                            <div>
                                <label>Вид образования *</label>
                                <select v-model="type" class="form_input form_input-business" id="select" required>
                                    <option value="Частное предприятие - ЧП">Частное предприятие - ЧП</option>
                                    <option value="Семейное предприятие - СП">Семейное предприятие - СП</option>
                                    <option value="Фермерское хозяйство - ФХ">Фермерское хозяйство - ФХ</option>
                                    <option value="Производственный кооператив - ПК">Производственный кооператив - ПК</option>
                                    <option value="Коммандитная товарищества - КТ">Коммандитная товарищества - КТ</option>
                                    <option value="Общество с ограниченной ответственностью - ООО">Общество с ограниченной ответственностью - ООО</option>
                                </select>
                            </div>
                            <div>
                                <label class="labels">Наименование учебного заведения *</label>
                                <input
                                    class="form_input"
                                    :class="{'form_input_error': error && name.length === 0}"
                                    type="text"
                                    v-model="name"
                                    placeholder="Введите наименование учебного заведения"
                                    required
                                />
                            </div>
                            <div>
                                <label class="labels">Период обучения *</label><br>
                                <div class="date_container">
                                    <input
                                        class="form_input education_dates"
                                        type="date"
                                        v-model="started"
                                        :class="{'form_input_error': error && started.length === 0}"
                                        required
                                    />
                                    -
                                    <input
                                        class="form_input education_dates"
                                        type="date"
                                        v-model="expired"
                                        :class="{'form_input_error': error && expired.length === 0}"
                                        required
                                    />
                                </div>
                            </div>
                            <div class="status_radio flex-row">
                                <p>Учусь по сей день</p>
                                <div class="radio_rounded">
                                    <input v-model="isStudying" type="checkbox" id="radio_rounded">
                                    <label for="radio_rounded"></label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save">
                                    Изменить
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

<style scoped>
</style>
