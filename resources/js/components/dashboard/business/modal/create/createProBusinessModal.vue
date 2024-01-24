<script>
// Importing necessary modules
import axios from 'axios';

export default {
    // Component properties
    props: [
        'userId'
    ],
    emits: ['close'],
    // Component data initialization
    data() {
        return {
            // Data properties for the component
            inn: '',
            name_of_business: 'OOO ',
            form_of_business: 'Частное предприятие - ЧП',
            name_of_bank: '',
            name_of_banking_akkaunt: '',
            oked: '',
            address: '',
            mfo: '',
            payment_account: '',
            show: false,
            error: ''
        }
    },
    // Component lifecycle hook - called when the component is mounted
    mounted() {
        // Perform actions on component mount if needed
        this.show = true
        $('#select').select2();
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

            // Check if the name_of_bank is not empty
            if (this.name_of_bank !== '') {
                // Perform business and bank data creation
                axios.post('/api/pro-business', data).then(res => {
                    const business_id = res.data.id;
                    const banks_data = {
                        'business_id': business_id,
                        'name': this.name_of_bank,
                        'name_of_banking_akkaunt': this.name_of_banking_akkaunt,
                        'mfo': this.mfo,
                        'payment_account': this.payment_account
                    }
                    axios.post('/api/banks-data', banks_data).then(() => {
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
            axios.get('/api/bank-data-by-mfo').then(res => {
                // Find and set the bank name based on MFO
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
        <transition name="modal-entire">
            <div class="modal-mask" v-show="show">
                <div class="modal-wrapper">
                    <transition name="modal">
                        <!-- Modal container for creating business -->
                        <div class="modal-container modal-container-create-business" v-show="show">
                            <!-- Header of the modal -->
                            <div class="header_modal header_modal-business">
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
                                    <input type="number" v-model=inn class="form_input form_input-business"
                                           :class="{'form_input_error': error && inn.length === 0}"
                                           placeholder="Введите ИНН" required>
                                </div>
                                <div>
                                    <label>Название фирмы *</label>
                                    <input type="text" v-model=name_of_business class="form_input form_input-business"
                                           :class="{'form_input_error': error && name_of_business.length === 0}"
                                           placeholder="Введите официальное юридическое имя" required>
                                </div>
                                <div>
                                    <label>Форма бизнеса *</label>
                                    <select v-model=form_of_business class="form_input form_input-business" id="select"
                                            required>
                                        <option value="Частное предприятие - ЧП">Частное предприятие - ЧП</option>
                                        <option value="Семейное предприятие - СП">Семейное предприятие - СП</option>
                                        <option value="Фермерское хозяйство - ФХ">Фермерское хозяйство - ФХ</option>
                                        <option value="Производственный кооператив - ПК">Производственный кооператив -
                                            ПК
                                        </option>
                                        <option value="Коммандитная товарищества - КТ">Коммандитная товарищества - КТ
                                        </option>
                                        <option value="Общество с ограниченной ответственностью - ООО">Общество с
                                            ограниченной ответственностью - ООО
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label>ОКЭД бизнеса</label>
                                    <input type="number" v-model=oked class="form_input form_input-business"
                                           placeholder="Введите ОКЭД бизнеса"
                                           :class="{'form_input_error': error && oked.length === 0}"
                                           required>
                                </div>
                                <div>
                                    <label>Юридический адрес</label>
                                    <input type="text" v-model=address class="form_input form_input-business"
                                           :class="{'form_input_error': error && address.length === 0}"
                                           placeholder="Введите Юридический адрес">
                                </div>
                                <hr>
                                <h4>Банковские данные</h4>
                                <!-- Input fields for bank data -->
                                <div>
                                    <label>Наиминование счета *</label>
                                    <input type="text" v-model=name_of_banking_akkaunt
                                           class="form_input form_input-business"
                                           :class="{'form_input_error': error && name_of_banking_akkaunt.length === 0}"
                                           placeholder="Введите наименование счет" required>
                                </div>
                                <div>
                                    <label>МФО *</label>
                                    <input type="number" v-model=mfo class="form_input form_input-business"
                                           placeholder="Введите МФО"
                                           :class="{'form_input_error': error && mfo.length === 0}"
                                           @input="mfoBank" required>
                                </div>
                                <div>
                                    <label>Расчетный счет *</label>
                                    <input type="number" v-model=payment_account class="form_input form_input-business"
                                           :class="{'form_input_error': error && payment_account.length === 0}"
                                           placeholder="Введите Расчетный счет" required>
                                </div>
                                <div>
                                    <input type="text" v-model=name_of_bank class="form_input form_input-business"
                                           :class="{'form_input_error': error && name_of_bank.length === 0}"
                                           placeholder="Наимование банка" readonly>
                                </div>
                            </div>
                            <!-- Modal footer with Save button -->
                            <div class="modal-footer">
                                <slot name="footer">
                                    <button class="modal-default-button modal-default-button-business" @click="save">
                                        Сохранить
                                    </button>
                                </slot>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
</style>
