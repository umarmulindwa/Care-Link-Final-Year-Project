<script setup>
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import empty from "../../../images/empty.svg";
import { GoogleMap, Marker, Polygon } from "vue3-google-map";


import Layout from "../../Layouts/main.vue";
// import PageHeader from "../../components/page-header.vue";

import { reactive, onMounted, computed, watch } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import moment from "moment";
import Checkbox from "@/Components/Checkbox.vue";
import Swal from "sweetalert2";
//Default Layout
defineOptions({ layout: Layout });

//props
const props = defineProps({
    AdminLogs: Array,


});
//data
const state = reactive({
    searchText: '',
    requestMedicationModal: false,
    makeFormDisabled: false,
    myself: true,
    anotherPerson: false,
    anotherSelectedPatient: null,
    users: ['Patient1', 'Patient2', 'patient3'],
    hospital: null,
    hospitals: ['Hospital1', 'hospital2', 'hospital3'],
    medication: null,
    medications: ['Medication1', 'Medication2', 'Medication3'],
    delivery: true,
    pickup: false,
    markCenter: { lat: 0.363889, lng: 32.528611 },
});

//methods
//mounted
onMounted(() => {
    //changing filter by value

});

function disableAnother() {
    state.anotherPerson = false
    state.myself = true
}

function disableMyself() {
    state.myself = false
    state.anotherPerson = true
}

function disablepickup() {
    state.pickup = false
    state.delivery = true
}

function disableDelivery() {
    state.pickup = true
    state.delivery = false
}

const isSuperAdmin = computed(() => {
    const isSuperAdminPerm = usePage().props.auth.user.allPermissions.includes("s_admin");
    if (isSuperAdminPerm) {
        return true;
    } else {
        return false;
    }
});

</script>

<template>

    <Head title="Schedule Appointments" />

    <PageHeader :title="'Schedule Appointments'" :items="'Schedule Appointments'" />
    <div class="row">
        <!-- Right Sidebar -->
        <div class="col-12">
            <div class="mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="btn-toolbar p-3">
                            <div class="d-flex flex-row justify-content-between col-12 mb-4">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="rounded-pill d-none d-lg-block"
                                        style="background-color: #f9f9f9; border: 0px">
                                        <div class="input-group d-flex flex-row align-items-center">
                                            <div class="mt-1">
                                                <span class="" style="font-size: medium"><i
                                                        class="bx bx-search-alt"></i></span>
                                            </div>
                                            <div class="">
                                                <input type="text" class="form-control" placeholder="Search.."
                                                    style="background-color: #f9f9f9; border: 0px"
                                                    v-model="state.searchText" />
                                            </div>

                                        </div>
                                    </div>
                                    <div>
                                        <b-button variant="primary" @click="state.requestMedicationModal = true">Schedule Appointment</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 ">

                            <div class="d-flex justify-content-center align-items-center">
                                <div class="d-flex justify-content-center align-items-center flex-column mt-5 mb-5">
                                    <div class="pb-5"><img :src="empty" height="100" /></div>
                                    <div> You don't have any scheduled appointments</div>
                                </div>

                            </div>
                        </div>
                        <b-modal v-model="state.requestMedicationModal" id="requestMedicationModal"
                            title-class="font-18" title="Confirm" centered header-class="d-flex justify-content-center"
                            hide-header hide-footer size="lg">
                            <div class="mb-5">
                                <div class="text-center fw-bold">Schedule Appointment</div>
                                <div class="text-center mb-3">You are about to schedule an appointment</div>
                            </div>
                            <div class="mb-3">
                                <simplebar style="max-height: 50vh">
                                    <div>

                                        <div class="col-6 mb-4" v-if="state.anotherPerson">
                                            <label>Select Patient</label>
                                            <v-select :options="state.users" :label="'optionString'"
                                                v-model="state.anotherSelectedPatient"
                                                :disabled="state.makeFormDisabled"></v-select>
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label>Select Hospital</label>
                                            <v-select :options="state.hospitals" :label="'optionString'"
                                                v-model="state.hospital" :disabled="state.makeFormDisabled"></v-select>
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label>Select Medication</label>
                                            <v-select :options="state.medications" :label="'optionString'"
                                                v-model="state.medication"
                                                :disabled="state.makeFormDisabled"></v-select>
                                        </div>

                                    </div>
                 
                                </simplebar>
                            </div>
                            <div class="border-top">
                                <div class="d-flex gap-3 justify-content-end mt-3">
                                    <div>
                                        <b-button variant="danger"
                                            @click="state.requestMedicationModal = false">Cancel</b-button>
                                    </div>
                                    <div>
                                        <b-button variant="primary" @click="">Submit</b-button>
                                    </div>
                                </div>

                            </div>
                        </b-modal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
