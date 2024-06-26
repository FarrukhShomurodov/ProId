<script>
// Importing other components
import CreateJobExperienceModal from "../job/modal/create/CreateJobExperienceModal.vue";
import UpdateEducationModal from "../data/modal/update/UpdateEducationModal.vue";
import UpdateJobExperienceModal from "../job/modal/update/UpdateJobExperienceModal.vue";

export default {
    props: ['job_id'],
    emits: ['goBack'],
    components: {
        CreateJobExperienceModal,
        UpdateEducationModal,
        UpdateJobExperienceModal
    },
    data() {
        return {
            // Backend data
            job: [],
            education: [],
            experiences: [],
            experienceDate: 0,
            education_id: null,
            experience_id: null,
            loading: false,

            // Frontend state
            showCreateJobExperienceModal: false,
            showUpdateEducationModal: false,
            showUpdateExperienceModal: false,
        }
    },
    mounted() {
        this.fetchExperienceDate()
    },
    methods: {
        async fetchExperienceDate() {
            try {
                // Make both requests concurrently
                const [jobResponse, experienceResponse] = await Promise.all([
                    axios.get(`/api/job/${this.job_id}`),
                    axios.get(`/api/experience-by-job/${this.job_id}`)
                ]);

                // Handle job response
                this.job = jobResponse.data;
                this.education = jobResponse.data.education;

                // Handle experience response
                this.experiences = experienceResponse.data;
                this.experienceDate = 0;

                this.experiences.forEach((date) => {
                    const startedDate = new Date(date.started);
                    const expiredDate = new Date(date.expired);

                    const experienceDates = Math.abs(startedDate - expiredDate);
                    this.experienceDate += experienceDates;
                });

                // If experienceDate is greater than 0, update job-experience
                // if (this.experienceDate > 0) {
                //     await axios.put(`/api/experience/${this.job_id}`, {
                //         'experience_count': BigInt(this.experienceDate).toString()
                //     });
                // }

                this.loading = true;
            } catch (error) {
                console.error("Error fetching data:", error);
                // Handle error appropriately, e.g., set loading state or show error message
            }
        },
        formatExperienceDate(milliseconds) {
            const seconds = Math.floor(milliseconds / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);
            const years = Math.floor(days / 365);
            const months = Math.floor((days % 365) / 30); // Approximate months

            const formattedDate = [];

            if (years > 0) {
                formattedDate.push(`${years} ${years === 1 ? 'год' : 'год'}`);
            }
            if (months > 0) {
                formattedDate.push(`${months} ${months === 1 ? 'мес.' : 'мес.'}`);
            }

            return formattedDate.join(' ');
        },
        close() {
            this.fetchExperienceDate();
            this.showCreateJobExperienceModal = this.showUpdateExperienceModal = this.showUpdateEducationModal = false;
            // this.experienceDate = 0;
        },
    },

}
</script>

<template>
    <div v-if="loading">
        <!--job Details-->
        <div>
            <div class="d-flex">
                <img src=/images/icons/dashboard/back.svg width="13" height="23" class="back back_job"
                     @click="$emit('goBack')" alt="back icon">
                <section class="job_info d-flex flex-column justify-content-between">
                    <h3>Профессия: {{ job.profession }}</h3>
                    <div class="flex_row justify-content-between w-100">
                        <p>Специальность: {{ job.type }}</p>
                        <p>Стаж: {{
                                parseInt(experienceDate) !== 0 ? formatExperienceDate(experienceDate) : 'отсутствует'
                            }}</p>
                    </div>
                    <p>Образование: {{ education !== null ?  education.type : 'отсутствует' }}</p>
                </section>
            </div>
        </div>

        <!--NavBar-->
        <div class="details_nav pro_job_nav_details">
            <p :class="{'active': false}">Данные</p>
            <p :class="{'active': false}">Достижения</p>
        </div>

        <!--Education-->
        <div class="education_section" v-if="education">
            <h3>Образование</h3>
            <section class="education_details d-flex flex-column justify-content-between">
                <div class="d-flex flex_row align-items-center">
                    <p class="univer_name">{{ education.name }}</p>
                    <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon"
                         @click="showUpdateEducationModal = true; education_id = education.id" alt="edit icon">
                </div>
                <div class="d-flex flex_row align-items-center">
                    <p>Вид: {{ education.type }} </p>
                    <p>Период: {{ education.started.slice(0, 4) }}-{{ education.expired == null ? 'Учусь по сей день' : education.expired.slice(0,4)  }}</p>
                </div>
            </section>
        </div>

        <!--Experience-->
        <div class="education_section">
            <h3>Трудовой стаж</h3>
            <TransitionGroup name="list">
                <section :key="experience.id"
                         class="education_details experience_details d-flex flex-column justify-content-between"
                         v-for="experience in experiences">
                    <div class="d-flex flex-column justify-content-between">
                        <div class="d-flex flex_row">
                            <p class="univer_name">Место Работы: OOO "{{ experience.place }}” </p>
                            <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon" @click="showUpdateExperienceModal = true; experience_id = experience.id" alt="edit icon">
                        </div>
                        <div class="d-flex flex-column justify-content-between">
                            <p>Должность: {{ experience.post }}</p>
                            <p>Период: {{
                                    experience.started.slice(0, 4)
                                }}-{{
                                    experience.is_working === 0 ? experience.expired.slice(0, 4) : 'По сей день'
                                }}</p>
                        </div>
                    </div>
                </section>
            </TransitionGroup>
        </div>
        <button class="add_business flex-row add_experience" @click="showCreateJobExperienceModal = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p>Добавить информацию о трудовом стаже</p>
        </button>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
    <CreateJobExperienceModal v-if="showCreateJobExperienceModal" @goBack="close"
                         :job_id="this.job_id"></CreateJobExperienceModal>

    <UpdateEducationModal v-if="showUpdateEducationModal" @goBack="close"
                          :education_id="this.education_id"></UpdateEducationModal>
    <UpdateJobExperienceModal v-if="showUpdateExperienceModal" @goBack="close"
                          :experienceId="this.experience_id"></UpdateJobExperienceModal>
</template>

<style scoped>
</style>
