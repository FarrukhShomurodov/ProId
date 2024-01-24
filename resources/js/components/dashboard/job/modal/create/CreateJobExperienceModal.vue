<script>
// Importing necessary modules
import axios from 'axios';

export default {
    props: ['job_id'],
    emits: ['goBack'],
    data() {
        return {
            // Data properties for the component
            place: '',
            post: '',
            started: '',
            expired: null,
            error: '',
            isWorking: false,
            show: false,
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.show = true;
    },
    methods: {
        // Method to save experience
        save() {
            // comparing date
            let timeDifference = false;
            let comparingDate = false;

            if (!this.isWorking) {
                if (this.expired === null) {
                    this.error = 'Поле "Период работы" является обязательным для заполнения.'
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
                const data = {
                    'job_id': this.job_id,
                    'place': this.place,
                    'post': this.post,
                    'started': this.started,
                    'expired': this.expired,
                    'is_working': this.isWorking
                }

                axios.post('/api/experience', data).then((res) => {
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
                        <div class="modal-container modal-container-experience" v-show="show">
                            <div class="header_modal">
                                <h3 class="education_text">Добавить информацию об трудовой стаже</h3>
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
                                    <label class="labels">Место Работы *</label>
                                    <input
                                        class="form_input"
                                        :class="{'form_input_error': error && place.length === 0}"
                                        type="text"
                                        v-model="place"
                                        placeholder="Введите наименование организации"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="labels">Введите должность *</label>
                                    <input
                                        class="form_input"
                                        :class="{'form_input_error': error && post.length === 0}"
                                        type="text"
                                        v-model="post"
                                        placeholder="Введите должность"
                                        required
                                    />
                                </div>
                                <div>
                                    <label class="labels">Период работы *</label><br>
                                    <div class="date_container">
                                        <input
                                            class="form_input education_dates"
                                            type="date"
                                            v-model="started"
                                            :class="{'form_input_error': error && started.length === 0, 'education_date_margin': !isWorking}"
                                            required
                                        />
                                        <span v-show="!isWorking">-</span>
                                        <input
                                            class="form_input education_dates"
                                            v-if="!isWorking"
                                            type="date"
                                            v-model="expired"
                                            :class="{'form_input_error': error && expired == null}"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="status_radio flex-row">
                                    <p>Работаю по сей день</p>
                                    <div class="radio_rounded">
                                        <input v-model="isWorking" type="checkbox" id="radio_rounded">
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
