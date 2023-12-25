<script>
import axios from 'axios';
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';
import {setConsent} from "https://www.gstatic.com/firebasejs/10.6.0/firebase-analytics.js";

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
            axios.post('/api/address', data).then((response) => {
                this.$emit('goBack')
            })
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
        // Method center marker when move
        moveMap(event) {
            this.zoom = { min: 1, max: 9 };
            this.coords = event.location.center;

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
        }
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
                    <div class="modal-container modal-container-address-phone-mail">
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
                                <yandex-map-listener :settings="{
                                    onUpdate : moveMap
                                }"/>
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
                                <button class="modal-default-button address-save-btn" @click="save">
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

<style scoped>
</style>
