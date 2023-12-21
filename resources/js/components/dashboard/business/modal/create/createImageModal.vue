<script>
// Importing necessary modules
import axios from 'axios';

export default {
    props: ['business_id'],
    data() {
        return {
            // Vue.js data properties
            imageData: '/images/icons/proBusiness/proConnect.jpg',
            avatarUrl: null,
            avatarFile: null,
            loading: false, // Loading state
        };
    },

    mounted() {
        // Fetch business image data on component mount
        const headers = {
            'Authorization': `Bearer ` + localStorage.token,
            'Content-Type': 'application/json',
        };
        axios.get(`/api/pro-business-show/${this.business_id}`, {headers}).then(res => {
            this.avatarUrl = res.data.image;
            console.log(this.avatarUrl);
            this.loading = true; // Update loading state once data is fetched
        });
    },

    methods: {
        // Handle file selection for the avatar image
        onSelectFile() {
            this.avatarUrl = '/images/icons/proBusiness/proConnect.jpg';
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
        // Handle change in avatar file input
        handleAvatarChange(e) {
            this.avatarFile = e.target.files[0];
        },
        // Upload the avatar image to the server
        uploadAvatar() {
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'multipart/form-data',
            };
            if (this.avatarFile) {
                const formData = new FormData();
                formData.append("image", this.avatarFile);

                // HTTP POST request to update the business avatar image
                axios.post(`/api/pro-business-image/${this.business_id}`, formData, {headers}).then(response => {
                    this.avatarUrl = response.data.image;
                    this.$emit('close'); // Close the modal after successful upload
                }).catch(error => {
                    console.error('Error uploading avatar:', error);
                });
            }
        },
    },
};
</script>

<template>
    <div>
        <!-- Modal for updating business photo -->
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container update_business_foto" v-if="loading">

                        <!-- Header of the modal -->
                        <div class="header_modal">
                            <h4>Ваше Данные</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>

                        <!-- Main content of the modal -->
                        <div>
                            <div class="user_foto">
                                <!-- Display the avatar image -->
                                <div
                                    class="ava"
                                    style="width: 92px; height: 92px;"
                                    :style="{
                                    'background-image': `url(${
                                      avatarUrl === '/images/icons/proBusiness/proConnect.jpg' ? imageData : '/storage/' + avatarUrl
                                    })`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }"
                                ></div>

                                <!-- Avatar image actions -->
                                <div class="foto_actions">
                                    <button
                                        class="upload_file"
                                        @change="handleAvatarChange"
                                    >
                                        <input
                                            type="file"
                                            ref="fileInput"
                                            @change="handleAvatarChange"
                                            @input="onSelectFile"
                                        />
                                        Изменить
                                    </button>
                                </div>
                            </div>

                            <!-- Modal footer with Save button -->
                            <div class="modal-footer">
                                <slot name="footer">
                                    <button class="modal-default-button" @click="uploadAvatar" >
                                        Сохранить
                                    </button>
                                </slot>
                            </div>
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
/* Styling for the modal component */
.update_business_foto{
    width: 500px;
    height: 240px;
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
@media screen and (max-width: 500px) {
    /* Responsive styling for smaller screens */
    .update_business_foto{
        width: 400px !important;
    }
}
@media screen and (max-width: 390px) {
    .update_business_foto {
        width: 360px !important;
    }
}
</style>
