<script>
// Importing necessary modules
import axios from 'axios';

export default {
    props: ['userId'],
    data() {
        return {
            // Data properties for the component
            type: '',
            profession: '',
            education_id: 'Без образования',
            error: '',
            educations: [],
            show: false,
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        this.show = true;
        $('#select').select2();
        this.$nextTick(() => {
            $('#select').select2();
            $('#select2').select2();
        });

        // Fetch education by user
        axios.get(`/api/education/${this.userId}`).then(res => {
            this.educations = res.data
        })
    },
    methods: {
        // Method to save education
        save() {
            this.type = $('#select').val()
            this.education_id = isNaN(parseInt($('#select2').val())) ? null : parseInt($('#select2').val());

            const data = {
                'user_id': this.userId,
                'type': this.type,
                'profession': this.profession,
                'education_id': this.education_id,
            }

            axios.post('/api/job', data).then(() => {
                this.$emit('goBack');
            }).catch(err => {
                this.error = err.response.data.message;
            })
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
                        <div class="modal-container modal-container-job" v-show="show">
                            <div class="header_modal">
                                <h3 class="education_text">Добавление професии</h3>
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
                                        <option value="Частное предприятие - ЧП">Частное предприятие - ЧП</option>
                                        <option value="Семейное предприятие - СП">Семейное предприятие - СП</option>
                                        <option value="Фермерское хозяйство - ФХ">Фермерское хозяйство - ФХ</option>
                                        <option value="Производственный кооператив - ПК">Производственный кооператив -
                                            ПК
                                        </option>
                                        <option value="Коммандитная товарищества - КТ">Коммандитная товарищества - КТ
                                        </option>
                                        <option value="Общество с ограниченной ответственностью - ООО">Общество с
                                            ограниченной ответственностью - ООО
                                        </option>
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
                                <div class="add_bank" @click="" style="
                                margin-top: 15px;
                                margin-bottom: 20px;
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
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
</style>
