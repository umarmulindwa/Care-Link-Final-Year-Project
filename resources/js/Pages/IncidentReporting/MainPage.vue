<script setup>
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { onMounted, computed, reactive } from "vue";
import moment from "moment";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import icondata from "../../../images/icondata.png";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import Swal from "sweetalert2";
import axios from "axios";
//props
const props = defineProps({
    incidents: Array,
});
//state
const state = reactive({});
//store
const store = useStore();

//mounted
onMounted(() => {
    //ading user details to the store
    store.commit("LoggedInUser/storeCurrentUrl", usePage().url);
});

//methods

//methods

/**
 * Delete the row
 */

function createNewIncident() {
    router.get(route("CreateIncident"));
}

function formatDate(date) {
    return moment(date).format("MMMM Do YYYY [at] h:mm a");
}

function viewIncidentDetail(id) {
    router.get("/review_incident", { id }, {});
}
function deleteReport(id) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p style="font-size: 14px">You are about to delete this incident report</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            let formData = new FormData();

            formData.append("incidentId", id);

            axios
                .post(`${incidentUrl}/api/incidents/delete_request`, formData, options)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Report Deleted Successfully",
                        html: "<p style='font-size: 14px'>Report has been deleted successfully</p>",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        showCloseButton: true,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#32CD32",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            state.isProcessing = false;
                            router.visit("/myIncidents");
                            Swal.close();
                        }
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    state.isProcessing = false;
                    Swal.fire({
                        title: "Something Went Wrong",
                        icon: "error",
                        html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
                        showCloseButton: false,
                        showCancelButton: false,
                        focusConfirm: true,
                        confirmButtonText: "OK",
                        confirmButtonColor: "#43ad60",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        closeOnClickOutside: false,
                    }).then((result) => {
                        if (result.value) {
                            state.isProcessing = false;
                            Swal.close();
                        }
                    });
                });
        }
    });
}
</script>

<template>
    <Head title="Incident Reporting" />

    <!-- <h3>Dashboard</h3> -->

    <!-- <form @submit.prevent="logout">
        <b-button type="submit" variant="primary" class="btn-block" >Log Out</b-button>
    </form> -->
    <Layout>
        <PageHeader :title="'Incident Reporting'" :items="items" />
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex flex-row justify-content-between mb-5">
                        <div class="d-flex align-items-center">
                            <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                <div class="input-group d-flex flex-row align-items-center">
                                    <div class="mt-1">
                                        <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                    </div>
                                    <div class=""><input type="text" class="form-control" placeholder="Search.." style="background-color: #f9f9f9; border: 0px" /></div>

                                    <div class="hstack gap-3" role="button">
                                        <div class="vr" style="color: #dbdbdb"></div>
                                        <div class="">All</div>
                                        <div><i class="mdi mdi-chevron-down"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row gap-3">
                            <div>
                                <Link :href="route('createIncident')">
                                    <b-button variant="primary">
                                        <i class="bx bx bx-plus font-size-16 align-middle me-2"></i>
                                        Report New Incident
                                    </b-button>
                                </Link>
                            </div>
                            <div>
                                <b-button variant="primary" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded font-size-16 align-middle" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                    <ul class="dropdown-menu">
                                        <b-dropdown-item class="mb-1">Filter</b-dropdown-item>
                                        <b-dropdown-item class="mb-1">Export</b-dropdown-item>
                                    </ul>
                                </b-button>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div v-if="props.incidents.length > 0" class="">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead class="">
                                        <tr>
                                            <th :style="{ backgroundColor: '#eff2f7' }">Incident #</th>
                                            <!-- <th :style="{ backgroundColor: '#eff2f7' }" class="text-center">Incident</th> -->
                                            <th :style="{ backgroundColor: '#eff2f7' }" class="text-center">Date Reported</th>

                                            <th :style="{ backgroundColor: '#eff2f7' }" class="text-center">Status</th>
                                            <th :style="{ backgroundColor: '#eff2f7' }" class="text-center">Currently with</th>
                                            <th :style="{ backgroundColor: '#eff2f7' }" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="incident in props.incidents" :key="`${incident.id}_${incident.reference_no}`" style="vertical-align: middle">
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="fw-bold" style="color: #3490dc" role="button" @click="viewIncidentDetail(incident.id)">{{ incident.reference_no }}</div>
                                                <div><span class="fw-bold">Originator:</span> {{ incident.reported_by_name }}</div>
                                                <div class="">
                                                    <span class="fw-bold">Email :</span> <span class="">{{ incident.reported_by_email }}</span>
                                                </div>
                                            </td>
                                            <!-- <td :style="{ backgroundColor: '#fff' }">
                                                {{ incident.reported_by_name }}
                                            </td> -->
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="text-center">{{ formatDate(incident.created_at) }}</div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="text-center">
                                                    <span class="badge text-bg-warning" v-if="incident.status == 'pending'">Pending Approval</span>
                                                    <span class="badge text-bg-danger" v-if="incident.status == 'closed'">Case Closed</span>

                                                    <span class="badge text-bg-success" v-if="incident.status == 'approved'">Approved</span>
                                                    <span class="badge text-bg-danger" v-if="incident.status == 'deleted'">Deleted</span>
                                                </div>
                                            </td>
                                            <td :style="{ backgroundColor: '#fff' }">
                                                <div class="text-center">
                                                    {{ incident.currentlyWith_name }}
                                                </div>
                                            </td>
                                            <td class="" :style="{ backgroundColor: '#fff' }">
                                                <div class="d-flex justify-content-end">
                                                    <div class="list-unstyled hstack gap-1 mb-0">
                                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View">
                                                            <div class="btn btn-sm btn-soft-primary" @click="viewIncidentDetail(incident.id)"><i class="mdi mdi-eye-outline"></i></div>
                                                        </div>
                                                        <!-- <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit">
                                                            <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                                        </div> -->
                                                        <div
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            aria-label="Delete"
                                                            @click="deleteReport(incident.id)"
                                                            v-if="incident.status == 'pending'"
                                                        >
                                                            <b-button variant="soft-danger" class="btn-sm"><i class="mdi mdi-delete-outline"></i></b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else class="d-flex flex-column align-items-center" style="padding-top: 15vh; padding-bottom: 30vh">
                            <div class="mb-4 pt-5"><img :src="icondata" :height="80" /></div>
                            <div>You have no reported Incidents.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
<style scoped lang="scss">
.dropzone {
    // width: 400px;
    height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 16px;
    border: 2px dashed #3b86fe6f;
    background-color: #fff;
    transition: 0.3s ease all;
    label {
        padding: 8px 12px;
        border-radius: 4px;
        color: #fff;
        background-color: #3b85fe;
        transition: 0.3s ease all;
    }
    input {
        display: none;
    }
}
.active-dropzone {
    color: #fff;
    border-color: #fff;
    background-color: #3b85fe;
    label {
        background-color: #fff;
        border-radius: 4px;
        color: #3b85fe;
    }
}
</style>
