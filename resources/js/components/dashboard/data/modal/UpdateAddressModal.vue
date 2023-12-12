<script>
import axios from 'axios';
import {ref} from "vue";

export default {
    props: ['address_id'],
    data() {
        const coords = ref({lat: 18.7557237, lng: 73.4090757});
        const markerDetails = ref({
            id: 1,
            position: coords.value
        });

        const locationDetails = ref({
            address: '',
            url: ""
        });

        const openedMarkerID = ref(null);

        const setPlace = (place) => {
            if (place.geometry && place.geometry.location) {
                coords.value.lat = place.geometry.location.lat();
                coords.value.lng = place.geometry.location.lng();
                // Update the location details
                locationDetails.value.address = place.formatted_address;
                locationDetails.value.url = place.url;
                this.address = locationDetails.value.address
                console.log(coords)
            } else {
                console.error('Invalid place object:', place);
            }
        };


        return {
            coords,
            markerDetails,
            locationDetails,
            openedMarkerID,
            setPlace,
            address: '',
        };
    },
    mounted() {
        axios.get(`/api/address-show/${this.address_id}`).then(res => {
            this.address = res.data.name
        })
    },
    methods: {
        openMarker(id) {
            // Implement the method if needed
        },
        save(){
            const data = {
                name: this.address,
            }
            axios.put(`/api/address/${this.address_id}`, data).then(() => {

                this.$emit('goBack')
            })
        }
    }
};
</script>

<template>
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container-phone-number-edit">
                        <div class="header_modal">
                            <h3 class="add_address">Добавить адрес</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>

                        <GMapMap
                            :center="{ lat: 51.5072, lng: 0.1276 }"
                            :zoom="10"
                            class="map"
                        >
                            <GMapMarker
                                :key="markerDetails.id"
                                :position="markerDetails.position"
                                :clickable="true"
                                :draggable="false"
                                @click="openMarker(markerDetails.id)"
                            >
                                <!-- InfoWindow to display the searched location details -->
                                <GMapInfoWindow
                                    v-if="locationDetails.address !== ''"
                                    :closeclick="true"
                                    @closeclick="openMarker(null)"
                                    :opened="openedMarkerID === markerDetails.id"
                                    :options="{
                                        pixelOffset: {
                                            width: 10,
                                            height: 0,
                                        },
                                        maxWidth: 320,
                                        maxHeight: 320
                                    }"
                                    class="search_options"
                                >
                                    <!-- Content for the InfoWindow can be added here -->
                                </GMapInfoWindow>
                                <GMapAutocomplete
                                    placeholder="Search for a location (e.g., city, street)"
                                    @place_changed="setPlace"
                                    style="
                                        max-width: 438px;
                                        width: 100%;
                                        height: 61px;
                                        box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
                                        border-radius: 15px;
                                        border:none;
                                        padding-left: 10px;
                                        margin-top: 30px;"
                                    :value=address
                                ></GMapAutocomplete>
                            </GMapMarker>
                        </GMapMap>
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
.modal-mask {
    position: fixed;
    z-index: 1 !important;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}
.search_options{
    z-index: -1;
}
.modal-container-phone-number-edit {
    width: 512px;
    height: 440px;
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

.email_content p {
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}

.add_address {
    font-weight: 500;
    font-size: 24px;
}
.map{
    margin-top: -100px;
    width: 438px;
    height: 201px;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    border-radius: 15px;
}
@media screen and (max-width: 500px) {
    .modal-container-phone-number-edit {
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }
    .map{
        width: 364px;
    }
    .input_field{
        max-width: 406px;
    }
}
</style>
