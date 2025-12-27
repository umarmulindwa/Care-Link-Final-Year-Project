<script setup>
import { reactive } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import axios from "axios";
import { onMounted } from "vue";

const state = reactive({
    dependantModal: false,
    addDependantLoader: false,
    isEditDependant: false,
    dependantId: null,
    name: "",
    gender: "",
    date: "",
    userDetails: {},

    passport_number:null,
    pp_expiry_date: null,
    pp_file: null,
    validationError:false,
});

const props = defineProps({
    user: Object,
});

onMounted(() => {
    state.userDetails = usePage().props.auth.user;
});

function addDependant() {
    if(state.name.length === 0 || state.gender.length === 0 || state.date.length === 0){
        state.validationError = true;
        return;
    }

    state.addDependantLoader = true;

    const data = {
        id: state.dependantId,
        name: state.name,
        gender: state.gender,
        date: state.date,
        passport_number:state.passport_number,
        pp_expiry_date:state.pp_expiry_date,
        pp_file: state.pp_file,
    };

    router.post("/addDependant", data, {
        onSuccess: (page) => {
            state.addDependantLoader = false;
            state.dependantModal = false;

            Swal.fire({
                title: `${state.isEditDependant ? "Updated Successfully" : "Added Successfully."}`,
                icon: "success",
                html: `<p style="font-size: 14px">${state.isEditDependant ? "Your changes have been saved successfully" : "A new dependant has been added successfully."}</p>`,
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
        onError: (visit) => {
            state.addDependantLoader = false;
            state.dependantModal = false;

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
    });
}

function editDependant(dependant) {
    state.dependantId = dependant.id;
    state.name = dependant.name;
    state.gender = dependant.sex;
    state.date = dependant.date_of_birth;
    state.passport_number = dependant.passport_number;
    state.pp_expiry_date = dependant.pp_expiry_date;

    state.isEditDependant = true;
    state.dependantModal = true;
}

function deleteDependant(dependant) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to remove <strong>${dependant.name}</strong> from your dependants.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            router.post(
                "/removeDependant",
                { id: dependant.id },
                {
                    onSuccess: (page) => {
                        Swal.fire({
                            title: "Removed Successfully.",
                            icon: "success",
                            html: `<p style="font-size: 14px">${dependant.name} has been removed from your dependants list.</p>`,
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
                    onError: (visit) => {
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
                }
            );
        }
    });
}
</script>

<template>
    <div class="mt-5">
        <div>
            <div class="d-flex flex-row justify-content-between col-12 mb-4 mt-5">
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
                    <div>
                        <b-button variant="primary" @click="state.dependantModal = true">Add Dependant</b-button>
                    </div>
                </div>
            </div>
            <div v-if="usePage().props.auth.user.staff_profile.dependants.length > 0">
                <table class="table align-middle table-nowrap table-striped dt-responsive nowrap w-100" id="userList-table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Name</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Gender</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7' }" class="col-2">Date of Birth</th>
                            <th scope="col" :style="{ backgroundColor: '#eff2f7', textAlign: 'end' }"><div class="me-4">Action</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(dependant, index) in usePage().props.auth.user.staff_profile.dependants" :key="`${index}_${dependant.id}`">
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ dependant.name }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ dependant.sex }}</div>
                                </div>
                            </td>
                            <td :style="{ backgroundColor: '#fff' }">
                                <div role="button">
                                    <div class="col-12 text-truncate">{{ dependant.date_of_birth }}</div>
                                </div>
                            </td>

                            <td :style="{ backgroundColor: '#fff' }">
                                <div class="d-flex justify-content-end">
                                    <div class="list-unstyled hstack gap-1 mb-0">
                                        <div class="invisible" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" @click="">
                                            <div class="btn btn-sm btn-soft-primary"><i class="mdi mdi-eye-outline"></i></div>
                                        </div>
                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Edit" @click="editDependant(dependant)">
                                            <div href="#" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></div>
                                        </div>
                                        <div data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Delete" @click="deleteDependant(dependant)">
                                            <b-button variant="soft-danger" class="btn-sm" @click=""><i class="mdi mdi-delete-outline"></i></b-button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="text-center pt-5 mt-5 mb-5">You have no dependants</div>
        </div>
        <b-modal class="" v-model="state.dependantModal" scrollable size="md" title="Add Dependant (must be in country)" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-4 mt-3">
                <label>Name:</label>
                <input class="form-control" type="text" v-model="state.name" />
                <small class="text-danger" v-if="state.validationError && !state.name">Name is required</small>
            </div>

            <div class="mb-4">
                <label for="GENDER">Gender</label>

                <select class="form-select form-control" id="" v-model="state.gender">

                     <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <small class="text-danger" v-if="state.validationError && !state.gender">Gender is required</small>
            </div>
            <div class="mb-4">
                <label>Date of Birth:</label>
                <input class="form-control" type="date" v-model="state.date" />
                <small class="text-danger" v-if="state.validationError && !state.date">Date of birth is required</small>
            </div>

            <div class="row mb-5">
                    <div class="col-6">
                        <label>Passport Number:</label>
                        <input class="form-control" type="text" v-model="state.passport_number" />
                    </div>
                    <div class="col-6">
                        <label>Passport Expiry:</label>
                        <input class="form-control" type="date" v-model="state.pp_expiry_date" />
                    </div>
                    <div class="col-12 mt-4">
                        <label>Upload Passport:</label>
                        <input class="form-control" type="file"  @change="(e) => state.pp_file = e.target.files[0]" />
                    </div>
                </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="addDependant" :disabled="state.addDependantLoader"
                    ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.addDependantLoader"></i>{{ state.isEditDependant ? "Save Changes" : "Add Dependant" }}</b-button
                >
            </div>
        </b-modal>
    </div>
</template>
