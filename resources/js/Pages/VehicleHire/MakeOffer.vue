<template>
    <Head title="Vehicle Hire Requests"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Submit Your Offer</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <h5><b>Driver Details</b></h5>
                        <div class="row">
                            <div class="col-12">
                                <label for="">Proposed Driver's Name</label>
                                <input v-model="form.driver_name" type="text" class="form-control" placeholder="Name of the driver" />
                                <p class="text-danger" v-if="form_error && !form.driver_name">Driver's name is required</p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-6">
                                <label for="">Phone Number</label>
                                <input v-model="form.driver_phone" type="text" class="form-control" placeholder="+9999999999" @input="updatePhoneNumber('dn')" ref="dn"/>
                                <p class="text-danger" v-if="form_error && !form.driver_phone">Driver's phone number is required</p>
                            </div>
                            <div class="col-6">
                                <label for="">Alternative Phone No. (Optional)</label>
                                <input v-model="form.driver_phone_alt" type="text" class="form-control" placeholder="+9999999999" @input="updatePhoneNumber('adn')" ref="adn"/>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <label for="">Driver's Email Address</label>
                                <input v-model="form.driver_email" type="text" class="form-control" placeholder="driver@domain.com"/>
                                <p class="text-danger" v-if="form_error && !form.driver_email">Driver's email address is required</p>
                            </div>
                        </div>

                        <div class="row mt-4 mb-4">
                            <div class="col-12">
                                <label for="">Drivers Picture</label>
                                <p class="text-danger" v-if="form_error && !form.driver_picture">Driver's picture is required</p>
                                <single-file-upload @files="handleFileUpload"/>
                            </div>
                        </div>

                        <h4><b>Quotation</b></h4>
                        <div class="row">
                            <label for="">Total Amount you intend to charge for this trip</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select v-model="form.quotation_currency" class="form-control" name="currency">
                                        <option value="SSP">SSP</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                                <input v-model="form.quotation_amount" type="number" name="cost" class="form-control" placeholder="1000"/>
                            </div>
                            <p class="text-danger" v-if="form_error && !form.quotation_amount">Quotation amount is required</p>
                        </div>

                        <template v-if="sp_submitted_quotation && sp_submitted_quotation.length === 0">
                            <div class="row mt-4">
                                <div class="col-6 offset-3">
                                    <button class="btn btn-primary w-100" @click.prevent="submitForm" :disabled="processing">
                                        <template v-if="processing">
                                            SUBMITTING... <span class="spinner spinner-border spinner-border-sm"></span>
                                        </template>
                                        <template v-else>
                                            SUBMIT FOR CONSIDERATION
                                        </template>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <p class="text-center">You submitted an offer which is currently being reviewed. Until the review process is complete, you can not make any changes</p>
                                    <p class="text-center">If, however, you wish to recall your offer, <a href="">click here</a></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h5><b>Vehicle Details</b></h5>
                        <template v-if="info && !fetching">
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div :style="backgroundStyle"></div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-6">
                                            <b>License Plate</b><br>
                                            {{ info.sp_vehicle.license_plate }}
                                        </div>
                                        <div class="col-6">
                                            <b>Model</b><br>
                                            {{ info.sp_vehicle.name }}
                                        </div>
                                    </div>

                                    <br>

                                    <b>Inspection Status</b><br>
                                    <span class="badge badge-soft-success">{{ info.sp_vehicle.inspection_status }}</span>

                                    <br><br>
                                    <b>Next Inspection</b><br> {{ formatDate(info.sp_vehicle.next_inspection_date,'DD MMM YYYY, HH:MM') }}

                                </div>
                            </div>
                        </template>
                        <template v-else-if="fetching">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <span class="spinner spinner-border"></span>
                                    <br>Please wait
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="row">
                                <div class="col-12 text-center">
                                    Unable to fetch request details at the moment
                                </div>
                            </div>
                        </template>
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
import singleFileUpload from "@/Pages/LowValueRequest/SingleFileUpload.vue";

import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

import "intl-tel-input/build/css/intlTelInput.css";
import intlTelInput from "intl-tel-input";

