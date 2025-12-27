<script setup>
import Layout from "@/Layouts/verticalvendor.vue";

import PageHeader from "@/Components/page-header.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { GoogleMap, Marker, Polygon } from "vue3-google-map";
import { onMounted, computed, reactive, ref } from "vue";
import UserSignPdf from "./PdfForms/UserSignPdf.vue";
import moment from "moment";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";
import Swal from "sweetalert2";
import html2pdf from "html2pdf.js";
//import PdfTronViewer from "../../Components/PdfTronViewer.vue";
import VuePdfApp from "vue3-pdf-app";
import "vue3-pdf-app/dist/icons/main.css";
import "vue3-tel-input/dist/vue3-tel-input.css";
import { VueEditor } from "vue3-editor";
import { Money3Component } from "v-money3";
import initPhone from "../../Composables/PhoneUtil.js";
import "intl-tel-input/build/css/intlTelInput.css";
import { notify } from "@/mixins/notify";

defineOptions({ layout: Layout });

const props = defineProps({
    implementingPartners: Array,
    incidentLocations: Array,
    sections: Array,
    showTitle: {
        default: true,
    },
    incidentId: {
        default: null,
    },
    incidentDate: {
        default: "",
    },
    incidentDescription: {
        default: "",
    },
    incidentReportedByName: {
        default: "",
    },
    incidentReportedByEmail: {
        default: "",
    },
});
//store
const store = useStore();

const state = reactive({
    makeFormDisabled: false,
    config: {
        masked: false,
        prefix: "",
        suffix: "",
        thousands: ",",
        decimal: ".",
        precision: 2,
        disableNegative: false,
        disabled: false,
        min: null,
        max: null,
        allowBlank: false,
        minimumNumberOfCharacters: 0,
        shouldRound: true,
        focusOnRight: false,
    },
    //form
    incidentDate: "",

    newincidentLocation: null,
    addressComments: "",
    incidentLocationModal: false,
    incidentLocationLat: "4.851259724661479",
    incidentLocationLng: "31.60444601876833",
    incidenLocationCoords: "",
    incidentmapLoader: false,
    markCenter: { lat: 4.851259724661479, lng: 31.60444601876833 },
    phone: "",
    incidentDetails: "",
    supplies: [{ id: 1, currency: "SSD" }],
    implementingPartner: "",
    hasbeenReportedToPolice: true,
    hasbeenReportedToLocalLeader: true,
    incidentReportedByName: "",
    incidentReportedByEmail: "",
    incidentId: null,
    feedbacklocal: "",
    whynotReportedlocal: "",
    feedbackPolice: "",
    whynotReportedPolice: "",
    isTheft: false,
    isDamage: false,
    isLooted: false,
    contactPerson: "",
    iWillSign: false,
    signedPdf: null,
    isProcessing: false,
    systemSignedArrayBuffer: null,
    incidentSupplies: [],
    mitigationMeasures: [{ test: 1 }],
    mitigationFollowUps: [{ test: 1 }],
    reportedOnTime: true,
    reportDelayCause: "",
    maxDate: "",
    selectedSection: "",
    savedSignature: null,
});
//computed
const locationOptions = computed(() => {
    const locationitems = props.incidentLocations;
    const locationArray = locationitems.map((item) => {
        item.optionString = `${item.state}, ${item.county}, ${item.payam}, ${item.site_name}, ${item.cooperating_partner}`;
        return item;
    });
    return locationArray;
});

const locationCommentOptions = computed(() => {
    const locationitems = props.incidentLocations;
    const locationComments = locationitems.map((item) => item.comment);
    //removing null comments
    const cleanComments = locationComments.filter((item) => item !== null);
    //removing duplicate comments
    let myset = new Set();
    const noDuplicatesComments = cleanComments.filter((item) => {
        if (myset.has(item)) return false;
        myset.add(item);
        return true;
    });

    return noDuplicatesComments;
});

