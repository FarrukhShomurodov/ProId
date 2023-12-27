<script>
import CreateJobModal from "@/components/dashboard/ProJob/modal/CreateJobModal.vue";
import ShowProJob from "@/components/dashboard/ProJob/ShowProJob.vue";
export default {
    components: {
        CreateJobModal,
        ShowProJob,
    },
    data() {
        return {
            showJobModal: false,
            user_id: null,
            job_id: null,
            jobs: [],
            loading: false,
            showJob: false
        }
    },
    mounted() {
        this.getData();
    },
    methods: {
        getData(){
            axios.get('/api/user').then(res => {
                this.user_id = res.data.id

                //getting jobs by user
                axios.get(`/api/job/${this.user_id}`).then(res => this.jobs = res.data, this.loading = true)
            })
        },
        goBack() {
            this.getData();
            this.showJobModal = this.showJob = false;
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
                <div class="job-container" v-for="job in jobs" @click="job_id = job.id; showJob = true" :key="job.id">
                    <div class="job-header flex-row">
                        <div class="job-items">

                            <p>Профессия: {{ job.type.slice(0, 8) + '...' }}</p>
                            <span>Специализация: {{job.profession}}</span>
                        </div>
                        <img src="/images/icons/dashboard/edit.svg" width="25px" alt="Edit Icon"/>
                    </div>
                    <p class="experience">Стаж: 10 год 2 мес.</p>
                </div>
            </TransitionGroup>

            <!-- Add new job -->
            <div class="job-container add-job-container">
                <img src="/images/icons/dashboard/add_white.svg" alt="Add Icon" class="add_new_icon job_add_icon"
                     @click="showJobModal = true"/>
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
    <CreateJobModal v-if="showJobModal" @goBack="goBack" :userId="this.user_id"></CreateJobModal>
    <ShowProJob v-if="showJob" @goBack="goBack" :user_id="this.user_id" :job_id="this.job_id"></ShowProJob>
</template>

<style scoped>
</style>
