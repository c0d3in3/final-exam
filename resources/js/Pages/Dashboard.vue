<template>
    <Head title="Covid 19 Problems" />

    <BreezeAuthenticatedLayout>
        <div class="content py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200 flex">
                        <div class="w-2/3 h-96">
                            <h1>World Map</h1>
                            <div class="h-full max-w-full" id="covid-map"></div>
                        </div>

                        <div class="w-1/3">
                            <h1>Global Stats</h1>
                            <div
                                class="flex flex-col items-start justify-between added-country"
                            >
                                <div class="flex w-full justify-between">
                                    <div class="flex p-1">
                                        <img
                                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/International_Flag_of_Planet_Earth_%28Variant%29.svg/1200px-International_Flag_of_Planet_Earth_%28Variant%29.svg.png"
                                            style="width: 50px; height: 30px"
                                        />

                                        <p class="ml-2 font-bold">World</p>
                                    </div>
                                </div>

                                <div class="flex">
                                    <div class="shadow-3 stats">
                                        <div>
                                            <p>{{ worldStats.todayCases }}</p>
                                            <span>Today Cases</span>
                                        </div>
                                    </div>
                                    <div class="shadow-3 stats">
                                        <div>
                                            <p>{{ worldStats.todayDeaths }}</p>
                                            <span>Today Deaths</span>
                                        </div>
                                    </div>
                                    <div class="shadow-3 stats">
                                        <div>
                                            <p>
                                                {{ worldStats.todayRecovered }}
                                            </p>
                                            <span>Today Recovered</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="shadow p-2">
                                <h3 class="text-x1">Add Your Country</h3>
                                <div class="input-button flex">
                                    <Select2
                                        class="w-96"
                                        placeholder="Select country"
                                        v-model="selectedCountry"
                                        :options="myOptions"
                                        :settings="{
                                            width: '100%',
                                            settingOption: value,
                                            ajax: {
                                                url: '/api/countries/list',
                                                dataType: 'json',
                                            },
                                        }"
                                        @select="savedCountrySelected($event)"
                                    />
                                    <a
                                        href="javascript:void(0);"
                                        @click="saveCountry"
                                        class="add-button pt-1 text-md font-semibold shadow hover:shadow-x1"
                                        >Add</a
                                    >
                                </div>

                                <div v-for="c in savedCountries" :key="c.id">
                                    <div
                                        class="flex flex-col items-start justify-between added-country"
                                    >
                                        <div
                                            class="flex w-full justify-between"
                                        >
                                            <div class="flex p-1">
                                                <img
                                                    v-bind:src="c.flagUrl"
                                                    style="
                                                        width: 50px;
                                                        height: 30px;
                                                    "
                                                />

                                                <p class="ml-2 font-bold">
                                                    {{ c.name }}
                                                </p>
                                            </div>

                                            <a
                                                href="javascript:void(0);"
                                                @click="deleteSaved(c.id)"
                                                class="flex items-center pt-1 text-md font-semibold shadow hover:shadow-x1 country-button"
                                                >X</a
                                            >
                                        </div>

                                        <div class="flex">
                                            <div class="shadow-3 stats">
                                                <div>
                                                    <p>{{ c.todayCases }}</p>
                                                    <span>Today Cases</span>
                                                </div>
                                            </div>
                                            <div class="shadow-3 stats">
                                                <div>
                                                    <p>{{ c.todayDeaths }}</p>
                                                    <span>Today Deaths</span>
                                                </div>
                                            </div>
                                            <div class="shadow-3 stats">
                                                <div>
                                                    <p>
                                                        {{ c.todayRecovered }}
                                                    </p>
                                                    <span>Today Recovered</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from "@/Layouts/Authenticated.vue";
import { Head } from "@inertiajs/inertia-vue3";
import Select2 from "vue3-select2-component";

export default {
    components: {
        Select2,
        BreezeAuthenticatedLayout,
        Head,
    },
    data() {
        return {
            selectedCountry: "",
            savedCountries: [],
            map: {},
            worldStats: {},
        };
    },
    methods: {
        savedCountrySelected({ id, text }) {
            console.log({ id, text });
        },
        getSavedCountries() {
            axios.get("/api/countries/saved/").then((response) => {
                this.savedCountries = response.data;
                this.savedCountries.forEach((element) => {
                    this.map.updateChoropleth({
                        [element.code]: "#ff0000",
                    });
                });
            });
        },
        saveCountry() {
            if (
                this.selectedCountry !== "" &&
                this.savedCountries.find((x) => x.id == this.selectedCountry) ==
                    undefined
            ) {
                axios
                    .post("/api/countries/save-country", {
                        countryID: this.selectedCountry,
                    })
                    .then((response) => {
                        this.savedCountries.push(response.data);
                        this.map.updateChoropleth({
                            [response.data["code"]]: "#ff0000",
                        });
                    });
            } else {
                console.warn("1you must choose country first");
            }
        },
        deleteSaved(id) {
            axios
                .delete("/api/countries/delete-saved-country", {
                    params: {
                        countryID: id,
                    },
                })
                .then((response) => {
                    var idx = 0;
                    var code = "";
                    this.savedCountries.forEach(function callback(
                        value,
                        index
                    ) {
                        if (value.id == id) {
                            idx = index;
                            code = value.code;
                        }
                    });
                    this.savedCountries.splice(idx, 1);
                    this.map.updateChoropleth({
                        [code]: "#ABDDA4",
                    });
                });
        },
        initMap() {
            this.map = new Datamap({
                element: document.getElementById("covid-map"),
                projection: "mercator",
                fills: {
                    defaultFill: "#ABDDA4",
                },
            });
        },
        getWorldStats() {
            axios.get("/api/countries/world").then((response) => {
                this.worldStats = response.data;
                console.log(this.worldStats);
            });
        },
    },
    mounted() {
        this.$nextTick(function () {
            this.initMap();
        });
    },
    created() {
        this.getSavedCountries();
        this.getWorldStats();
    },
};
</script>