const formDetails = computed(() => {
    const details = {
        implementingPartner: state.implementingPartner,
        incidentDate: state.incidentDate,
        isTheft: state.isTheft,
        isDamage: state.isDamage,
        isLooted: state.isLooted,
        newincidentLocation: state.newincidentLocation,
        contactPerson: state.contactPerson,
        phone: state.phone,
        incidentDetails: state.incidentDetails,
        supplies: state.supplies,
        hasbeenReportedToPolice: state.hasbeenReportedToPolice,
        hasbeenReportedToLocalLeader: state.hasbeenReportedToLocalLeader,
        feedback: state.feedback,
        whynotReported: state.whynotReported,
        mitigationMeasures: state.mitigationMeasures,
        mitigationFollowUps: state.mitigationFollowUps,
        reportedOnTime: state.reportedOnTime,
        reportDelayCause: state.reportDelayCause,
        feedbacklocal: state.feedbacklocal,
        whynotReportedlocal: state.whynotReportedlocal,
        feedbackPolice: state.feedbackPolice,
        whynotReportedPolice: state.whynotReportedPolice,
        addressComments: state.addressComments,
        section: state.selectedSection == "" ? "" : state.selectedSection.name,
    };

    return details;
});

const implemetingPartnedsArray = computed(() => {
    const ips = props.implementingPartners;

    const ipArray = ips.map((item) => item.name);

    return ipArray;
});

//mounted
onMounted(() => {
    //click module dropdown
    // document.getElementById("clickOnInitialLoad").click();
    store.commit("LoggedInUser/storeCurrentUrl", usePage().url);
    //auto loading fields in edit mode
    state.incidentDate = props.incidentDate;
    state.incidentDetails = props.incidentDescription;
    state.incidentReportedByName = props.incidentReportedByName;
    state.incidentReportedByEmail = props.incidentReportedByEmail;
    state.incidentId = props.incidentId;
    state.maxDate = moment().format("YYYY-MM-DD");
    if (state.incidentId === null) {
        state.incidentReportedByName = usePage().props.auth.user.name;
        state.incidentReportedByEmail = usePage().props.auth.user.email;
    }
    initPhone("incidentPhoneNumber");

    getIncidentSupplies();

    getUserSignature();
});

