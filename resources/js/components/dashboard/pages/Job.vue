<script>

// Importing other components
import CreateJobModal from "../job/modal/create/CreateJobModal.vue";
import UpdateJobModal from "../job/modal/update/UpdateJobModal.vue";
import ShowProJob from "../job/ShowProJob.vue";

export default {
    components: {
        CreateJobModal,
        ShowProJob,
        UpdateJobModal
    },
    data() {
        return {
            // Backend data
            job_id: null,
            jobs: [],

            // Frontend state
            showJob: false,
            showJobModal: false,
            showUpdateJobModal: false,
            loading: false,
        }
    },
    mounted() {
        this.fetchJobs();
    },
    methods: {
        fetchJobs(){
            axios.get("/api/job").then((res) => {
                this.jobs = res.data

                this.loading = true
            })
        },
        formatExperienceDate(milliseconds) {
            if(milliseconds < 0){
                milliseconds *= -1
            }

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
        goBack() {
            this.fetchJobs();
            this.showJobModal = this.showUpdateJobModal = this.showJob = false;
        }
    }
}
</script>

<template>
    <div v-if="loading && !showJob">
        <h3>Профессии</h3>
        <section class="job">
            <!-- Jobs -->
            <TransitionGroup name="list">
                <div class="job-container" v-for="job in jobs" :key="job.id">
                    <div class="job-header flex-row">
                        <div class="job-items" @click="job_id = job.id; showJob = true">

                            <p>Профессия: {{ job.type.slice(0, 8) + '...' }}</p>
                            <span>Специализация: {{job.profession}}</span>
                        </div>
                        <img src="/images/icons/dashboard/edit.svg" width="25" alt="Edit Icon" @click="job_id = job.id; showUpdateJobModal = true"/>
                    </div>

                    <p class="experience" v-if="job.experience_count !== undefined && job.experience_count !== null" @click="job_id = job.id; showJob = true">
                        Стаж: {{ formatExperienceDate(job.experience_count) }}
                    </p>
                    <p class="experience" v-else @click="job_id = job.id; showJob = true">Стаж: отсутствует</p>
                </div>
            </TransitionGroup>

            <!-- Add new job -->
            <div class="job-container add-job-container" @click="showJobModal = true">
                <img src="/images/icons/dashboard/add_white.svg" alt="Add Icon" class="add_new_icon job_add_icon"/>
                <div class="education-items flex-column">
                    <p class="add_new_text">Добавить<br>
                        новую профессию</p>
                </div>
            </div>
        </section>
    </div>

    <div v-if="loading && !showJob">
        <div class="links_to_services">
            <h3>Полезные ссылки</h3>
            <!-- Link 1 -->
            <div class="add_business flex-row links">
                <span><a href="#">Проверить информацию о трудоустройстве на Мехнат Уз.</a></span>
            </div>
            <!-- Link 2 -->
            <div class="add_business flex-row links" onclick="window.location.href='https://my.gov.uz/ru/service/357'">
                <span><a href="https://my.gov.uz/ru/service/357">Получение ЭЦП (Электронная цифровая подпись)</a></span>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div v-if="!loading" class="loading-indicator">
        Loading...
    </div>
    <CreateJobModal v-if="showJobModal" @goBack="goBack"></CreateJobModal>
    <UpdateJobModal v-if="showUpdateJobModal" @goBack="goBack" :jobId="this.job_id"></UpdateJobModal>
    <ShowProJob v-if="showJob" @goBack="goBack" :job_id="this.job_id"></ShowProJob>
</template>

<style scoped>
</style>
