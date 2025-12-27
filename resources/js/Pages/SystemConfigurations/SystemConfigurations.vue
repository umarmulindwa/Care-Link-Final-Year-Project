<script setup>
import Layout from "../../Layouts/main.vue";
import { reactive, onMounted, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import SystemVideos from "../Support/SystemVideos.vue";
import GeneralConfig from "./GeneralConfig.vue";
import CountryOffice from "./CountryOffice.vue";
import ForexRates from "./ForexRates.vue";
import UnspscProducts from "./UnspscProducts.vue";

//Default Layout
defineOptions({ layout: Layout });
//props
const props = defineProps({
    systemVideos: Array,
    generalConfigs: Object,
    districts: Array,
    banks: Array,
    pillars: Array,
    sections: Array,
    organisationUnit: Array,
    grade: Array,
    categories: Array,
    appointmentType: Array,
    rooms: Array,
    rates:Array,
    field_offices:Array,
    staff:Array,
    donors:Array,
});
//data
const state = reactive({
    title: "General Configuration",
    items: [
        {
            text: "Email",
            href: "/",
        },
        {
            text: "Inbox",
            active: true,
        },
    ],
});

//methods
//mounted
onMounted(() => {
    document.body.classList.remove("side-bg");
});

//computed
</script>

<template>
    <Head title="System Configuration" />
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5 mt-4">
                    <h4>{{ state.title }}</h4>
                </div>
            </div>
        </div>
        <div class="w-100 d-flex flex-row justify-content-between">
            <div class="d-flex flex-column gap-3" style="width: 10%">
                <div class="card" role="button" :class="state.title == 'General Configuration' ? 'bg-primary text-white' : 'text-muted'" @click="state.title = 'General Configuration'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="mt-2">
                            <i class="bx bx-cog nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="mb-2 text-center">
                            <p class="fw-bold mb-0">General Configuration</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'UNICEF South Sudan'" :class="state.title == 'UNICEF South Sudan' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="bx bx-buildings nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="text-center">
                            <p class="fw-bold mb-0">UNICEF South Sudan</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Forex Rates'" :class="state.title == 'Forex Rates' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="bx bx-money nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="text-center">
                            <p class="fw-bold mb-2">Forex Rates</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'UNSPSC Products/Services'" :class="state.title == 'UNSPSC Products/Services' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div>
                            <i class="bx bx-purchase-tag nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="text-center">
                            <p class="fw-bold mb-2">UNSPSC Products/Services</p>
                        </div>
                    </div>
                </div>
                <div class="card" role="button" @click="state.title = 'Sytem Videos'" :class="state.title == 'Sytem Videos' ? 'bg-primary text-white' : 'text-muted'">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="mt-2">
                            <i class="bx bxs-videos nav-icon d-block mb-2" style="font-size: large"></i>
                        </div>
                        <div class="mb-2 text-center">
                            <p class="fw-bold mb-0">Sytem Videos</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="width: 87%">
                <div class="card-body" id="staff_reg" v-if="state.title == 'General Configuration'">
                    <GeneralConfig :generalConfigs="props.generalConfigs" :disticts="props.districts" :banks="props.banks" :donors="props.donors" />
                </div>
                <div class="card-body" id="requestStats" v-else-if="state.title == 'UNICEF South Sudan'">
                    <CountryOffice
                        :="props.pillarspillars"
                        :sections="props.sections"
                        :organisationUnit="props.organisationUnit"
                        :grade="props.grade"
                        :categories="props.categories"
                        :appointmentType="props.appointmentType"
                        :rooms="props.rooms"
                        :pillars="props.pillars"
                        :field_offices = "props.field_offices"
                        :staff="props.staff"
                    />
                </div>
                <div class="card-body" id="faq" v-else-if="state.title == 'Forex Rates'"><ForexRates :rates="props.rates" /></div>
                <div class="card-body" id="faq" v-else-if="state.title == 'UNSPSC Products/Services'"><UnspscProducts /></div>
                <div class="card-body" id="system_videos" v-else-if="state.title == 'Sytem Videos'">
                    <SystemVideos :videos="props.systemVideos" />
                </div>
            </div>
        </div>
    </div>
</template>
