<script>
// Importing necessary modules
import axios from 'axios';

export default {
    props: ['userId'],
    data() {
        return {
            // Data properties for the component
            type: 'Среднее общее',
            name: '',
            started: '',
            expired: null,
            isStudying: false,
            show: false,
            error: '',
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.show = true;
        $('#select').select2();
        this.$nextTick(() => {
            $('#select').select2();
        });
    },
    methods: {
        // Method to save education
        save() {
            let timeDifference = false;
            let comparingDate = false;

            if (!this.isStudying) {
                if (this.expired === null) {
                    this.error = 'Поле "Период обучения" является обязательным для заполнения.'
                } else {
                    const currentDate = new Date();
                    const startedDate = new Date(this.started)
                    const expiredDate = new Date(this.expired);

                    // Calculate the absolute difference in milliseconds
                    timeDifference = currentDate > expiredDate;
                    comparingDate = startedDate > expiredDate;

                    if(!timeDifference) this.error = 'Дата окончание не может быть в будушем времени'
                    if(comparingDate) this.error = 'Дата окончание не может быть в меньше даты начало'
                }
            } else {
                this.expired = null;
                timeDifference = true;
            }

            if(timeDifference && !comparingDate){
                this.type = $('#select').val()
                const data = {
                    'user_id': this.userId,
                    'type': this.type,
                    'name': this.name,
                    'started': this.started,
                    'expired': this.expired,
                    'is_studying': this.isStudying
                }

                axios.post('/api/education', data).then(() => {
                    this.$emit('goBack');
                }).catch(err => {
                    this.error = err.response.data.message;
                })
            }
        },
    },
};
</script>

<template>
    <div>
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">

                        <div class="modal-container modal-container-education" v-show="show">
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
                                        <option value="Среднее общее">Среднее общее</option>
                                        <option value="Среднее профессиональное">Среднее профессиональное</option>
                                        <option value="Бакалавриат">Бакалавриат</option>
                                        <option value="Магистратура">Магистратура</option>
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
                                            :class="{'form_input_error': error && started.length === 0, 'education_date_margin': isStudying}"
                                            required
                                        />
                                        <span v-show="!isStudying">-</span>
                                        <input
                                            v-show="!isStudying"
                                            class="form_input education_dates"
                                            type="date"
                                            v-model="expired"
                                            :class="{'form_input_error': error && expired === null}"
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
                                        Создать
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
</style>
