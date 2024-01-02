<script>
import CreateJobExperience from "@/components/dashboard/ProJob/modal/CreateJobExperience.vue";
import UpdateEducationModal from "@/components/dashboard/data/modal/UpdateEducationModal.vue";

export default {
    props: ['job_id'],
    components: {
        CreateJobExperience,
        UpdateEducationModal
    },
    data() {
        return {
            job: [],
            education: [],
            experiences: [],
            experienceDate: 0,
            education_id: null,
            loading: false,

            //modal
            showCreateJobExperienceModal: false,
            showUpdateEducationModal: false,
        }
    },
    mounted() {
        this.fetchData()
    },
    methods: {
        fetchData() {
            axios.get(`/api/job-show/${this.job_id}`).then(res => {
                this.job = res.data

                //fetch education
                if(this.job.education_id != null){
                    axios.get(`/api/education-show/${this.job.education_id}`).then(res => {
                        this.education = res.data
                    })
                }

                //fetch job experience
                axios.get(`/api/experience/${this.job_id}`).then(res => {
                    this.experiences = res.data

                    this.experiences.map((date) => {
                        const startedDate = new Date(date.started);
                        const expiredDate = new Date(date.expired);

                        const experienceDates = Math.abs(startedDate - expiredDate);
                        this.experienceDate += experienceDates
                    })

                    if (this.experienceDate > 0) {
                        axios.put(`/api/job-experience/${this.job_id}`, {
                            'experience_count': this.experienceDate
                        });
                        console.log(this.experienceDate)
                    }

                    this.loading = true
                })


            })
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
            this.fetchData();
            this.showCreateJobExperienceModal = this.showUpdateEducationModal = false;
            // this.experienceDate = 0;
        },
    },

}
</script>

<template>
    <div v-if="loading">
        <!--Job Details-->
        <div>
            <div class="d-flex">
                <img src=/images/icons/dashboard/back.svg width="13" height="23" class="back back_job"
                     @click="$emit('goBack')">
                <section class="job_info d-flex flex-column justify-content-between">
                    <h3>Профессия: {{ job.profession }}</h3>
                    <div class="flex_row justify-content-between w-100">
                        <p>Специальность: {{ job.type }}</p>
                        <p>Стаж: {{
                                parseInt(experienceDate) !== 0 ? formatExperienceDate(experienceDate) : 'отсутствует'
                            }}</p>
                    </div>
                    <p>Образование: {{ education.hasOwnProperty('id') ? 'Высшее' : 'отсутствует' }}</p>
                </section>
            </div>
        </div>

        <!--NavBar-->
        <div class="details_nav pro_job_nav_details">
            <p :class="{'active': false}">Данные</p>
            <p :class="{'active': false}">Достижения</p>
        </div>

        <!--Education-->
        <div class="education_section" v-if="education.hasOwnProperty('id')">
            <h3>Образование</h3>
            <section class="education_details d-flex flex-column justify-content-between">
                <div class="d-flex flex_row align-items-center">
                    <p class="univer_name">{{ education.name }}</p>
                    <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon"
                         @click="showUpdateEducationModal = true; education_id = education.id">
                </div>
                <div class="d-flex flex_row align-items-center">
                    <p>Вид: {{ education.type }} </p>
                    <p>Период: {{ education.started.slice(0, 4) }}-{{ education.expired.slice(0, 4) }}</p>
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
                            <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon">
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
        <div class="add_business flex-row add_experience" @click="showCreateJobExperienceModal = true">
            <img src="/images/icons/dashboard/add.svg" alt="">
            <p>Добавить информацию о трудовом стаже</p>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
    <CreateJobExperience v-if="showCreateJobExperienceModal" @goBack="close"
                         :job_id="this.job_id"></CreateJobExperience>

    <UpdateEducationModal v-if="showUpdateEducationModal" @goBack="close"
                          :education_id="this.education_id"></UpdateEducationModal>
</template>

<style scoped>

</style>
