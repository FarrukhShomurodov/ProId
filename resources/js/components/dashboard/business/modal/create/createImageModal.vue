<script>
import axios from 'axios';

export default {
    props: ['business_id'],
    data() {
        return {
            imageData: '/images/icons/proBusiness/proConnect.jpg',
            avatarUrl: null,
            avatarFile: null,
            loading: true, // Loading state
        };
    },

    mounted() {
        axios.get(`/api/pro-business-show/${this.business_id}`).then(res => {
            this.avatarUrl = res.data.image;
            console.log(this.avatarUrl)
        })
    },

    methods: {
        onSelectFile() {
            this.avatarUrl = '/images/icons/proBusiness/proConnect.jpg'
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
        handleAvatarChange(e) {
            this.avatarFile = e.target.files[0];
        },
        uploadAvatar() {
            if (this.avatarFile) {
                const formData = new FormData();
                formData.append("image", this.avatarFile);

                axios.post(`/api/pro-business-image/${this.business_id}`, formData).then(response => {
                    this.avatarUrl = response.data.image;
                    this.$emit('close');
                });
            }
        },

    },
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container update_business_foto" v-if="loading">

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
                                      avatarUrl === '/images/icons/proBusiness/proConnect.jpg' ? imageData : 'storage/' + avatarUrl
                                    })`,
                                    'background-size': 'cover',
                                    'background-position': 'center'
                                  }"
                                ></div>

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
    .update_business_foto{
        width: 400px;
    }
}
</style>
