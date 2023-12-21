<script>
import axios from 'axios';
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';

export default {
    props: ['userId'],
    components: {
        // Importing Yandex Map components
        YandexMap,
        YandexMapDefaultSchemeLayer,
        YandexMapMarker,
        YandexMapDefaultFeaturesLayer,
        YandexMapListener,
    },
    data() {
        return {
            // State variables for address, coordinates, search, and map settings
            address: '',
            coords: [69.240562, 41.2800],
            searchQuery: '',
            suggestions: [],
            zoom: 9,
        };
    },
    methods: {
        // Method to save the address data to the server
        save() {
            const data = {
                user_id: this.userId,
                name: this.address,
                coords: this.coords
            }
            const headers = {
                'Authorization': `Bearer ` + localStorage.token,
                'Content-Type': 'application/json',
            };
            axios.post('/api/address', data, {headers}).then((response) => {
                this.$emit('goBack')
            })
        },
        // Method called when the map is clicked
        logMapClick(object, event) {
            // Set zoom and coordinates based on the clicked location
            this.zoom = {min: 1, max: 9};
            this.coords = event.coordinates;

            // Perform reverse geocoding to get the address of the clicked location
            ymaps.geocode(this.coords.reverse()).then((result) => {
                const firstGeoObject = result.geoObjects.get(0);

                if (firstGeoObject) {
                    // Extract address from the result
                    const address = firstGeoObject.getAddressLine();

                    // Set the address and searchQuery variables
                    this.address = address;
                    this.searchQuery = address;
                }
            });
            // Reverse the coordinates back to the original order
            this.coords.reverse();
        },
        // Method to handle input in the search field
        handleSearchInput() {
            if (this.searchQuery.length > 2) {
                // Initialize a new map for search and suggestion
                var myMap = new ymaps.Map('map', {
                    center: [55.75, 37.57],
                    zoom: 9,
                    controls: ['searchControl']
                });

                // Initialize search control
                let searchControl = new ymaps.control.SearchControl({
                    options: {
                        provider: 'yandex#search'
                    }
                });

                // Add search control to the map
                myMap.controls.add(searchControl);

                // Perform search based on the input query
                searchControl.search(this.searchQuery).then(() => {
                    var geoObjectsArray = searchControl.getResultsArray();
                    this.suggestions = geoObjectsArray;
                    console.log(this.suggestions)
                });

            } else {
                // Clear suggestions if input is too short
                this.suggestions = [];
            }
        },
        // Method to handle selection of a suggestion
        selectSuggestion(suggestion) {
            // Use the selected suggestion
            this.searchQuery = suggestion;
            this.performSearch();

            // Clear suggestions
            this.suggestions = [];
        },
        // Method to perform the search based on the selected suggestion
        performSearch() {
            // Set zoom to default
            this.zoom = 9;

            // Extract coordinates and address from the selected suggestion
            const coordinates = this.searchQuery.geometry.getCoordinates();
            this.address = this.searchQuery.properties.get('address');
            this.coords = coordinates.reverse();
            this.searchQuery = this.address;
        },
    }
};
</script>

<template>
    <!-- Main container for the address modal -->
    <div>
        <transition name="modal">
            <!-- Modal mask and wrapper -->
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <!-- Modal container for phone number editing -->
                    <div class="modal-container-phone-number-edit">
                        <!-- Modal header -->
                        <div class="header_modal">
                            <h3 class="add_address">Добавить адрес</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>

                        <!-- Map and search input section -->
                        <div class="custom-search">
                            <input v-model="searchQuery" placeholder="Адрес" class="form_input"
                                   @input="handleSearchInput"/>
                            <div v-if="suggestions.length > 0" class="suggestions">
                                <div v-for="suggestion in suggestions" :key="suggestion"
                                     @click="selectSuggestion(suggestion)">
                                    {{ suggestion.properties.get('address') }}
                                </div>
                            </div>
                        </div>
                        <div id="map" style="width: 100px"></div>
                        <div class="yaMap">
                            <!-- Yandex Map component -->
                            <yandex-map
                                :settings="{
                                location: {
                                  center:  coords,
                                  zoom: zoom,
                                },
                              }">
                                <!-- Default layers and features layer -->
                                <yandex-map-default-scheme-layer/>
                                <yandex-map-default-features-layer/>
                                <!-- Map click event listener -->
                                <yandex-map-listener :settings="{ onClick: logMapClick }"/>
                                <!-- Marker for the map -->
                                <yandex-map-marker
                                    :settings="{
                                        coordinates: coords,
                                    }"
                                >
                                    <template #default>
                                        <!-- Marker icon -->
                                        <img
                                            alt=""
                                            :src="'/images/icons/marker.svg'"
                                            :style="{
                                            width: '20px',
                                            position: 'relative',
                                            boxSizing: 'border-box',
                                            transform: 'translate(-50%, calc(-50% - 24px))',
                                            cursor: 'pointer',
                                          }"
                                        />
                                    </template>
                                </yandex-map-marker>
                            </yandex-map>
                        </div>

                        <!-- Footer with save button -->
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
/* Styling for modal mask */
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

/* Styling for modal container */
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

/* Styling for default button in the modal */
.modal-default-button {
    margin-top: 10px;
}

/* Styling for modal footer */
.modal-footer {
    justify-content: center;
}

/* Styling for email content */
.email_content p {
    color: #000;
    font-family: GT Walsheim Pro;
    font-size: 19px;
    font-style: normal;
    font-weight: 400;
    line-height: 20px;
    margin-bottom: 0;
}

/* Styling for the "Добавить адрес" header */
.add_address {
    font-weight: 500;
    font-size: 24px;
}

/* Styling for Yandex Map container */
.yaMap {
    width: 438px;
    height: 201px;
    border-radius: 15px;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
}

/* Styling for suggestions container */
.suggestions {
    margin-top: 4px;
    position: absolute;
    background-color: #fff;
    max-height: 150px;
    width: 438px;
    overflow-y: auto;
    box-shadow: 0 0 7px 0 rgba(0, 0, 0, 0.25);
    border-radius: 15px;
    z-index: 1;
}

/* Styling for individual suggestion item */
.suggestions div {
    padding: 8px;
    cursor: pointer;
}

/* Styling for suggestion item on hover */
.suggestions div:hover {
    background-color: #f0f0f0;
}

/* Media query for smaller screens */
@media screen and (max-width: 500px) {
    .modal-container-phone-number-edit {
        width: 406px;
        height: 492px;
        border-radius: 25px 25px 0px 0px;
    }

    .yaMap {
        width: 380px;
    }
    .suggestions{
        width: 380px !important;
    }
}


@media screen and (max-width: 390px) {
    .modal-container-phone-number-edit {
        width: 360px !important;
    }
    .yaMap {
        width: 320px !important;
    }
    .suggestions{
        width: 320px !important;
    }
}
</style>
