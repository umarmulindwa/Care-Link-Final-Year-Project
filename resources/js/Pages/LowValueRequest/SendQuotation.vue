<template>
    <Head title="Requests for Quotations"></Head>
    <Layout>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Requests for Quotations</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <template v-if="request">
                            <div class="col-md-12 mb-4">
                                <h6>UNICEF Section</h6>
                                <p>{{ request.section_name }}</p>
                            </div>

                            <div class="col-md-12 mb-4 mt-4">
                                <h6>Description</h6>
                                <div v-html="request.requesters_message"></div>
                            </div>

                            <div class="col-md-12 mb-4 mt-4" v-if="supply_items">
                                <div class="list-group">
                                    <div class="list-group-item list-group-item-action active">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">Item Description</h5>
                                            <small>Unit price</small>
                                        </div>
                                    </div>
                                    <div v-for="(item, index) in supply_items" :key="index" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ item.item_description }}</h5>
                                            <small>{{ `${Number(item.amount).toLocaleString() } ${item.currency}` }}</small>
                                        </div>
                                        <p class="mb-1">Material No.: {{ item.material_number }}</p>
                                        <small>Quantity: {{ Number(item.quantity).toLocaleString() }}</small> <br>
                                        <small>UOM: {{ item.unit_of_measure }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mb-2 mt-4">
                                <hr style="border:1px solid #2C2C2C;">
                                <button @click.prevent="upload_quotation_modal = true" type="button" class="btn btn-primary w-50">
                                    <span><i v-if="isProcessing" class="fa fa-spinner fa-spin"> </i>
                                        {{ `${previousQuotation ? 'EDIT PREVIOUS QUOTATION' : 'SUBMIT QUOTATION' }`}}
                                    </span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card time-line-screen">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="fw-500 fs-12">
                                <span class="text-start">
                                  File Attachments
                                </span>
                            </div>
                        </div>

                        <div v-if="request" id="file-attachments">
                            <div class="row mt-2" v-for="file in request.supply_files">
                                <div class="col-2 mt-3">
                                    <img src="/images/icons/icon.pdf.png" style="width:30px;" alt="" />
                                </div>
                                <div class="col-10">
                                    <a :href="`${pdfUrl(file.file_name)}`" class="text mb-0 mt-0" target="_blank">{{ file.file_name }}</a>
                                    <p class="text-muted text-success font-size-10">
                                        {{ formatDate(file.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center">
                            <div><i class="fa fa-spinner fa-spin"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>


    <b-modal v-model="upload_quotation_modal" title="Submit Quotation" size="lg" scrollable centered hide-footer>

        <form action="" id="quotation-form" @submit.prevent="submitQuotation(supply_id)">
            <div class="row">

                <div class="col-md-4 d-flex">
<!--                    <label>Currency</label>-->
<!--                    <select v-model="currency" class="form-control form-control-sm ms-2" >-->
<!--                        <option value="USD">USD</option>-->
<!--                        <option value="EUR">EUR</option>-->
<!--                        <option value="SSD">SSD</option>-->
<!--                    </select>-->
                </div>
                <div class="col-md-8 text-end">
                    <h4>{{ `${Number(total_quotation_value).toLocaleString()} ${currency}` }}</h4>
                </div>
            </div>

            <div class="row mt-4"></div>

            <div class="col-md-12 mb-4 ">
                <div class="list-group" v-if="supply_items">
                    <div class="list-group-item list-group-item-action active">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-1">Item Description</h5>
                            </div>
                            <div class="col-md-5">
                                <small>Quotation (per unit)</small>
                            </div>
                        </div>
                    </div>
                    <div v-for="(item, index) in supply_items" :key="index" class="list-group-item list-group-item-action">
                        <div class="row" :style="{ textDecoration: item.removed ? 'line-through' : '' }">
                            <div class="col-md-6">
                                <span class="h5 font-size-14">{{ item.item_description }}</span><br>
                                <small>Material No.: {{ item.material_number }}</small> <br>
                                <small>Quantity: {{ Number(item.quantity).toLocaleString() }}</small> <br>
                                <small>UOM: {{ item.unit_of_measure }}</small>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="number" v-model="item.quotation" class="form-control form-control-sm" :readonly="isPreviewMode" :disabled="item.removed" required>
                                    <div class="input-group-append">
                                        <select v-model="currency" class="form-control" disabled>
                                            <option value="USD">USD</option>
                                            <option value="EUR">EUR</option>
                                            <option value="SSD">SSD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button @click.prevent="item.removed = !item.removed;" class="btn">
                                    <span class="bx font-size-24 text-danger" :class="item.removed ? 'bx-undo':'bx-x'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <label for="">Support Document (If any)</label>
                    <single-file-upload @files="uploadedFileHandler" ></single-file-upload>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="text-center mb-2 mt-4">

                        <div v-if="!isPreviewMode">
                            <button :disabled="isProcessing" type="submit" class="btn btn-primary w-50">
                                <span>
                                    <i v-if="isProcessing" class="fa fa-spinner fa-spin" id="proceed-to-sign">
                                    </i>Upload
                                </span>
                            </button>

                        </div>

                        <div v-else>

                            <button :disabled="isProcessing" @click.prevent="goBack()" type="button" class="btn btn-danger w-25 m-2">
                                <span>
                                    Back
                                </span>
                            </button>
                            <button :disabled="isProcessing" type="submit" class="btn btn-primary w-25">
                                <span>
                                    <i v-if="isProcessing" class="fa fa-spinner fa-spin" id="proceed-to-sign">
                                    </i>Confirm
                                </span>
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
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

import SingleFileUpload from "@/Pages/LowValueRequest/SingleFileUpload.vue";


export default {
    name: "SendQuotations",
    props: ['supply_id'],
    mixins:[RequestHelper,notify],
    components:{
        Layout,
        PageHeader,
        Head,
        SingleFileUpload,
    },

    data() {
        return {
            session: null,
            request: null,
            previousQuotation: null,

            currency: 'USD',
            quotation_value: null,
            upload_quotation_modal:false,
            supply_items: null,

            step: 1,

            isProcessing: true,

            isPreviewMode: false,

            uploadedDocument: null,

            //url
            supplyUrl: import.meta.env.VITE_LOW_VALUE_PROCUREMENT_URL,

        };
    },
    async mounted() {
        // useStore().commit("LoggedInUser/storeCurrentUrl", "/low-value-procurement");
        this.session = this.$attrs.auth.user;
        await this.fetchRequest();

        this.setSupplyItems();
    },
    methods: {
        async fetchRequest() {
            await axios.get(`${this.supplyUrl}/api/sp-requests/${this.$props.supply_id}/`, {
                params: {sp: this.session.id},
                headers: {
                    "Accept": "application/json",
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                if (!response.data || response.data.length === 0) {
                    router.get(route('low-value-procurement.index'))
                }

                this.request = response.data.request;
                this.previousQuotation = response.data.quotation;

                if (this.previousQuotation) {
                    this.currency = JSON.parse(this.previousQuotation.quotation_value).currency;
                    this.total_quotation_value = JSON.parse(this.previousQuotation.quotation_value).value
                }

                //ensure currency is that used by initiator
                this.currency = this.request?.supply_items[0].currency

                this.isProcessing = false;

            })
                .catch((error) => {
                    console.error("Error fetching requests:", error);
                    this.isfetching = false;
                });
        },

        setSupplyItems(){
            if(this.previousQuotation){
                let singleItemQuotations = this.previousQuotation.supply_item_quotations;
                this.supply_items = this.request?.supply_items.map((item) => {
                    let quoted = singleItemQuotations.find((quotation) => quotation.supply_item_id === item.id);

                    if(quoted){
                        return {
                            ...item,
                            quotation: quoted.amount,
                            removed: false,
                        }
                    }else{
                        return {
                            ...item,
                            quotation: null,
                            removed: true,
                        }
                    }
                })
            }else{
                this.supply_items = this.request?.supply_items.map((item) => {
                    return {
                        ...item,
                        quotation: null,
                        removed: false,
                    }
                })
            }
        },

        formatDate(date) {
            return moment(date).format("DD/MMM/YYYY");
        },

        uploadedFileHandler(file){
            this.uploadedDocument = file;
        },

        submitQuotation(supply_id) {
            if(!this.supply_items?.find((item) => item.removed === false)){
                return this.showErrorMessage("You should submit a quotation of atleast one item. ");
            }

            let app = this;

            let form = document.getElementById("quotation-form");
            let formData = new FormData(form);

            formData.append('sp', app.session.id);
            formData.append('supply_id', supply_id);
            formData.append('uploaded_file', app.uploadedDocument);
            formData.append('quotation_currency',app.currency);
            formData.append('quotation_value',app.total_quotation_value);

            if (app.previousQuotation != null) {
                formData.append('quotation_id', app.previousQuotation.id);
                formData.append('quotation_document', app.previousQuotation.quotation_document);
            }

            //only do items that have not been removed by sp
            let selected_items = this.supply_items.map((item) => {
                if(!item.removed){
                    return item;
                }
            });

            formData.append('item_quotations',JSON.stringify(selected_items));

            if (!app.isPreviewMode) {
                app.isPreviewMode = true;
                app.step = 2;
                return;
            }

            app.isProcessing = true;


            axios.post(`${this.supplyUrl}/api/sp-requests`,formData,{
                headers:{
                    "Accept": "application/json",
                    'Content-Type': "multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2),
                    "Authorization": `Bearer ${this.$attrs.auth.user.api_token}`
                }
            }).then((response) => {
                if (response.data.success === 200) {
                    app.showSuccessMessage('<p style="font-size: 14px">Quotation Submitted.</p>')
                    router.get(route('low-value-procurement.index'))
                }
            }).catch((e) => {
                app.isProcessing = false;
                app.showErrorMessage("Something happened, please try again!")
            })
        },

        pdfUrl(file) {
            return this.supplyUrl + '/storage/supply-request-files/' + file;
        },


        goBack() {
            let app = this;
            if (app.step > 1) {
                app.step--;
                app.isPreviewMode = false;
            }
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

        showErrorMessage(message){
            return Swal.fire({
                title: 'Something happened',
                icon: 'error',
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
    },

    computed: {
        total_quotation_value(){
            if(!this.supply_items) return 0;
            return this.supply_items.reduce((total, item) => {
                if(!item.removed){
                    return total += parseInt((item.quotation || 0) * item.quantity );
                }else{
                    return total;
                }
            }, 0) || 0;
        }
    }

}

</script>
