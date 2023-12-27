<script>
import CreateJobExperience from "@/components/dashboard/ProJob/modal/CreateJobExperience.vue";
export default {
    props:['user_id', 'job_id'],
    components:{
        CreateJobExperience,
    },
    data(){
        return {
            job: [],
            educations: [],
            experiences: [],
            loading: false,

            //modal
            showCreateJobExperienceModal: false,
        }
    },
    mounted() {
        this.fetchData()
    },
    methods:{
        fetchData(){
            axios.get(`/api/job-show/${this.job_id}`).then(res =>{
                this.job = res.data

                //fetch education
                axios.get(`/api/education/${this.user_id}`).then(res => {
                    this.educations = res.data
                })

                //fetch job experience
                axios.get(`/api/experience/${this.job_id}`).then(res => {
                    this.experiences = res.data
                    this.loading = true;
                })


            })
        },
        close(){
            this.fetchData();
            this.showCreateJobExperienceModal = false;
        }
    },

}
</script>

<template>
    <div v-if="loading">
        <!--Job Details-->
        <div>
            <div class="d-flex">
                <img src=/images/icons/dashboard/back.svg width="13px" height="23px" class="back back_job" @click="$emit('goBack')">
                <section class="job_info d-flex flex-column justify-content-between">
                    <h3>Профессия: {{ job.profession }}</h3>
                    <div class="flex_row justify-content-between w-100">
                        <p>Специальность: {{ job.type }}</p>
                        <p>Стаж: отсутствует</p>
                    </div>
                    <p>Образование: Высшее</p>
                </section>
            </div>
        </div>

        <!--NavBar-->
        <div class="details_nav pro_job_nav_details">
            <p :class="{'active': false}">Данные</p>
            <p :class="{'active': false}" >Кассы</p>
        </div>

        <!--Education-->
        <div class="education_section">
            <h3>Образование</h3>
            <section class="education_details d-flex flex-column justify-content-between" v-for="education in this.educations">
                <div class="d-flex flex_row align-items-center">
                    <p class="univer_name">{{ education.name }}</p>
                    <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon">
                </div>
                <div class="d-flex flex_row align-items-center">
                    <p>Вид: {{ education.type }} </p>
                    <p>Период: {{ education.started.slice(0,4) }}-{{ education.expired.slice(0,4) }}</p>
                </div>
            </section>
        </div>

        <!--Experience-->
        <div class="education_section">
            <h3>Трудовой стаж</h3>
            <TransitionGroup name="list">
                <section :key="experience.id" class="education_details experience_details d-flex flex-column justify-content-between" v-for="experience in experiences">
                    <div class="d-flex flex-column justify-content-between">
                        <div class="d-flex flex_row">
                            <p class="univer_name">Место Работы: OOO "{{ experience.place }}” </p>
                            <img src="/images/icons/dashboard/edit.svg" class="edit_education_icon">
                        </div>
                        <div class="d-flex flex-column justify-content-between">
                            <p>Должность: {{ experience.post }}</p>
                            <p>Период: {{ experience.started.slice(0, 4) }}-{{experience.is_working === 0 ? experience.expired.slice(0, 4) : 'По сей день'}}</p>
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
    <CreateJobExperience v-if="showCreateJobExperienceModal" @goBack="close" :job_id="this.job_id"></CreateJobExperience>
</template>

<style scoped>

</style>
