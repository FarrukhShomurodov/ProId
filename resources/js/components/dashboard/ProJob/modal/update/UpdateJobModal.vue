<script>
// Importing necessary modules
import axios from 'axios';

//import components
import CreateEducationModal from "@/components/dashboard/data/modal/CreateEducationModal.vue";

export default {
    props: ['userId', 'jobId'],
    components: {
        CreateEducationModal
    },
    data() {
        return {
            // Data properties for the component
            type: null,
            profession: '',
            education_id: null,
            error: '',
            educations: [],
            show: false,
            showCreateEducationModal: false,
            loading: false
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.initializeSelect2();
        this.getData();
    },
    methods: {
        // Initialize select2
        initializeSelect2() {
            $('#select').select2();
            $('#select2').select2();
        },
        // Method for fetching data
        getData() {
            this.show = true;

            // Fetch education by user
            axios.get(`/api/education/${this.userId}`).then(res => {
                this.educations = res.data
            })

            // Fetch job data
            axios.get(`/api/job-show/${this.jobId}`).then(res => {
                const data = res.data;

                this.type = data.type
                this.profession = data.profession
                this.education_id = data.education_id ?? 'Без образования'

                $('#select').val(this.type).trigger('change');
                $('#select2').val(this.education_id).trigger('change');

                this.loading = true;
            });
        },
        // Method to save education
        save() {
            this.type = $('#select').val()
            this.education_id = isNaN(parseInt($('#select2').val())) ? null : parseInt($('#select2').val());

            const data = {
                'type': this.type,
                'profession': this.profession,
                'education_id': this.education_id,
            }
            axios.put(`/api/job/${this.jobId}`, data).then(() => {
                this.$emit('goBack');
            }).catch(err => {
                this.error = err.response.data.message;
            })
        },
        goBack() {
            this.showCreateEducationModal = false;
            this.getData();
        }
    },
};
</script>

<template>
    <div v-if="!showCreateEducationModal">
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <div class="modal-container modal-container-job" v-show="loading">
                            <div class="header_modal">
                                <h3 class="education_text">Изменение професии</h3>
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
                                    <label>Тип профессии *</label>
                                    <select v-model="type" class="form_input form_input-business" id="select" required>
                                        <optgroup label="Информационные технологии">
                                            <option value="Программист">Программист</option>
                                            <option value="Системный администратор">Системный администратор</option>
                                            <option value="Инженер по безопасности информации">Инженер по безопасности
                                                информации
                                            </option>
                                            <option value="Аналитик данных">Аналитик данных</option>
                                            <option value="Веб-разработчик">Веб-разработчик</option>
                                            <option value="Сетевой инженер">Сетевой инженер</option>
                                        </optgroup>

                                        <optgroup label="Здравоохранение">
                                            <option value="Врач">Врач</option>
                                            <option value="Медсестра">Медсестра</option>
                                            <option value="Врач-хирург">Врач-хирург</option>
                                            <option value="Фармацевт">Фармацевт</option>
                                            <option value="Терапевт">Терапевт</option>
                                            <option value="Стоматолог">Стоматолог</option>
                                        </optgroup>

                                        <optgroup label="Образование">
                                            <option value="Учитель">Учитель</option>
                                            <option value="Профессор">Профессор</option>
                                            <option value="Педагогический консультант">Педагогический консультант
                                            </option>
                                            <option value="Директор школы">Директор школы</option>
                                            <option value="Тренер">Тренер</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label class="labels">Введите специальность *</label>
                                    <input
                                        class="form_input"
                                        :class="{'form_input_error': error && profession.length === 0}"
                                        type="text"
                                        v-model="profession"
                                        placeholder="Введите  специальность"
                                        required
                                    />
                                </div>
                                <div>
                                    <label>Информация об образование *</label>
                                    <select v-model="education_id" class="form_input form_input-business" id="select2"
                                            required>
                                        <option value="Без образования">Без образования</option>
                                        <option v-for="education in educations" :value="education.id">{{
                                                education.name
                                            }}
                                        </option>
                                    </select>
                                </div>
                                <div class="add_bank" @click="showCreateEducationModal = true" style="
                                margin-top: 15px;
                                margin-bottom: 5px;
                                max-width: 438px;
                                height: 42px;
                                border-radius: 20px;
                                background: #F3F3F3;
                                display: flex;
                                flex-direction: row;
                                align-items: center;
                                padding-left: 11px;">
                                    <img src="/images/icons/dashboard/add.svg" alt="" style="margin-right: 11px;">
                                    <p>Добавьте информацию об образование</p>
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
                    <!-- Loading indicator -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
    <CreateEducationModal v-if="showCreateEducationModal" @goBack="goBack"
                          :userId=userId
    ></CreateEducationModal>
</template>

<style scoped>
</style>
