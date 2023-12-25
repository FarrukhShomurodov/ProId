<script>
import axios from 'axios';

export default {
    data() {
        return {
            // State for user data
            imageData: '', // Image data for avatar
            name: '', // User's name
            surname: '', // User's surname
            activeM: false, // Flag for male gender button
            activeF: false, // Flag for female gender button
            userId: '', // User ID
            date: '', // User's date of birth
            gender: '', // User's gender
            avatarUrl: '', // URL of the avatar image
            avatarFile: '', // File object for avatar upload
            editAVA: true, // Flag for avatar editing
            loading: false, // Loading state
        };
    },

    mounted() {
        // Fetch user data from the API when the component is mounted

        axios.get('/api/user').then((res) => {
            // Set user data based on the API response
            this.userId = res.data.id;
            this.name = res.data.name;
            this.surname = res.data.surname;
            res.data.gender === 'male' ? this.activeMale() : this.activeFemale();
            this.date = res.data.date_of_birth;
            this.avatarUrl = res.data.avatar ?? '';
            this.loading = true; // Set loading to false once data is loaded
        });
    },

    methods: {
        // Set active flags for male and female buttons
        activeMale() {
            this.activeM = true;
            this.activeF = false;
            this.gender = 'male';
        },
        activeFemale() {
            this.activeF = true;
            this.activeM = false;
            this.gender = 'female';
        },
        // Handle file selection for avatar
        onSelectFile() {
            this.avatarUrl = '';
            this.imageData = '';
            const input = this.$refs.fileInput;
            const files = input.files;

            if (files && files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    this.imageData = e.target.result;
                };
                reader.readAsDataURL(files[0]);
                this.$emit('input', files[0]);
            }
        },
        // Delete user's avatar image
        deleteImage() {
            axios.delete(`/api/delete-avatar/${this.userId}`).then(res => console.log(res));
            this.avatarUrl = '';
            this.imageData = '';
            this.editAVA = true;
        },
        // Handle avatar file change
        handleAvatarChange(e) {
            this.avatarFile = e.target.files[0];
        },
        // Upload avatar image
        uploadAvatar() {
            if (this.avatarFile) {
                const formData = new FormData();
                formData.append('avatar', this.avatarFile);

                axios.post(`/api/upload-avatar/${this.userId}`, formData)
                    .then(response => {
                        this.avatarUrl = response.data.avatar_url;
                    })
                    .catch(error => {
                        console.error('Error uploading avatar:', error);
                    });
            }
        },
        // Save user data
        save() {
            // Upload avatar first
            this.uploadAvatar();

            // Prepare user data for update
            const data = {
                name: this.name,
                surname: this.surname,
                date_of_birth: this.date,
                gender: this.gender,
            };

            // Send update request to the API
            axios.put(`/api/update/${this.userId}`, data).then(() => {
                this.$emit('close');
            });
        },
    },
};
</script>

<template>
    <!-- User data modal container -->
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container" v-if="loading">
                        <!-- Modal header -->
                        <div class="header_modal">
                            <h4>Ваше Данные</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <!-- Avatar and photo editing section -->
                        <div>
                            <div class="user_foto">
                                <div
                                    class="ava"
                                    style="width: 92px; height: 92px;"
                                    :style="{
                                    'background-image': `url(${
                                      avatarUrl !== '' ? '/storage/' + avatarUrl : imageData
                                    })`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }"
                                ></div>

                                <div class="foto_actions">
                                    <!-- Edit and delete buttons for existing photo -->
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
                                    <!-- Add button for adding new photo -->
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
                        <!-- Name, surname, and other information input section -->
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
                            <!-- Gender and date of birth input -->
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
                        <!-- Modal footer with save button -->
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

<style scoped>
</style>