export default {
    name: "MakeOffer",
    mixins:[RequestHelper,notify],
    components:{
        Layout,
        PageHeader,
        Head,
        multipleUpload,
        singleFileUpload,
    },

    props: ['trip_id','vhr_id','vehicle'],

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

            info: null,

            form: {
                trip_id: this.$props.trip_id,
                vhr_id: this.$props.vhr_id,
                sp_vehicle: null,
                driver_name:null,
                driver_phone: null,
                driver_phone_alt: null,
                driver_email: null,
                driver_picture: null,
                quotation_currency: 'USD',
                quotation_amount: null,
            },

            form_error: false,

            intlTel: null,

            session: null,

            processing: false,
            fetching: true,

            travel_planner_url: import.meta.env.VITE_ADMIN_TRAVEL_PLANNER_URL,
        };
    },
    mounted() {

        useStore().commit("LoggedInUser/storeCurrentUrl",'/vehicle-inspection');
        this.initPhoneInput();

        this.session = this.$attrs.auth.user;

        this.getRequestDetails();

    },

    methods: {

        getRequestDetails(){
            axios.get(`${this.travel_planner_url}/api/sp/vehicle-hire/make-offer`, {
                params: { sp: this.session.id, trip_id:this.trip_id, vhr_id: this.vhr_id,vehicle:this.vehicle},
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                this.info = response.data.info;

                if(response.data.info)
                    this.form.sp_vehicle = response.data.info.sp_vehicle;

                this.fetching = false;
            }).catch((error) => {
                console.error("Error fetching requests:", error);
                this.isfetching = false;
            });
        },


        handleFileUpload(files){
            this.form.driver_picture = files;
        },

        initPhoneInput() {
            // Access the native input element using $refs
            const inputElement1 = this.$refs.dn;
            const inputElement2 = this.$refs.adn;

            // Initialize the intlTelInput plugin with initialCountry set to 'MW' for Malawi
            this.intlTel = {
                'dn':intlTelInput(inputElement1, {
                        initialCountry: 'MW',
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js", // Optional utility script
                    }),
                'adn':intlTelInput(inputElement2, {
                        initialCountry: 'MW',
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.3/js/utils.js", // Optional utility script
                    })
            };

        },

        updatePhoneNumber(name) {
            if(name === 'dn'){
                this.$refs.dn.value = this.intlTel.dn.getNumber();
                this.form.driver_phone = this.intlTel.dn.getNumber();
            }
            else if(name === 'adn'){
                this.$refs.adn.value = this.intlTel.adn.getNumber();
                this.form.driver_phone_alt = this.intlTel.adn.getNumber();
            }
        },

        submitForm(){
            if(this.hasEmptyValue(this.form,'driver_phone_alt')){
                this.form_error = true;
                return notify.toastErrorMessage("Please fill required fields!")
            }

            this.showWarningMessage("You are about to submit your quotation, sure to proceed?").then((result) => {
                if(result.isConfirmed){
                    this.processing = true;

                    let formData = new FormData();
                    formData.append('form',JSON.stringify(this.form));
                    formData.append('sp',this.session.id)
                    formData.append('driver_picture',this.form.driver_picture);

                    axios.post(`${this.travel_planner_url}/api/sp/vehicle-hire/add-quotation`,formData,{
                        headers:{
                            "Accept": "application/json",
                            'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                            "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                        }
                    }).then((response) => {
                        if (response.status === 200) {
                            this.processing = false;
                            this.showSuccessMessage("Your quotation has been submitted for consideration successfully").then(() => {
                                this.goTo('vehicle-hire.index');
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

        formatDate(date,format){
            return moment(date).format(format);
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

                if(except !== key){
                    if (value === '' || value === null) {
                        return true;
                    }
                }
            }

            return false; // Return false if all keys have values
        },
    },
    computed: {
        backgroundStyle() {
            if(this.info){
                return {
                    height: '155px',
                    overflow: 'hidden',
                    backgroundImage: `url('${this.travel_planner_url}/storage/${JSON.parse(this.info.sp_vehicle.image)[0]}')`,
                    backgroundPosition: 'center',
                    backgroundSize: 'cover',
                    backgroundColor: 'grey',
                };
            }

        },
        sp_submitted_quotation(){
            if(this.info && this.info.trip.offers && this.session){
                return this.info.trip.offers.filter((offer) => offer.service_provier.email === this.session.email)
            }else{
                return null;
            }
        }
    },
};
</script>

<style scoped>
td{
    background:#FFFFFF !important;
}
</style>
