<script>
// Importing necessary modules
import axios from 'axios';

export default {
    // Component properties
    props: [
        'userId'
    ],
    // Component data initialization
    data() {
        return {
            // Data properties for the component
            inn: '',
            name_of_business: '',
            form_of_business: '',
            name_of_bank: '',
            name_of_banking_akkaunt: '',
            oked: '',
            address: '',
            mfo: '',
            payment_account: '',
            error: ''
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        // Perform actions on component mount if needed
    },
    // Component methods
    methods: {
        // Method to save business and bank data
        save() {
            const data = {
                'user_id': this.userId,
                'inn': this.inn,
                'name_of_business': this.name_of_business,
                'form_of_business': this.form_of_business,
                'oked': this.oked,
                'address': this.address,
            }
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };

            // Check if the name_of_bank is not empty
            if (this.name_of_bank !== '') {
                // Perform business and bank data creation
                axios.post('/api/pro-business', data, {headers}).then(res => {
                    const business_id = res.data.id;
                    const banks_data = {
                        'business_id': business_id,
                        'name': this.name_of_bank,
                        'name_of_banking_akkaunt': this.name_of_banking_akkaunt,
                        'mfo': this.mfo,
                        'payment_account': this.payment_account
                    }
                    axios.post('/api/banks-data', banks_data, {headers}).then(() => {
                        this.$emit('close'); // Close the modal after successful data creation
                    }).catch(err => {
                        this.error = err.response.data.message;
                    });
                }).catch(err => {
                    this.error = err.response.data.message;
                });
            } else {
                this.error = "Поле \"Наиминование банка\" является обязательным для заполнения.";
            }
        },
        // Method to fetch bank data based on MFO
        mfoBank() {
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.get('/api/bank-data-by-mfo', {headers}).then(res => {
                for (let i = 0; i < res.data.length; i++) {
                    if (res.data[i].code == this.mfo) {
                        this.name_of_bank = res.data[i].name
                    }
                }
            })
        }
    }
};
</script>

<template>
    <div>
        <!-- Modal transition -->
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <!-- Modal container for creating business -->
                    <div class="modal-container-create-business">
                        <!-- Header of the modal -->
                        <div class="header_modal">
                            <h4>Реквизиты</h4>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('close')"
                                alt="exit icon"
                            />
                        </div>
                        <!-- Main content of the modal -->
                        <div class="create-business-content">
                            <!-- Display error message if any -->
                            <div class="error">
                                <p>{{ error }}</p>
                            </div>
                            <!-- Input fields for business data -->
                            <div>
                                <label>ИНН бизнеса *</label>
                                <input type="number" v-model=inn class="form-input" placeholder="Введите ИНН" required>
                            </div>
                            <div>
                                <label>Название фирмы *</label>
                                <input type="text" v-model=name_of_business class="form-input"
                                       placeholder="Введите официальное юридическое имя" required>
                            </div>
                            <div>
                                <label>Форма бизнеса *</label>
                                <input type="text" v-model=form_of_business class="form-input"
                                       placeholder="Выберите форму бизнеса" required>
                            </div>
                            <div>
                                <label>ОКЭД бизнеса</label>
                                <input type="number" v-model=oked class="form-input" placeholder="Введите ОКЭД бизнеса"
                                       required>
                            </div>
                            <div>
                                <label>Юридический адрес</label>
                                <input type="text" v-model=address class="form-input"
                                       placeholder="Введите Юридический адрес">
                            </div>
                            <hr>
                            <h4>Банковские данные</h4>
                            <!-- Input fields for bank data -->
                            <div>
                                <label>Наиминование счета *</label>
                                <input type="text" v-model=name_of_banking_akkaunt class="form-input"
                                       placeholder="Введите наименование счет" required>
                            </div>
                            <div>
                                <label>МФО *</label>
                                <input type="number" v-model=mfo class="form-input" placeholder="Введите МФО"
                                       @input="mfoBank" required>
                            </div>
                            <div>
                                <label>Расчетный счет *</label>
                                <input type="number" v-model=payment_account class="form-input"
                                       placeholder="Введите Расчетный счет" required>
                            </div>
                            <div>
                                <input type="text" v-model=name_of_bank class="form-input"
                                       placeholder="Наимование банка" readonly>
                            </div>
                        </div>
                        <!-- Modal footer with Save button -->
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button" @click="save">
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
/* Styling for the modal component */
.modal-container-create-business {
    width: 512px;
    height: 970px !important;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-direction: column;
    padding: 20px;
    background: #FFF;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    border-radius: 40px;
}

.modal-default-button {
    margin-top: 10px;
}

.modal-footer {
    justify-content: center;
}

.create-business-content {
    margin-left: 17px;
}

label {
    margin-top: 10px;
    font-size: 20px;
    margin-left: 5px;
}

.form-input {
    width: 438px;
    height: 47px;
    border-radius: 15px;
    background: #FFF;
    box-shadow: 0px 0px 7px 0px rgba(0, 0, 0, 0.25);
    border: none;
    padding-left: 15px;
    font-size: 20px;
    margin-top: 10px;
}

.error{
    width: 438px;
}
.error p {
    font-size: 14px;
    text-align: center;
    color: #FF0000;
}


/* Styling for modal scrolling on screens with smaller height */
@media screen and (max-height: 970px) {
    .modal-container-create-business {
        height: 930px !important;
        border-radius: 25px 25px 25px 25px;
        overflow-y: scroll; /* Add this line to enable vertical scrolling */
    }

    .modal-container-create-business::-webkit-scrollbar {
        display: none; /* Safari and Chrome */
    }
}
/* Responsive styling for smaller screens */
@media screen and (max-width: 500px) {
    .modal-container-create-business {
        width: 400px !important; /* Set width to 100% for smaller screens */
        height: 525px !important; /* Let the height adjust based on content */
        border-radius: 25px 25px 0 0;
        overflow-y: auto; /* Add this line to enable vertical scrolling */
    }
    .create-business-content div {
        width: 380px;
    }
    .create-business-content {
        margin-left: 2px;
    }

    /* Adjust other styles for smaller screens as needed */
    .form-input {
        width: 380px !important; /* Set width to 100% for smaller screens */
    }

    .create-business-content {
        margin-left: 0 !important;
    }
}
</style>
