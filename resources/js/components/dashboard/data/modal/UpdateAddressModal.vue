<script>
import axios, { formToJSON } from 'axios';
import {
    YandexMap,
    YandexMapDefaultSchemeLayer,
    YandexMapDefaultFeaturesLayer,
    YandexMapMarker,
    YandexMapListener,
} from 'vue-yandex-maps';

export default {
    props: ['address_id'],
    components: {
        YandexMap,
        YandexMapDefaultSchemeLayer,
        YandexMapMarker,
        YandexMapDefaultFeaturesLayer,
        YandexMapListener,
    },
    data() {
        return {
            // State variables for address, coordinates, search query, suggestions, zoom, and loading state
            address: '',
            coords: [69.240562, 41.2800],
            searchQuery: '',
            suggestions: [],
            zoom: 9,
            loading: false
        };
    },
    mounted() {
        axios.get(`/api/address-show/${this.address_id}`).then(res => {
            this.address = res.data.name
            this.searchQuery = res.data.name
            this.coords = JSON.parse(res.data.coords)
            this.loading = true
        })
    },
    methods: {
        // Method to save the address data to the server
        save() {
            const data = {
                name: this.address,
                coords: this.coords
            }
            axios.put(`/api/address/${this.address_id}`, data).then(() => {
                this.$emit('goBack')
            })
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
    <div>
        <transition name="modal">
            <div class="modal-mask">
                <div class="modal-wrapper">
                    <div class="modal-container modal-container-address-phone-mail" v-if="loading">
                        <div class="header_modal">
                            <h3 class="add_address">Изменить адрес</h3>
                            <img
                                src="/images/icons/dashboard/exit.svg"
                                @click="$emit('goBack')"
                                alt="exit icon"
                            />
                        </div>

                        <!-- Map Section -->
                        <div class="custom-search">
                            <input v-model="searchQuery" placeholder="Адрес" class="form_input"
                                   @input="handleSearchInput" />
                            <div v-if="suggestions.length > 0" class="suggestions">
                                <div v-for="suggestion in suggestions" :key="suggestion"
                                     @click="selectSuggestion(suggestion)">
                                    {{ suggestion.properties.get('address') }}
                                </div>
                            </div>
                        </div>
                        <div id="map" style="width: 100px"></div>
                        <div class="yaMap">
                            <yandex-map
                                :settings="{
                                location: {
                                  center: coords,
                                  zoom: zoom,
                                },
                              }">
                                <yandex-map-default-scheme-layer />
                                <yandex-map-default-features-layer />
                                <yandex-map-listener :settings="{
                                    onUpdate : moveMap
                                }"/>
                                <yandex-map-marker
                                    :settings="{
                                        coordinates: coords,
                                    }"
                                >
                                    <template #default>
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

                        <!-- Footer Section -->
                        <div class="modal-footer">
                            <slot name="footer">
                                <button class="modal-default-button address-save-btn" @click="save">
                                    Сохранить
                                </button>
                            </slot>
                        </div>
                    </div>
                    <!-- Loading indicator -->
                    <div v-if="!loading" class="loading-indicator">
                        Loading...
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
</style>

