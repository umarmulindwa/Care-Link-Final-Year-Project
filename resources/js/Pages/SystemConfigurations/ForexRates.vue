<script setup>
import Swal from "sweetalert2";
import { reactive, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { showLoader } from "@/mixins/helpers";
import axios from "axios";
import moment from "moment";
import simplebar from "simplebar-vue";

const props = defineProps({
    rates: Array,
});
const state = reactive({
    addRatesModal: false,
    rateNumber: "",
    fromCur: "",
    tocur: "SSP",
    addForexRateLoader: false,
    currentDate: "",
    rateId: null,
    isRateEdit: false,
});

function formatDate(date) {
    return moment(date).format("dddd, MMMM Do YYYY, h:mm:ss a");
}

function addForexRate() {
    state.addForexRateLoader = true;
    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();
    formdata.append("id", state.rateId);
    formdata.append("rate", state.rateNumber);
    formdata.append("exchange", state.fromCur);

    axios
        .post("api/system-configuration/addDollarRate", formdata, options)
        .then((res) => {
            state.addForexRateLoader = false;
            state.addRatesModal = false;

            if (res.data.results === "success") {
                Swal.fire({
                    title: `${state.isRateEdit ? "Updated Successfully" : "Added Successfully."}`,
                    icon: "success",
                    html: `<p style="font-size: 14px">${state.isRateEdit ? "Your changes have been saved successfully" : "A new rate has been added successfully."}</p>`,
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
                        Swal.close();
                        router.reload();
                    }
                });
            }
        })
        .catch((err) => {
            console.log("Error:", err);
            state.addForexRateLoader = false;
            state.addRatesModal = false;

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
                    Swal.close();
                }
            });
        });
}

function editRate(rate) {
    state.isRateEdit = true;
    state.rateNumber = rate.rate;
    state.fromCur = rate.currency;
    state.rateId = rate.id;
    state.addRatesModal = true;
}

function deleteRate(rate) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to delete <strong>${rate.currency}</strong> from the forex rates list.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            const options = {
                "content-type": "multipart/form-data",
                headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
            };

            let formdata = new FormData();
            formdata.append("id", rate.id);

            axios
                .post("api/system-configuration/deleteRate", formdata, options)
                .then((res) => {
                    if (res.data.results === "success") {
                        Swal.fire({
                            title: `Deleted Successfully.`,
                            icon: "success",
                            html: `<p style="font-size: 14px">The rate has been deleted successfully</p>`,
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
                                Swal.close();
                                router.reload();
                            }
                        });
                    }
                })
                .catch((err) => {
                    console.log("Error:", err);
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
                            Swal.close();
                        }
                    });
                });
        }
    });
}
function forceImportRates() {
       Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
    router.visit("/getLatestForexRates", {
        method: "get",
        replace: false,
        preserveState: false,
        preserveScroll: false,
        headers: {},
        errorBag: null,
        forceFormData: false,
        onProgress: (progress) => {},
        onSuccess: (page) => {
            Swal.fire({
                title: `Successfully Updated.`,
                icon: "success",
                html: `<p style="font-size: 14px">The Latest Forex rates have been fetched</p>`,
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
                    Swal.close();
                    router.reload();
                }
            });
        },
        onError: (errors) => {
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
                            Swal.close();
                        }
                    });
        },
        onFinish: (visit) => {},
    });
}
</script>
<template>
    <div class="p-4">
        <div class="d-flex flex-row justify-content-between col-12 mb-4">
            <div class="d-flex align-items-center invisible">
                <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                    <div class="input-group d-flex flex-row align-items-center">
                        <div class="mt-1">
                            <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                        </div>

                        <div class="hstack gap-3" role="button">
                            <div class="vr" style="color: #dbdbdb"></div>
                            <div class="btn-group dropbottomstart">
                                <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                    <div>
                                        <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-row gap-3">
                <div @click="forceImportRates">
                    <b-button variant="secondary">Force Import Rates</b-button>
                </div>
                <div>
                    <b-button variant="primary" @click="state.addRatesModal = true">Add Rate</b-button>
                </div>
            </div>
        </div>

        <div v-if="props.rates.length > 0">
            <simplebar style="max-height: 80vh">
                <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Currency</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Rate</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Source</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Last Updated</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(rate, index) in props.rates" :key="`${index}_${rate.id}`">
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ rate.currency }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ rate.rate }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ rate.source }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ formatDate(rate.updated_at) }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div class="d-flex justify-content-end">
                                    <div class="list-unstyled hstack gap-1 mb-0">
                                        <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                            <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                        </div>
                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editRate(rate)">
                                            <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                        </div>
                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteRate(rate)">
                                            <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </simplebar>
        </div>
        <div v-else class="text-center pt-5 mt-5 mb-5">No Forex Rates</div>
    </div>
    <b-modal class="" v-model="state.addRatesModal" size="md" title="Add Rate" title-class="font-18" centered hide-footer hide-title>
        <div class="d-flex flex-row gap-3 align-items-center mb-4 mt-4">
            <div>
                <label>From:</label>
                <input class="form-control" type="text" v-model="state.fromCur" />
            </div>
            <div>
                <label class="invisible">To:</label>
                <div>-</div>
            </div>
            <div>
                <label>To:</label>
                <input class="form-control" type="tex" v-model="state.tocur" :disabled="true" />
            </div>
        </div>
        <div class="mb-4">
            <label>Rate:</label>
            <input class="form-control" type="number" v-model="state.rateNumber" />
        </div>

        <!-- <div class="mb-5 mt-3">
            <label>Date:</label>
            <input class="form-control" type="date" v-model="state.currentDate" />
        </div> -->

        <div class="text-center mb-4">
            <b-button variant="primary" @click="addForexRate" :disabled="state.addForexRateLoader"
                ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addForexRateLoader"></i>{{ state.isRateEdit ? "Save Changes" : "Add Rate" }}</b-button
            >
        </div>
    </b-modal>
</template>
