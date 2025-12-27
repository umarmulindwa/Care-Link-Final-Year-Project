<template>
    <Head title="Vehicle Inspection"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Submit Vehicle For Inspection</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">
                                <label for="">License Plate</label>
                                <input v-model="form.license_plate" type="text" class="form-control" placeholder="Vehicle Licence Plate Number">
                                <p v-if="!form.license_plate && form_error" class="text-danger">License plate is required</p>
                            </div>
                            <div class="col-4">
                                <label for="">Vehicle Model</label>
                                <input v-model="form.model" type="text" class="form-control" placeholder="eg Toyota Hilux Double Cabin">
                                <p v-if="!form.model && form_error" class="text-danger">Vehicle model is required</p>

                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-8">
                                <label for="">Vehicle Description</label>
                                <ckeditor :editor="editor" v-model="form.description" :config="editorConfig"></ckeditor>
                                <p v-if="!form.description && form_error" class="text-danger">Vehicle description is required</p>

                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-3">
                                <label for="phone-input">
                                    Phone Number
                                    <span style="cursor:pointer;" title="UNICEF may need to reach out and suggest alternative inspection dates to avoid a protracted back-and-forth. This is therefore the number that UNICEF may use to quickly reach you.">
                                        <i class="fa fa-info-circle text-gray-600"></i>
                                    </span>
                                </label>
                                <input type="tel" id="phone-input" class="form-control" placeholder="Enter phone number" @input="updatePhoneNumber" ref="phoneInput">
                                <p v-if="!form.phone && form_error" class="text-danger">Phone number is required</p>

                            </div>
                            <div class="col-3">
                                <label for="">Tentative Inspection Date</label>
                                <input v-model="form.inspection_date" type="datetime-local" class="form-control">
                                <p v-if="!form.inspection_date && form_error" class="text-danger">Tentative inspection date is required</p>

                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-8">
                                <label for="">Vehicle Pictures</label>
                                <p v-if="!form.pictures && form_error" class="text-danger">Atleast one vehicle picture is required</p>

                                <multiple-upload @files="handleFileUpload"/>
                            </div>
                        </div>


                        <div class="row mt-5">
                            <div class="col-8 text-center">
                                <button class="btn btn-primary w-50" @click.prevent="submitForm" :disabled="processing">
                                    <template v-if="!processing">
                                        SUBMIT
                                    </template>
                                    <template v-else>
                                        SUBMITTING...<span v-if="!processing" class="spinner spinner-border spinner-border-sm"></span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>

</template>


<script>

import moment from "moment";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import PageHeader from "@/Components/page-header.vue";
import Layout from '@/Layouts/verticalmain.vue';
import { RequestHelper } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import Swal from "sweetalert2";
import {useStore} from "vuex";
import axios from "axios";

import multipleUpload from "@/Pages/VehicleInspection/MultipleUpload.vue";

import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import "intl-tel-input/build/css/intlTelInput.css";
import intlTelInput from "intl-tel-input";

export default {
    name: "List",
    mixins:[RequestHelper,notify],
    components:{
        Layout,
        PageHeader,
        Head,
        multipleUpload,
    },

    data() {
        return {

            editor: ClassicEditor,

            editorConfig: {
                toolbar: {
                    items: [
                        'bold',
                        'italic',
                        'link',
                        'undo',
                        'redo'
                    ]
                },
                height: '600',
            },

            form: {
                license_plate: null,
                model: null,
                description: null,
                phone: null,
                inspection_date: null,
                pictures: null,
            },

            form_error: false,

            intlTel: null,

            session: null,

            processing: false,

            travel_planner_url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,
        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/vehicle-inspection');
        this.initPhoneInput();

        this.session = this.$attrs.auth.user;

        // this.getRequests()

    },

    methods: {

        handleFileUpload(files){
            this.form.pictures = files;
        },

        initPhoneInput() {
            // Access the native input element using $refs
            const inputElement = this.$refs.phoneInput;

            // Initialize the intlTelInput plugin with initialCountry set to 'MW' for Malawi
            this.intlTel = intlTelInput(inputElement, {
                initialCountry: 'MW',
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js", // Optional utility script
            });
        },

        updatePhoneNumber() {
            // Update the phone number input value with the formatted international number
            this.$refs.phoneInput.value = this.intlTel.getNumber();
            this.form.phone = this.intlTel.getNumber();
        },

        submitForm(){
            if(this.hasEmptyValue(this.form)){
                this.form_error = true;
                return notify.toastErrorMessage("Please fill required fields!")
            }

            this.showWarningMessage("You are about to submit a vehicle for inspection, sure to proceed?").then((result) => {
                if(result.isConfirmed){
                    this.processing = true;

                    let formData = new FormData();
                    formData.append('form',JSON.stringify(this.form));
                    formData.append('sp',this.$attrs.auth.user.id)

                    this.form.pictures.forEach((picture) => {
                        formData.append('vehicle_pictures[]',picture);
                    })

                    axios.post(`${this.travel_planner_url}/api/sp/vehicle-inspection/add-vehicle`,formData,{
                        headers:{
                            "Accept": "application/json",
                            'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                            "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                        }
                    }).then((response) => {
                        if (response.status === 200) {
                            this.processing = false;
                            this.showSuccessMessage("Your vehicle has been submitted for inspection successfully").then(() => {
                                this.goTo('vehicle-inspection.index');
                            })
                        }
                    }).catch((error) => {
                        this.processing = false;
                    })
                }
            })
        },

        showSuccessMessage(message){
            return Swal.fire({
                title: 'Success',
                icon: 'success',
                html: message,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#43ad60',
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false
            })
        },


        showWarningMessage(message){
            return Swal.fire({
                title: 'Confirmation',
                html: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'OK,proceed',
                confirmButtonColor: "#f00",
                cancelButtonText: 'Cancel'
            });
        },

        goTo(routeName, id){
            if(id){
                router.get(route(routeName,{id:id}));
            }else{
                router.get(route(routeName));
            }
        },

        hasEmptyValue(obj,except) {
            for (const key in obj) {
                const value = obj[key];

                if (value === '' || value === null) {
                    return true;
                }
            }

            return false; // Return false if all keys have values
        },
    },
};
</script>

<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
