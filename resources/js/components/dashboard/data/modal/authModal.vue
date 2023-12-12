<script>
import axios from 'axios';

export default {
    data() {
        return {
            imageData: '',
            name: '',
            surname: '',
            activeM: false,
            activeF: false,
            userId: '',
            date: '',
            gender: '',
            avatarUrl: '',
            avatarFile: null,
            editAVA: false,
            loading: false, // Loading state
        };
    },

    mounted() {
        const headers = {
            Authorization: `Bearer ` + localStorage.token,
            'Content-Type': 'application/json',
        };

        axios.get('/api/user', {headers}).then((res) => {
            this.userId = res.data.id;
            this.name = res.data.name;
            this.surname = res.data.surname;
            res.data.gender === 'male' ? this.activeMale() : this.activeFemale();
            this.date = res.data.data_of_birth;
            this.avatarUrl = res.data.avatar;
            this.loading = true; // Set loading to false once data is loaded
        });
    },

    methods: {
        activeMale() {
            this.activeM = true;
            this.activeF = false;
            this.gender = 'male'
        },
        activeFemale() {
            this.activeF = true;
            this.activeM = false;
            this.gender = 'female'
        },
        onSelectFile() {
            this.avatarUrl = ''
            this.imageData = ''
            const input = this.$refs.fileInput
            const files = input.files
            if (files && files[0]) {
                const reader = new FileReader
                reader.onload = e => {
                    this.imageData = e.target.result
                }
                reader.readAsDataURL(files[0])
                this.$emit('input', files[0])
            }
        },
        deleteImage() {
            axios.delete(`/api/delete-avatar/${this.userId}`).then(res => console.log(res));
            this.avatarUrl = ''
            this.imageData = ''
            this.editAVA = true
        },
        handleAvatarChange(e) {
            this.avatarFile = e.target.files[0];
        },
        uploadAvatar() {
            if (this.avatarFile) {
                const formData = new FormData();
                formData.append("avatar", this.avatarFile);

                axios.post(`/api/upload-avatar/${this.userId}`, formData).then(response => {
                    this.avatarUrl = response.data.avatar;
                });
            }
        },
        save() {
            this.uploadAvatar()
            const data = {
                name: this.name,
                surname: this.surname,
                date_of_birth: this.date,
                gender: this.gender,
            };

            axios.put(`/api/update/${this.userId}`, data).then(() => {
                this.$emit('close');
            });
        },
    },
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container" v-if="loading">

                        <div class="header_modal">
                            <h4>Ваше Данные</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <div>
                            <div class="user_foto">
                                <div
                                    class="ava"
                                    style="width: 92px; height: 92px;"
                                    :style="{
                                    'background-image': `url(${
                                      avatarUrl !== '' ? 'storage/' + avatarUrl : imageData
                                    })`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }"
                                ></div>

                                <div class="foto_actions">
                                    <button
                                        class="upload_file"
                                        @change="handleAvatarChange"
                                        v-if="this.imageData !== '' || this.avatarUrl !== ''"
                                    >
                                        <input
                                            type="file"
                                            ref="fileInput"
                                            @change="handleAvatarChange"
                                            @input="onSelectFile"
                                        />
                                        Изменить
                                    </button>
                                    <button
                                        class="upload_file"
                                        @click="deleteImage"
                                        v-if="this.imageData !== '' || this.avatarUrl !== ''"
                                    >
                                        Удалить
                                    </button>
                                    <button
                                        class="upload_file"
                                        v-if="this.imageData === '' && this.avatarUrl === '' && this.editAVA"
                                    >
                                        <input
                                            type="file"
                                            ref="fileInput"
                                            @change="handleAvatarChange"
                                            @input="onSelectFile"
                                        />
                                        Добавить Фото
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="name_surname_data">
                            <label class="labels">Ваше Имя Фамилия</label>
                            <input
                                class="form_input"
                                type="text"
                                v-model="name"
                                placeholder="Введите Имя"
                            />
                            <input
                                class="form_input"
                                type="text"
                                v-model="surname"
                                placeholder="Введите Фамилию"
                            />
                        </div>
                        <div class="other_information">
                            <div class="container_info">
                                <label class="labels">Пол</label>
                                <div class="gender">
                                    <button
                                        class="male"
                                        :class="{'activeMale': activeM}"
                                        @click="activeMale"
                                    >
                                        М
                                    </button>
                                    <button
                                        class="female"
                                        :class="{'activeFemale': activeF}"
                                        @click="activeFemale"
                                    >
                                        Ж
                                    </button>
                                </div>
                            </div>
                            <div class="container_info">
                                <label class="labels">Дата Рождения</label>
                                <input type="date" class="date" v-model="date"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save">
                                    Сохранить
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

<style>

.male, .female {
    color: #575757;
    font-size: 24px;
    padding: 10px;
    border: none;
    background-color: white;
}

.activeMale {
    border-radius: 15px;
    background: #DDD;
    color: black;
}

.activeFemale {
    border-radius: 15px;
    background: #DDD;
    color: black;
}

.gender button:hover {
    border-radius: 15px;
    background: #DDD;
    color: black;
}

.loading-indicator {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    z-index: 9999;
}
</style>