function getIncidentSupplies() {
    const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };
    axios
        .get(`${incidentUrl}/api/incidents/getIncidentSupplies`, options)
        .then((res) => {
            state.incidentSupplies = res.data.results;
            Swal.close();
        })
        .catch(function (error) {
            console.log(error);
            Swal.close();
        });
}
async function getUserSignature() {
    const options = {
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };
    await axios
        .get(`/api/staff-register/get-userSignature`, options)
        .then((res) => {
            if (res.data.results != null) {
                state.savedSignature = res.data.results?.image;
            }
        })
        .catch((error) => {
            console.log(error);
        });
}
async function selectRoomLocation(coord, index) {
    const { latLng } = coord;
    state.incidentLocationLat = latLng.lat();
    state.incidentLocationLng = latLng.lng();
    state.incidenLocationCoords = `${state.incidentLocationLat} , ${state.incidentLocationLng}`;
    state.incidentmapLoader = true;
    //getting the name of the place
    const response =
        await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${state.incidentLocationLat},${state.incidentLocationLng}&result_type=locality&key=AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8
`);
    const place = await response.json();
    if (place.results.length > 0) {
        state.newincidentLocation = place.results[0].formatted_address;
        state.incidentmapLoader = false;
    } else {
        state.newincidentLocation = place.plus_code.compound_code;
        state.incidentmapLoader = false;
    }
}
function deletesupplyRecord(index) {
    state.supplies.splice(index, 1);
}
function deletemitigationFollowup(index) {
    state.mitigationFollowUps.splice(index, 1);
}
function deleteMitigationRecord(index) {
    state.mitigationMeasures.splice(index, 1);
}
function submitIncidentReport() {
    state.confirmSignedModal = false;
    const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    //implementing partner
    // const ipEmail = props.implementingPartners.find((item) => item.name === state.implementingPartner);

    let formData = new FormData();

    formData.append("implementing_partner_name", state.implementingPartner);
    formData.append("implementing_partner_email", "");
    formData.append("incidentDate", state.incidentDate);
    formData.append("incident_theft_case", state.isTheft);
    formData.append("incident_damage_case", state.isDamage);
    formData.append("incident_loot_case", state.isLooted);
    formData.append("incident_address", state.newincidentLocation != null ? state.newincidentLocation.optionString : "");
    formData.append("incident_address_coordinates", `${state.incidentLocationLat},${state.incidentLocationLng}`);
    formData.append("contact_person", state.contactPerson);
    formData.append("phone_number", state.phone);
    formData.append("supplies", JSON.stringify(state.supplies));
    formData.append("incident_details", state.incidentDetails);
    formData.append("reported_to_police", state.hasbeenReportedToPolice);
    formData.append("reported_to_local_leader", state.hasbeenReportedToLocalLeader);
    formData.append("why_not_reported", state.hasbeenReportedToPolice == true ? "" : state.whynotReportedPolice);
    formData.append("feedback", state.hasbeenReportedToPolice == true ? state.feedbackPolice : "");
    formData.append("signedFile", state.signedPdf);
    formData.append("incidentID", props.incidentId);
    formData.append("reportedByName", state.incidentReportedByName);
    formData.append("reportedByEmail", state.incidentReportedByEmail);
    formData.append("incident_address_lng", state.newincidentLocation != null ? state.newincidentLocation.longitude : "");
    formData.append("incident_address_lat", state.newincidentLocation != null ? state.newincidentLocation.latitude : "");
    formData.append("locationId", state.newincidentLocation != null ? state.newincidentLocation.id : "");
    formData.append("additionalComment", state.addressComments);
    formData.append("feedbackLocal", state.hasbeenReportedToLocalLeader == true ? state.feedbacklocal : "");
    formData.append("notReportedLocal", state.hasbeenReportedToLocalLeader == false ? state.whynotReportedlocal : "");
    formData.append("reportedOntime", state.reportedOnTime);
    formData.append("reportDelayCause", state.reportedOnTime == false ? state.reportDelayCause : "");
    formData.append("mitigation_measures", JSON.stringify(state.mitigationMeasures));
    formData.append("mitigation_followups", JSON.stringify(state.mitigationFollowUps));
    formData.append("sectionId", state.selectedSection == "" ? null : state.selectedSection.id);
    formData.append("sectionName", state.selectedSection == "" ? null : state.selectedSection.name);

    const config = {
        headers: {
            "content-type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post(`${incidentUrl}/api/incidents/submitReport`, formData, config)
        .then((res) => {
            if (res.status == 200) {
                state.addNewReportModal = false;
                state.showSubmissionLoader = false;
                Swal.fire({
                    title: "Incident Report Submitted",
                    icon: "success",
                    showCloseButton: false,
                    showCancelButton: false,
                    focusConfirm: true,
                    confirmButtonText: "OK",
                    confirmButtonColor: "#43ad60",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    closeOnClickOutside: false,
                }).then((result) => {
                    router.visit("/myIncidents");
                });
            }
        })
        .catch((error) => {
            app.isProcessing = false;
            console.log({ error });
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
function savePdftronForm(file) {
    state.signedPdf = file;
    state.confirmSignedModal = false;

    //call function to submit request
    submitIncidentReport();
}

function showDownloadOffline() {
    Swal.close();
    state.downloadOfflineModal = true;
}

function calculateTotal(index) {
    if (state.supplies[index] != null) {
        const item = state.supplies[index];
        const qty = item.qty != null ? item.qty : 0;
        const unit = item?.unitCost;
        state.supplies[index].value = qty * unit;
    }
}

function submitRequest() {
    Swal.fire({
        title: "Proceed with Approval",
        icon: "info",
        html: `<div style="font-size: 14px">
                                    You are about to proceed with approving this request.<br/>
                                    A PDF form evidencing this approval will be automatically signed by the system (on your behalf).
                                        </div>
                                    `,
        //input: "checkbox",
        //inputPlaceholder: "<div style='font-size: 14px'>I want to sign the PDF Form myself</div>",
        footer: "<div class='custom-footer' style='font-size: 14px; color:#99A5F6; cursor: pointer'>Alternatively, download and sign PDF offline</div>",
        coler: "#99A5F6",
        showCloseButton: false,
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#43ad60",
        confirmButtonText: "Proceed",
        allowOutsideClick: false,
        allowEscapeKey: false,
        closeOnClickOutside: false,
        didOpen: () => {
            const footer = document.querySelector(".custom-footer");
            footer.addEventListener("click", showDownloadOffline);
        },
        willClose: () => {
            const footer = document.querySelector(".custom-footer");
            footer.removeEventListener("click", showDownloadOffline);
        },
    }).then((result) => {
        if (result.isConfirmed && state.savedSignature != null) {
            state.iWillSign = result.value;
            state.isProcessing = true;
            //save the signing mode status
            store.commit("LoggedInUser/userSigns", result.value);
            //generate form 792927
            generateFile();
        } else {
            notify.toastErrorMessage("You have no registered signature");
        }
    });
}

//generating pdf for the form
function generateFile() {
    state.isProcessing = true;
    Swal.fire({
        title: "Generating Report ...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    let name = "IncidentForm.pdf";
    var element = document.getElementById("user-incidentform-pdf");
    var options = {
        margin: 0.5,
        filename: name,
        pagebreak: {
            after: "#break",
        },
        image: {
            type: "jpeg",
            quality: 1.0,
        },
        html2canvas: {
            scale: 2,
        },
        jsPDF: {
            unit: "in",
            format: "a4",
            orientation: "landscape",
            compress: true,
        },
    };
    html2pdf()
        .from(element)
        .set(options)
        .outputPdf("arraybuffer")
        .then(async (result) => {
            let blob = new Blob([result], {
                type: "application/pdf",
            });

            let file = new File([blob], name, {
                type: "application/pdf",
                lastModified: new Date(),
            });

           
                //storing file if it was signed by the system
                state.signedPdf = file;
            
            //generating form array buffer that will be used in pdftron and pdfvueApp
            state.systemSignedArrayBuffer = await blob.arrayBuffer();

            //show signed pdf modal
            state.confirmSignedModal = true;

            state.isProcessing = false;
            Swal.close();
        });
}

function offlineSignedUploadedFile(e) {
    state.signedPdf = e.target.files[0];
    state.downloadOfflineModal = false;

    //call function to submit request
    submitIncidentReport();
}
function saveUploadedSignedForm() {
    document.getElementById("offlineSignedIncident").click();
}

//download pettycashForm for offline Signing
function downloadIncidentPdf() {
    state.downloadOfflineModal = false;
    //disable auto signing
    store.commit("LoggedInUser/userSigns", 1);
    var element = document.getElementById("user-incidentform-pdf");
    Swal.fire({
        title: "Downloading...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    var options = {
        margin: 0.5,
        filename: "IncidentForm.pdf",
        pagebreak: {
            after: "#break",
        },
        image: {
            type: "jpeg",
            quality: 1.0,
        },
        html2canvas: {
            scale: 6,
        },
        jsPDF: {
            unit: "in",
            format: "a4",
            orientation: "landscape",
            compress: true,
        },
    };
    html2pdf()
        .from(element)
        .set(options)
        .save()
        .then(() => {
            Swal.close();
        });
}

function AddKeptInCopy() {
    state.supplies.push({ supplies: "", currency: "SSD" });
}
function AddMitigationFollowup() {
    state.mitigationFollowUps.push({ test: "" });
}
function AddMitigations() {
    state.mitigationMeasures.push({ test: "" });
}
</script>

<template>
    <Head :title="'Create Incident Report'" />
    <div>
        <PageHeader v-if="props.showTitle" :title="`Create New Incident Report`" />
        <div class="row">
            <!--end col-->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- <div class="col-6 mb-4">
                            <label>Implementing Partner</label>
                            <v-select :options="implemetingPartnedsArray" v-model="state.implementingPartner" :disabled="state.makeFormDisabled"></v-select>
                        </div> -->
                        <div class="col-6 mb-4">
                            <label>Incident Date</label>
                            <input type="date" class="form-control" v-model="state.incidentDate" :max="state.maxDate" :disabled="state.makeFormDisabled" />
                        </div>
                        <div class="col-4 mb-4">
                            <label>Incident Cases</label>
                            <div class="d-flex flex-row gap-4">
                                <b-form-checkbox id="customControlInline" name="checkbox-1" v-model="state.isTheft" :disabled="state.makeFormDisabled"> Theft </b-form-checkbox>
                                <b-form-checkbox id="customControlInline2" name="checkbox-2" v-model="state.isDamage" :disabled="state.makeFormDisabled"> Damaged </b-form-checkbox>
                                <b-form-checkbox id="customControlInline2" name="checkbox-2" v-model="state.isLooted" :disabled="state.makeFormDisabled"> Looted </b-form-checkbox>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <label>Address Place of incident</label>
                            <v-select :options="locationOptions" :label="'optionString'" v-model="state.newincidentLocation" :disabled="state.makeFormDisabled"></v-select>
                            <!-- <input class="form-control" type="text" v-model="state.newincidentLocation" @focus="state.incidentLocationModal = true" :disabled="state.makeFormDisabled" /> -->
                        </div>
                        <div class="col-6 mb-4">
                            <label>Additional comments on Address</label>
                            <v-select :options="locationCommentOptions" :taggable="true" v-model="state.addressComments" :disabled="state.makeFormDisabled"></v-select>
                            <!-- <input class="form-control" type="text" v-model="state.newincidentLocation" @focus="state.incidentLocationModal = true" :disabled="state.makeFormDisabled" /> -->
                        </div>
                        <div class="col-6 mb-4">
                            <label>Contact Person</label>
                            <input type="text" class="form-control" v-model="state.contactPerson" :disabled="state.makeFormDisabled" placeholder="Name" />
                        </div>
                        <div class="mb-4 col-6">
                            <label>Section</label>
                            <vSelect :clearable="true" label="name" name="officeSupplies" :options="props.sections" v-model="state.selectedSection" :taggable="true" :disabled="state.makeFormDisabled">
                            </vSelect>
                        </div>
                        <div class="col-6 mb-4">
                            <label>Phone Number</label>
                            <div>
                                <input class="form-control" id="incidentPhoneNumber" v-model="state.phone" :disabled="state.makeFormDisabled" />
                            </div>
                        </div>
                        <div class="mb-4 col-10">
                            <label for="request_description">Incident Description</label>
                            <VueEditor v-model="state.incidentDetails" id="incident_details" :disabled="state.makeFormDisabled"></VueEditor>
                        </div>
                        <div class="mb-4 col-12 pt-3">
                            <div class="d-flex flex-row align-items-center gap-3" :class="'col-10'">
                                <div class="col-11 d-flex gap-2">
                                    <div class="col-3">
                                        <label for="request_description">Supplies</label>
                                    </div>
                                    <div class="col-2">
                                        <label for="request_description">Units of Measurement</label>
                                    </div>
                                    <div class="col-1">
                                        <label for="request_description">Qty</label>
                                    </div>
                                    <div class="col-1">
                                        <label for="request_description">Currency</label>
                                    </div>
                                    <div class="col-2">
                                        <label for="request_description">Unit Cost</label>
                                    </div>
                                    <div class="col-2">
                                        <label for="request_description">Value</label>
                                    </div>
                                </div>
                            </div>

                            <div v-for="(field, index) in state.supplies" :key="`Email${index}`" class="row" id="supplies">
                                <div class="d-flex flex-row align-items-center gap-3" :class="'col-10'">
                                    <div class="d-flex flex-row gap-2">
                                        <div class="col-3">
                                            <div class="mb-3">
                                                <!-- <input class="form-control" id="formrow-firstname-input" type="email" v-model="field.supplies" :disabled="state.makeFormDisabled" /> -->
                                                <v-select :options="state.incidentSupplies" :label="'supply'" v-model="field.supplies" :disabled="state.makeFormDisabled"></v-select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-3">
                                                <input class="form-control" id="formrow-firstname-input" type="email" v-model="field.measurementUnit" :disabled="state.makeFormDisabled" />
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="mb-3">
                                                <input
                                                    class="form-control"
                                                    id="formrow-firstname-input"
                                                    type="number"
                                                    v-model="field.qty"
                                                    :disabled="state.makeFormDisabled"
                                                    @input="calculateTotal(index)"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="mb-3">
                                                <select class="form-select form-control" id="" v-model="field.currency" :disabled="state.makeFormDisabled">
                                                    <option selected value="SSD">SSP</option>
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                </select>
                                                <!-- <input class="form-control" id="formrow-firstname-input" type="text" v-model="field.currency" /> -->
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-3">
                                                <Money3Component
                                                    class="form-control"
                                                    v-model="field.unitCost"
                                                    v-bind="state.config"
                                                    @change="calculateTotal(index)"
                                                    :disabled="state.makeFormDisabled"
                                                ></Money3Component>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="mb-3">
                                                <Money3Component
                                                    class="form-control"
                                                    v-model="field.value"
                                                    v-bind="state.config"
                                                    @change="calculateTotal"
                                                    :disabled="state.makeFormDisabled"
                                                ></Money3Component>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row gap-3 justify-content-start align-items-center col-1">
                                        <div v-if="!state.makeFormDisabled">
                                            <i class="bx bxs-x-circle text-danger fs-5 mb-3" role="button" @click="deletesupplyRecord(index)" v-if="state.supplies.length > 1"></i>
                                        </div>
                                        <div v-if="index == state.supplies.length - 1">
                                            <input type="button" class="btn btn-success mb-3" value="Add" @click="AddKeptInCopy" :disabled="state.makeFormDisabled" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-4 pt-3">
                                <label>Has the incident been reported to the nearby police station (through the UN security office)?</label>
                                <div class="d-flex gap-4">
                                    <div class="custom-control custom-radio custom-control-inline" role="button">
                                        <input
                                            role="button"
                                            id="customRadioInline1"
                                            type="radio"
                                            name="customRadioInline1"
                                            class="custom-control-input"
                                            checked
                                            @click="state.hasbeenReportedToPolice = true"
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline1">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                            role="button"
                                            id="customRadioInline2"
                                            type="radio"
                                            name="customRadioInline1"
                                            class="custom-control-input"
                                            @click="state.hasbeenReportedToPolice = false"
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline2">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-4" v-if="state.hasbeenReportedToPolice">
                                <label>What was their feedback?</label>
                                <textarea class="form-control" rows="4" placeholder="" v-model="state.feedbackPolice" :disabled="state.makeFormDisabled"></textarea>
                            </div>
                            <div class="col-6 mb-5" v-if="!state.hasbeenReportedToPolice">
                                <label>Why hasn't it been reported?</label>
                                <textarea class="form-control" rows="4" placeholder="" v-model="state.whynotReportedPolice" :disabled="state.makeFormDisabled"></textarea>
                            </div>
                            <div class="col-12 mb-4">
                                <label>Has the incident been reported to the nearby local leader?</label>
                                <div class="d-flex gap-4">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                            role="button"
                                            id="customRadioInline3"
                                            type="radio"
                                            name="customRadioInline3"
                                            class="custom-control-input"
                                            @click="state.hasbeenReportedToLocalLeader = true"
                                            checked
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline3">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                            role="button"
                                            id="customRadioInline4"
                                            type="radio"
                                            name="customRadioInline3"
                                            class="custom-control-input"
                                            @click="state.hasbeenReportedToLocalLeader = false"
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline4">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-4" v-if="state.hasbeenReportedToLocalLeader">
                                <label>What was their feedback?</label>
                                <textarea class="form-control" rows="4" placeholder="" v-model="state.feedbacklocal" :disabled="state.makeFormDisabled"></textarea>
                            </div>
                            <div class="col-6 mb-4" v-if="!state.hasbeenReportedToLocalLeader">
                                <label>Why hasn't it been reported?</label>
                                <textarea class="form-control" rows="4" placeholder="" v-model="state.whynotReportedlocal" :disabled="state.makeFormDisabled"></textarea>
                            </div>

                            <div>
                                <label>What are the mitigation measures put in place by the partner?</label>
                                <div v-for="(field, index) in state.mitigationMeasures" :key="`mitigation${index}`" id="mitigatins">
                                    <div class="d-flex" :class="'col-12'">
                                        <div class="mb-4 col-5">
                                            <textarea class="form-control" type="text" :maxlength="255" v-model="field.mitigationMeasure" :disabled="state.makeFormDisabled" />
                                        </div>
                                        <div class="d-flex flex-row gap-3 justify-content-start align-items-center col-1">
                                            <div v-if="!state.makeFormDisabled" class="ms-2">
                                                <i class="bx bxs-x-circle text-danger fs-5 mb-3" role="button" @click="deleteMitigationRecord(index)" v-if="state.mitigationMeasures.length > 1"></i>
                                            </div>
                                            <div v-if="index == state.mitigationMeasures.length - 1" class="ms-3">
                                                <input type="button" class="btn btn-success mb-3" value="Add" @click="AddMitigations" :disabled="state.makeFormDisabled" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <label>Was the incidence reported to UNICEF on time?</label>
                                <div class="d-flex gap-4">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                            role="button"
                                            id="customRadioInline4"
                                            type="radio"
                                            name="customRadioInline4"
                                            class="custom-control-input"
                                            @click="state.reportedOnTime = true"
                                            checked
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline3">Yes</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                            role="button"
                                            id="customRadioInline4"
                                            type="radio"
                                            name="customRadioInline4"
                                            class="custom-control-input"
                                            @click="state.reportedOnTime = false"
                                            :disabled="state.makeFormDisabled"
                                        />
                                        <label class="custom-control-label ms-2" for="customRadioInline4">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-4" v-if="!state.reportedOnTime">
                                <label>What led to the delay of reporting the incident?</label>
                                <textarea class="form-control" rows="4" placeholder="" v-model="state.reportDelayCause" :disabled="state.makeFormDisabled"></textarea>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex flex-row align-items-center gap-3" :class="'w-70'">
                                    <div class="d-flex gap-2 w-70">
                                        <div class="w-50">
                                            <label for="request_description">What are the follow-up actions and timelines for their execution?</label>
                                        </div>
                                        <div class="">
                                            <label for="request_description">Deadline (est.)</label>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="(field, index) in state.mitigationFollowUps" :key="`Email${index}`" class="row" id="followups">
                                    <div class="d-flex gap-3">
                                        <div class="d-flex flex-row gap-2 w-70">
                                            <div class="w-80">
                                                <div class="mb-3">
                                                    <input class="form-control" id="formrow-firstname-input" type="text" v-model="field.followup" :disabled="state.makeFormDisabled" />
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="mb-3">
                                                    <input class="form-control" id="formrow-firstname-input" type="date" v-model="field.deadline" :disabled="state.makeFormDisabled" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row gap-3 justify-content-start align-items-center">
                                            <div v-if="!state.makeFormDisabled">
                                                <i class="bx bxs-x-circle text-danger fs-5 mb-3" role="button" @click="deletemitigationFollowup(index)" v-if="state.mitigationFollowUps.length > 1"></i>
                                            </div>
                                            <div v-if="index == state.mitigationFollowUps.length - 1">
                                                <input type="button" class="btn btn-success mb-3" value="Add" @click="AddMitigationFollowup" :disabled="state.makeFormDisabled" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top">
                                <div class="pt-4 pb-3 d-flex gap-3" v-if="state.makeFormDisabled == true">
                                    <b-button
                                        variant="secondary"
                                        class="w-lg"
                                        @click="
                                            () => {
                                                state.makeFormDisabled = false;
                                                state.showSubmissionLoader = false;
                                            }
                                        "
                                        :disabled="state.showSubmissionLoader"
                                    >
                                        Back
                                    </b-button>
                                    <b-button variant="primary" class="w-lg" @click="submitRequest" :disabled="state.showSubmissionLoader">
                                        <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.showSubmissionLoader"></i>Confirm & Submit
                                    </b-button>
                                </div>
                                <div class="pt-4 pb-3" v-else>
                                    <b-button variant="primary" class="w-lg" @click="state.makeFormDisabled = true"> Proceed </b-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none">
                <UserSignPdf :details="formDetails" />
            </div>
        </div>

        <!-- modal to confirm the signed pettycash -->
        <b-modal v-model="state.confirmSignedModal" size="lg" title="Confirm that your Signature is correctly positioned " title-class="font-18" centered hide-footer hide-title>
            <!-- Modal body -->
            <div class="" style="padding: ">
                <div v-if="state.iWillSign != 1" class="small text-muted text-center">Before proceeding, verify that the signed document has your signature in the correct location.</div>
                <br />
                <div class="col-12">
                    <!-- pdftron -->
                    <!-- <div v-if="state.iWillSign == 1">
                        <div v-if="state.systemSignedArrayBuffer != null">
                            <PdfTronViewer :pdfArrayBufferData="state.systemSignedArrayBuffer" :canSign="true" :id="'incidentSignOriginator'" @saveUserSignedForm="savePdftronForm" />
                        </div>
                    </div> -->
                    <div>
                        <div v-if="state.systemSignedArrayBuffer != null">
                            <vue-pdf-app style="height: 80vh" theme="light" :pdf="state.systemSignedArrayBuffer" file-name="IncidentReport.pdf"></vue-pdf-app>
                        </div>
                        <div v-else>Loading</div>
                    </div>
                </div>

                <div style="text-align: center" class="mt-3 mb-2">
                    <b-button variant="primary" type="button" class="unicef-btn unicef-primary w-md ml-1" @click="submitIncidentReport">
                        <span>Proceed</span>
                    </b-button>
                </div>
            </div>
        </b-modal>

        <!-- approve  offline  modal -->
        <b-modal v-model="state.downloadOfflineModal" centered hide-footer hide-title>
            <div>
                <div class=" ">
                    <!-- <div class="modal-header"></div> -->
                    <div class="">
                        <!-- carousel -->
                        <div class="w-100 d-flex flex-row justify-content-between">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="">STEP 1</div>
                                <div class="">
                                    <i class="mdi mdi-cloud-download" style="font-size: 50px; color: #a5a5a5"></i>
                                </div>
                                <div class="text-center">
                                    <div @click="downloadIncidentPdf" style="" class="text-primary" role="button">Click to Download</div>
                                    <small class="text-muted">IncidentForm.pdf</small> <br />
                                    <div class="invisible">download form</div>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-end">
                                <div class="">STEP 2</div>
                                <div class="">
                                    <i class="mdi mdi-signature-freehand" style="font-size: 50px; color: #a5a5a8"> </i>
                                </div>
                                <div class="text-center">
                                    <div style="" class="text-muted">Sign</div>
                                    <small class="text-muted invisible"
                                        >This is done <br />
                                        offline.
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <div class="">STEP 3</div>
                                <div class="">
                                    <i class="mdi mdi-cloud-upload" style="color: #0160a0; font-size: 50px"></i>
                                </div>
                                <div class="text-center">
                                    <input id="offlineSignedIncident" type="file" @change="offlineSignedUploadedFile" ref="offlineSignedPettyCash" accept="application/pdf" hidden />

                                    <div style="" class="text-primary" role="button" @click="saveUploadedSignedForm">Click to Upload</div>
                                    <small class="text-muted">Upload file you've signed</small> <br />
                                    <div class="invisible">download form</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        <!-- end of modal -->

        <b-modal class="" v-model="state.incidentLocationModal" size="lg" title="Drag marker to select incident location" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-5">
                <div class="mt-3 mb-3">
                    {{ state.incidentLocationLat }}
                    {{ state.incidentLocationLng }}
                </div>

                <GoogleMap api-key="AIzaSyCBE7aRb0VJdRBlUnvjcQ_lnKWoAWuXUx8" :center="state.markCenter" :zoom="16" style="height: 400px">
                    <Marker :options="{ position: state.markCenter, draggable: true }" @dragend="selectRoomLocation" />
                </GoogleMap>
            </div>

            <div class="text-center mb-4">
                <b-button variant="primary" @click="state.incidentLocationModal = false">Select Location</b-button>
            </div>
        </b-modal>
    </div>
</template>
<style scoped>
/* pdf viewer customisation */

.pdf-app.light {
    --pdf-toolbar-color: #0160a0;
}
</style>
