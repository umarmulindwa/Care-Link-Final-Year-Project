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
    hospitalName: "",
    location: "",
    address: "",
    contact: "",
    email: "",
    hospitalStock: [{ medicine: "", stock: 0 }]
});

//methods
//mounted
onMounted(() => {
    //changing filter by value

});

function addStock() {
    state.hospitalStock.push({ medicine: "", stock: 0 });
}

function removeStock(index) {
    state.hospitalStock.splice(index, 1);
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

    <Head title="Hospital Referrals" />

    <PageHeader :title="'Hospital Referrals'" :items="'Hospital Referrals'" />
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
                                        <b-button variant="primary"
                                            @click="state.requestMedicationModal = true">Register Hospital</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 ">

                            <div class="d-flex justify-content-center align-items-center">
                                <div class="d-flex justify-content-center align-items-center flex-column mt-5 mb-5">
                                    <div class="pb-5"><img :src="empty" height="100" /></div>
                                    <div> You don't have any requests</div>
                                </div>

                            </div>
                        </div>
                        <b-modal v-model="state.requestMedicationModal" id="requestMedicationModal"
                            title-class="font-18" title="Confirm" centered header-class="d-flex justify-content-center"
                            hide-header hide-footer size="lg">
                            <div class="mb-5">
                                <div class="text-center fw-bold">Register Hospital</div>
                                <div class="text-center mb-3">You are about to register a new hospital</div>
                            </div>
                            <div class="mb-3">
                                <simplebar style="max-height: 50vh">
                                    <div>

                                        <div class="col-6 mb-3">
                                            <label>Hospital Name</label>
                                            <input v-model="state.hospitalName" type="text" class="form-control"
                                                aria-label="Hospital Names" />
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label>Location</label>
                                            <input v-model="state.location" type="text" class="form-control"
                                                aria-label="Location" />
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label>Address</label>
                                            <input v-model="state.address" type="text" class="form-control"
                                                aria-label="Address" />
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label>Phone Number</label>
                                            <input v-model="state.contact" type="text" class="form-control"
                                                aria-label="Phone Number" />
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label>Email</label>
                                            <input v-model="state.email" type="email" class="form-control"
                                                aria-label="Email" />
                                        </div>

                                        <div class="inner-repeater mb-4">
                                            <div class="inner mb-3">
                                                <label>Hospital Stock</label>
                                                <div class="d-flex flex-row justify-content-between">
                                                     <div class=" col-6 mb-2">Medicine</div>
                                                     <div class="col-3">Stock</div>
                                                     <div class="col-2 invisible">btn</div>
                                                </div>
                                                <div v-for="(data, index) in state.hospitalStock" :key="data.id"
                                                    class="inner mb-3 row">
                                                    <div class="d-flex flex-row justify-content-between">
                                                        <div class=" col-6">
                                                            <input v-model="data.medicine" type="text"
                                                                class="inner form-control" />
                                                        </div>
                                                        <div class="col-3">
                                                            <input v-model="data.stock" type="number"
                                                                class="inner form-control" />
                                                        </div>
                                                        <div class="col-2">
                                                            <div class="d-grid">
                                                                <input type="button"
                                                                    class="btn btn-info btn-block inner"
                                                                    value="Delete" @click="removeStock(index)" />
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <input type="button" class="btn btn-success inner" value="Add Stock"
                                                @click="addStock" />
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
