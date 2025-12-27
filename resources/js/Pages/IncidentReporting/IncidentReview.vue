<script setup>
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import { onMounted, computed, reactive } from "vue";
import moment from "moment";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";
import Swal from "sweetalert2";
import { Money3Component } from "v-money3";
import html2pdf from "html2pdf.js";
import icondata from "../../../images/icondata.png";
import CreateIncident from "./CreateIncident.vue";
import simplebar from "simplebar-vue";
import PDFMerger from "pdf-merger-js";
import AudioPlayer from "vue3-audio-player";
import VuePdfApp from "vue3-pdf-app";
import "vue3-pdf-app/dist/icons/main.css";
import VueEasyLightbox from "vue-easy-lightbox";
import { VueEditor } from "vue3-editor";
import "vue3-audio-player/dist/style.css";
const props = defineProps({
    incident: Object,
    implementingPartners: Array,
    donors: Array,
    incidentLocations: Array,
});

defineOptions({ layout: Layout });

let merger = new PDFMerger();

//state
const state = reactive({
    userPermissions: [],
    returnRequestToStaffModal: false,
    returnReason: "",
    retrunTo: "",
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
    supplies: [{ id: 1, currency: "SSD" }],
    affected: [{ id: 1 }],
    isUserChiefField: false,
    fileToSignArrayBuffer: null,
    systemSignedArrayBuffer: null,
    isMerged: false,
    signedFile: null,
    showSignedPdf: false,
    latestForm: null,
    showImages: false,
    imageUrl: [],
    showAudioPlayerModal: false,
    audioUrl: "",
    incidentSupplies: [],
    incidentComment: "",
    submittingComment: false,
});

//mounted
onMounted(() => {
    //setting user permissions
    state.userPermissions = usePage().props.auth.user.allPermissions.map((perm) => perm.name);
    state.isUserChiefField = state.userPermissions.includes("field_office_chief");
    //click module dropdown
    document.getElementById("clickOnInitialLoad").click();

    //geting file to merge
    getFileToMerge();
    getLatestSignedPdf();

    //get incidents
    getIncidentSupplies();
});
const isChiefField = computed(() => {
    if (state.userPermissions.includes("field_office_chief")) {
        return true;
    } else {
        return false;
    }
});

const incidentFiles = computed(() => {
    const IncidentLogs = props.incident.logs;
    let cleanLogFiles = [];

    IncidentLogs.forEach((item) => {
        const itemFiles = item.log_files;
        itemFiles.forEach((item) => cleanLogFiles.unshift(item));
    });

    return cleanLogFiles;
});

async function getLatestSignedPdf() {
    const IncidentLogs = props.incident.logs;
    let cleanLogFiles = [];

    IncidentLogs.forEach((item) => {
        const itemFiles = item.log_files;
        itemFiles.forEach((item) => cleanLogFiles.unshift(item));
    });
    const lastestPdf = cleanLogFiles.find((item) => getExtension(item.original_url) == "pdf");
    const fileUrl = lastestPdf.original_url;

    const fileResponse = await fetch(fileUrl);
    state.latestForm = await fileResponse.arrayBuffer();
}

const unicefStaff = computed(() => {
    const staff = props.staff.map((staff) => ({
        label: `${staff.name}`,
        id: staff.id,
        email: staff.email,
    }));

    return staff;
});

const reversedTimelines = computed(() => {
    return props.incident.incident_timeline.slice().reverse();
});

const reversedIncidentFiles = computed(() => {
    return props.incident.logs[0].log_files.slice().reverse();
});

//method
async function getFileToMerge() {
    const IncidentLogs = props.incident.logs;
    let cleanLogFiles = [];

    IncidentLogs.forEach((item) => {
        const itemFiles = item.log_files;
        itemFiles.forEach((item) => cleanLogFiles.unshift(item));
    });

    const lastestPdf = cleanLogFiles.find((item) => getExtension(item.original_url) == "pdf");
    const fileToAppend = await fetch(`${lastestPdf.original_url}`);
    state.fileToSignArrayBuffer = await fileToAppend.arrayBuffer();
}
function getIncidentSupplies() {
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };
            const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

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
function submitComment() {
    state.submittingComment = true;
    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };
    let formData = new FormData();

    formData.append("incidentId", props.incident.id);
    formData.append("comment", state.incidentComment);
            const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

    if (state.incidentComment != "") {
          Swal.fire({
                title: "Please wait...",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        axios
            .post(`${incidentUrl}/api/incidents/submitComment`, formData, options)
            .then((res) => {
                state.submittingComment = false;
                state.incidentComment = "";
                Swal.fire({
                    icon: "success",
                    title: "Comment Submitted Successfully",
                    html: "<p style='font-size: 14px'>You have successfully ammended a comment to the incident report</p>",
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    showCloseButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#32CD32",
                }).then((result) => {
                    if (result.isConfirmed) {
                        state.submittingComment = false;
                        state.incidentComment = "";
                        router.reload();
                        Swal.close();
                    }
                });
            })
            .catch(function (error) {
                console.log(error);
                state.submittingComment = false;
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
                        state.submittingComment = false;

                        Swal.close();
                    }
                });
            });
    }
}
function closeIncident(){
        Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to mark this case as closed.</p>`,
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
            let formData = new FormData();

            const options = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };
            formData.append("incidentId", props.incident.id);
            const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

            axios
                .post(`${incidentUrl}/api/incidents/close-incident`, formData, options)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'> The case has been closed successfully.</p>",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        showCloseButton: false,
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#32CD32",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.close();
                            router.reload();
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
                            Swal.close();
                        }
                    });
                });
        }
    });
}
function signForm() {
    Swal.fire({
        title: "Proceed with Approval",
        icon: "info",
        html: `<div style="font-size: 14px">
                                    You are about to proceed with approving this request.<br/>
                                    A PDF form evidencing this approval will be automatically signed by the system (on your behalf).
                                        </div>
                                    `,
        coler: "#99A5F6",
        showCloseButton: false,
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonColor: "#43ad60",
        confirmButtonText: "Proceed",
        allowOutsideClick: false,
        allowEscapeKey: false,
        closeOnClickOutside: false,
    }).then((result) => {
        if (result.isConfirmed) {
            if (usePage().props.auth.user.signature != null) {
                generateFile();
            } else {
                //TODO: alert user if they dont have a signature within the system
                console.log("user has no registered signature");
            }
        }
    });
}
function AddKeptInCopy() {
    state.supplies.push({ supplies: "", currency: "SSD" });
}
function deletesupplyRecord(index) {
    state.supplies.splice(index, 1);
}

function AddKeptInCopyAffected() {
    state.affected.push({ supplies: "", currency: "SSD" });
}
function deletesupplyRecordAffected(index) {
    state.affected.splice(index, 1);
}
async function approveRequest() {
    state.showSignedPdf = false;
    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const options = {
        headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    let formData = new FormData();

    formData.append("requestId", props.incident.id);
    formData.append("supplies", JSON.stringify(state.supplies));
    formData.append("affected", JSON.stringify(state.affected));
    formData.append("signedFile", state.signedFile);
            const incidentUrl = import.meta.env.VITE_INCIDENTREPORTING_URL;

    axios
        .post(`${incidentUrl}/api/incidents/approve_request`, formData, options)
        .then((res) => {
            Swal.fire({
                icon: "success",
                title: "Report Approved Successfully",
                html: "<p style='font-size: 14px'>Report has been approved successfully</p>",
                showConfirmButton: true,
                allowOutsideClick: false,
                showCloseButton: true,
                confirmButtonText: "Ok",
                confirmButtonColor: "#32CD32",
            }).then((result) => {
                if (result.isConfirmed) {
                    state.isProcessing = false;
                    router.visit("/myIncidentReports");
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
// function returnToStaff() {
//     state.isProcessing = true;

//     const options = {
//         headers: {
//             "Content-Type": "multipart/form-data",
//             Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
//         },
//     };

//     let formData = new FormData();
//     formData.append("reason", state.returnReason);
//     formData.append("returnTo", state.retrunTo);
//     formData.append("closeOnReturn", state.closeAfterReturn);
//     formData.append("requestId", props.accident.id);

//     axios
//         .post(`/api/return-report`, formData, options)
//         .then((res) => {
//             state.returnRequestToStaffModal = false;

//             Swal.fire({
//                 icon: "success",
//                 title: "Report Returned",
//                 html: "<p style='font-size: 14px'>Report has been returned successfully</p>",
//                 showConfirmButton: true,
//                 allowOutsideClick: false,
//                 showCloseButton: true,
//                 confirmButtonText: "Ok",
//                 confirmButtonColor: "#32CD32",
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     state.isProcessing = false;
//                     router.visit("/");
//                     Swal.close();
//                 }
//             });
//         })
//         .catch(function (error) {
//             console.log(error);
//             state.isProcessing = false;
//             Swal.fire({
//                 title: "Something Went Wrong",
//                 icon: "error",
//                 html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to ICT for help.</p>`,
//                 showCloseButton: false,
//                 showCancelButton: false,
//                 focusConfirm: true,
//                 confirmButtonText: "OK",
//                 confirmButtonColor: "#43ad60",
//                 allowOutsideClick: false,
//                 allowEscapeKey: false,
//                 closeOnClickOutside: false,
//             }).then((result) => {
//                 if (result.value) {
//                     state.isProcessing = false;
//                     Swal.close();
//                 }
//             });
//         });
// }

function formatDate(date) {
    return moment(date).format("MMMM Do YYYY [at] h:mm a");
}
function formatTime(date) {
    return moment(date).format("h:mm a");
}
function getExtension(name) {
    // gets filename extension
    const regex = new RegExp("[^.]+$");
    const extension = name.match(regex);
    return extension;
}

function downloadrequestFile(file) {
    window.open(`${file.original_url}`, "_blank");
}

function showDownloadOffline() {
    Swal.close();
    state.downloadOfflineModal = true;
}

async function generateFile() {
    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    state.signProcessing = true;
    let name = "IncidentReportForm";
    let element = document.getElementById("chiefFieldOffice-pdf");

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
            scale: 1.2,
        },
        jsPDF: {
            unit: "in",
            format: "a4",
            orientation: "landscape",
            compress: true,
        },
    };

    await html2pdf()
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

            /**
             * Note: For this case the file is only signed with html2pdf
             */
            if (state.isMerged == true) {
                // reset merger to avoid duplicates
                merger.reset();
            }

            await merger.add(state.fileToSignArrayBuffer);
            await merger.add(file);
            state.isMerged = true;
            //generating form array buffer that will be used in pdftron and pdfvueApp
            const UnitArrayfile = await merger.saveAsBuffer();
            state.systemSignedArrayBuffer = UnitArrayfile.buffer;

            state.systemSignedBlob = await merger.saveAsBlob();

            let mergedfile = new File([state.systemSignedBlob], name, {
                type: "application/pdf",
                lastModified: new Date(),
            });

            state.signedFile = mergedfile;
            state.showSignedPdf = true;
            Swal.close();
        });
}

function showImage(imgUrl) {
    state.imageUrl.push(imgUrl);
    state.showImages = true;
}

function calculateTotal(index) {
    if (state.supplies[index] != null) {
        const item = state.supplies[index];
        const qty = item?.qty != null ? item?.qty : 0;
        const unit = item.unitCost;
        state.supplies[index].value = qty * unit;
    }
}
function onHide() {
    state.showImages = false;
}
function showAudioPlayer(audioUrl) {
    state.audioUrl = audioUrl;
    state.showAudioPlayerModal = true;
}
</script>
<template>
    <Head :title="props.incident.reference_no" />

    <div>
        <PageHeader :title="`Incident: ${props.incident.reference_no} `" />
        <div class="row">
            <!--end col-->
            <div class="col-xl-9">
                <div class="card" v-if="props.incident.logs[props.incident.logs.length - 1].channel === 'web'">
                    <div class="card-body">
                        <div class="p-3">
                            <div class="">
                                <div v-if="isChiefField == false">
                                    <b-tabs>
                                       
                                        <b-tab title="Note For the Record (NFR)" active>
                                            <div v-if="props.incident.status == 'pending'">
                                                <div class="d-flex flex-column align-items-center mt-5 pb-5">
                                                    <div class="mb-4 pt-5"><img :src="icondata" :height="80" /></div>
                                                    <div>NFR has not yet been generated</div>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div class="mt-5">
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="d-flex col-12">
                                                            <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Implementing Partner</div>
                                                            <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.implementing_partner_name }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="d-flex col-12">
                                                            <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Date</div>
                                                            <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.incidentDate }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="d-flex col-12">
                                                            <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Cases</div>
                                                            <div class="col-9 pt-2 pb-2 ps-4">
                                                                <span class="me-3">{{ props.incident.incident_theft_case == 1 ? "Theft" : "" }}</span
                                                                ><span>{{ props.incident.incident_damage_case == 1 ? "Damaged" : "" }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="d-flex col-12">
                                                            <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Address</div>
                                                            <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.incident_address }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="d-flex col-12">
                                                            <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Contact Person</div>
                                                            <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.contact_person }}</div>
                                                        </div>
                                                    </div>
                                                    <div v-if="props.incident.status == 'approved'">
                                                        <div class="border border-opacity-10 border-secondary">
                                                            <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Incident Details</div>
                                                        </div>
                                                        <div class="border border-opacity-10 border-secondary" id="break" v-if="props.incident.incident_supply.length > 0">
                                                            <div class="d-flex">
                                                                <div class="col-4 pt-2 pb-2 ps-3 fw-bold">Supplies</div>
                                                                <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit of Measurement</div>
                                                                <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Quantity</div>
                                                                <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit Cost ($)</div>
                                                                <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Value</div>
                                                            </div>
                                                            <div class="d-flex border-bottom" v-for="(item, index) in props.incident.incident_supply" :key="index">
                                                                <div class="col-4 pt-2 pb-2 ps-3">{{ item.supplyItem }}</div>
                                                                <div class="col-2 pt-2 pb-2 text-ceter">{{ item.measurement_unit }}</div>
                                                                <div class="col-2 pt-2 pb-2 text-ceter">{{ item.quantity }}</div>
                                                                <div class="col-2 pt-2 pb-2 text-ceter">{{ item.unit_cost }}</div>
                                                                <div class="col-2 pt-2 pb-2 text-ceter">{{ item.value }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="border border-opacity-10 border-secondary">
                                                            <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Affected</div>
                                                        </div>
                                                        <div class="border border-opacity-10 border-secondary" id="break" v-if="props.incident.incident_supply.length > 0">
                                                            <div class="d-flex">
                                                                <div class="col-5 pt-2 pb-2 ps-3 fw-bold">Grant/Donor</div>
                                                                <div class="col-5 pt-2 pb-2 fw-bold text-ceter">Donor</div>
                                                                <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Amount</div>
                                                            </div>
                                                            <div class="d-flex border-bottom" v-for="(item, index) in props.incident.affected_entity" :key="index">
                                                                <div class="col-5 pt-2 pb-2 ps-3">{{ item.grant }}</div>
                                                                <div class="col-5 pt-2 pb-2 text-ceter">{{ item.donor }}</div>
                                                                <div class="col-2 pt-2 pb-2 text-ceter">{{ item.amount }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="props.incident.status == 'pending'" class="text-center mt-5">
                                                <div class="fw-medium">Currently with {{ props.incident.currentlyWith_name }}</div>
                                            </div>
                                            <div v-else-if="props.incident.status == 'approved'" class="text-center mt-5">
                                                <div class="fw-medium">Currently with {{ props.incident.currentlyWith_name }}</div>
                                            </div>
                                        </b-tab>
                                        <b-tab :title="`Comments (${props.incident.comments != null ? props.incident.comments.length : 0})`">
                                            <div class="d-flex flex-column justify-content-between">
                                                <div>
                                                    <div v-if="props.incident.comments != null && props.incident.comments.length > 0" class="mt-4">
                                                        <b-card-text>
                                                            <div class="tab-information">
                                                                <div class="row d-flex justify-content-start">
                                                                    <div class="card shadow-md col-md-8" v-for="(item, ky) in props.incident.comments" :key="`${ky}-${item.id}-comment`">
                                                                        <div class="card-body">
                                                                            <div>
                                                                                <b>{{ item.stage_at }}</b>
                                                                            </div>
                                                                            <div v-html="item.comment"></div>
                                                                            <small>By {{ item.made_by }} on {{ formatDate(item.created_at) }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-card-text>
                                                    </div>
                                                    <div v-else class="d-flex flex-column align-items-center" style="padding-top: 15vh; padding-bottom: 30vh">
                                                        <div class="mb-4 pt-5"><img :src="icondata" :height="80" /></div>
                                                        <div>Incident Report has no comments.</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="">
                                                    <label for="request_description">Add Comment:</label>
                                                    <VueEditor v-model="state.incidentComment" id="request_description"></VueEditor>
                                                </div>
                                                <div class="mt-4">
                                                    <b-button @click="submitComment" variant="primary" class="w-lg" :disabled="state.submittingComment"
                                                        ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.submittingComment"></i>Submit Comment</b-button
                                                    >
                                                </div>
                                            </div>
                                        </b-tab>
                                    </b-tabs>
                                </div>
                                <div v-else>
                                    <b-tabs>
                                        <b-tab title="Note For the Record (NFR)" active>
                                            <div class="mt-5">
                                                <div class="border border-opacity-10 border-secondary">
                                                    <div class="d-flex col-12">
                                                        <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Implementing Partner</div>
                                                        <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.implementing_partner_name }}</div>
                                                    </div>
                                                </div>
                                                <div class="border border-opacity-10 border-secondary">
                                                    <div class="d-flex col-12">
                                                        <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Date</div>
                                                        <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.incidentDate }}</div>
                                                    </div>
                                                </div>
                                                <div class="border border-opacity-10 border-secondary">
                                                    <div class="d-flex col-12">
                                                        <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Incident Cases</div>
                                                        <div class="col-9 pt-2 pb-2 ps-4">
                                                            <span class="me-3">{{ props.incident.incident_theft_case == 1 ? "Theft" : "" }}</span
                                                            ><span>{{ props.incident.incident_damage_case == 1 ? "Damaged" : "" }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border border-opacity-10 border-secondary">
                                                    <div class="d-flex col-12">
                                                        <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Address</div>
                                                        <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.incident_address }}</div>
                                                    </div>
                                                </div>
                                                <div class="border border-opacity-10 border-secondary">
                                                    <div class="d-flex col-12">
                                                        <div class="col-3 border-end pt-2 pb-2 ps-3 fw-bold" style="background-color: #fafafa">Contact Person</div>
                                                        <div class="col-9 pt-2 pb-2 ps-4">{{ props.incident.contact_person }}</div>
                                                    </div>
                                                </div>
                                                <div v-if="props.incident.status == 'approved' || props.incident.status == 'closed'">
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Incident Details</div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary" id="break" v-if="props.incident.incident_supply.length > 0">
                                                        <div class="d-flex">
                                                            <div class="col-4 pt-2 pb-2 ps-3 fw-bold">Supplies</div>
                                                            <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit of Measurement</div>
                                                            <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Quantity</div>
                                                            <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Unit Cost ($)</div>
                                                            <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Value</div>
                                                        </div>
                                                        <div class="d-flex border-bottom" v-for="(item, index) in props.incident.incident_supply" :key="index">
                                                            <div class="col-4 pt-2 pb-2 ps-3">{{ item.supplyItem }}</div>
                                                            <div class="col-2 pt-2 pb-2 text-ceter">{{ item.measurement_unit }}</div>
                                                            <div class="col-2 pt-2 pb-2 text-ceter">{{ item.quantity }}</div>
                                                            <div class="col-2 pt-2 pb-2 text-ceter">{{ item.unit_cost }}</div>
                                                            <div class="col-2 pt-2 pb-2 text-ceter">{{ item.value }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary">
                                                        <div class="col-3 pt-2 pb-2 ps-3 fw-bold">Affected</div>
                                                    </div>
                                                    <div class="border border-opacity-10 border-secondary" id="break" v-if="props.incident.incident_supply.length > 0">
                                                        <div class="d-flex">
                                                            <div class="col-5 pt-2 pb-2 ps-3 fw-bold">Grant/Donor</div>
                                                            <div class="col-5 pt-2 pb-2 fw-bold text-ceter">Donor</div>
                                                            <div class="col-2 pt-2 pb-2 fw-bold text-ceter">Amount</div>
                                                        </div>
                                                        <div class="d-flex border-bottom" v-for="(item, index) in props.incident.affected_entity" :key="index">
                                                            <div class="col-5 pt-2 pb-2 ps-3">{{ item.grant }}</div>
                                                            <div class="col-5 pt-2 pb-2 text-ceter">{{ item.donor }}</div>
                                                            <div class="col-2 pt-2 pb-2 text-ceter">{{ item.amount }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="props.incident.status == 'pending' && isChiefField">
                                                    <div class="mt-5 fw-bold">Add More Information</div>
                                                    <div class="mt-4 pb-4 border-bottom">
                                                        <div class="d-flex flex-row align-items-center gap-3" :class="'col-12'">
                                                            <div class="col-11 d-flex gap-2">
                                                                <div class="col-3">
                                                                    <label for="request_description">Supplies</label>
                                                                </div>
                                                                <div class="col-2">
                                                                    <label for="request_description">Measurement</label>
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
                                                            <div class="d-flex flex-row align-items-center" :class="'col-11'">
                                                                <div class="d-flex flex-row col-12 gap-2">
                                                                    <div class="col-3">
                                                                        <div class="mb-3">
                                                                            <!-- <input class="form-control" id="formrow-firstname-input" type="email" v-model="field.supplies" :disabled="state.makeFormDisabled" /> -->
                                                                            <v-select
                                                                                :options="state.incidentSupplies"
                                                                                :label="'supply'"
                                                                                v-model="field.supplies"
                                                                                :disabled="state.makeFormDisabled"
                                                                            ></v-select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2">
                                                                        <div class="mb-3">
                                                                            <input
                                                                                class="form-control"
                                                                                id="formrow-firstname-input"
                                                                                type="email"
                                                                                v-model="field.measurementUnit"
                                                                                :disabled="state.makeFormDisabled"
                                                                            />
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
                                                                        <i
                                                                            class="bx bxs-x-circle text-danger fs-5 mb-3"
                                                                            role="button"
                                                                            @click="deletesupplyRecord(index)"
                                                                            v-if="state.supplies.length > 1"
                                                                        ></i>
                                                                    </div>
                                                                    <div v-if="index == state.supplies.length - 1">
                                                                        <input type="button" class="btn btn-success mb-3" value="Add" @click="AddKeptInCopy" :disabled="state.makeFormDisabled" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 pb-4 border-bottom">
                                                        <div class="d-flex flex-row align-items-center gap-3" :class="'col-12'">
                                                            <div class="col-11 d-flex gap-2">
                                                                <div class="col-4">
                                                                    <label for="request_description">Grant/Donor Affected by Loss</label>
                                                                </div>
                                                                <div class="col-5">
                                                                    <label for="request_description">Donor</label>
                                                                </div>

                                                                <div class="col-2">
                                                                    <label for="request_description">Amount(USD)</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div v-for="(field, index) in state.affected" :key="`Affected${index}`" class="row" id="affected">
                                                            <div class="d-flex flex-row align-items-center" :class="'col-11'">
                                                                <div class="d-flex flex-row col-12 gap-2">
                                                                    <div class="col-4">
                                                                        <div class="mb-3">
                                                                            <input class="form-control" id="formrow-firstname-input" type="email" v-model="field.grant" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-5">
                                                                        <div class="mb-3">
                                                                            <!-- <input class="form-control" id="formrow-firstname-input" type="email" v-model="field.donor" /> -->
                                                                            <v-select :options="props.donors" :label="'donor_name'" v-model="field.donor"></v-select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-2">
                                                                        <div class="mb-3">
                                                                            <Money3Component class="form-control" v-model="field.amount" v-bind="state.config"></Money3Component>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-row gap-3 justify-content-start align-items-center col-1">
                                                                    <div v-if="!state.makeFormDisabled">
                                                                        <i
                                                                            class="bx bxs-x-circle text-danger fs-5 mb-3"
                                                                            role="button"
                                                                            @click="deletesupplyRecordAffected(index)"
                                                                            v-if="state.affected.length > 1"
                                                                        ></i>
                                                                    </div>
                                                                    <div v-if="index == state.affected.length - 1">
                                                                        <input type="button" class="btn btn-success mb-3" value="Add" @click="AddKeptInCopyAffected" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-if="props.incident.status == 'pending'" class="text-center mt-5">
                                                        <div v-if="isChiefField">
                                                            <b-button variant="primary" class="w-lg" @click="signForm"> Approve </b-button>
                                                        </div>
                                                        <div v-else class="fw-medium">Currently with {{ props.incident.currentlyWith_name }}</div>
                                                    </div>
                                                    <div v-else-if="props.incident.status == 'approved'" class="text-center mt-5">
                                                        <div class="fw-medium">Currently with {{ props.incident.currentlyWith_name }}</div>
                                                    </div>
                                                </div>
                                                <div v-if="props.incident.status == 'approved' && isChiefField" class="text-center mt-5">
                                                    <b-button variant="primary" class="w-lg" @click="closeIncident"> Close Case </b-button>
                                                </div>
                                                  <div v-if="props.incident.status == 'closed'" class="text-center mt-5">
                                                Case Closed
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab :title="`Comments (${props.incident.comments != null ? props.incident.comments.length : 0})`">
                                            <div class="d-flex flex-column justify-content-between">
                                                <div>
                                                    <div v-if="props.incident.comments != null && props.incident.comments.length > 0" class="mt-4">
                                                        <b-card-text>
                                                            <div class="tab-information">
                                                                <div class="row d-flex justify-content-start">
                                                                    <div class="card shadow-md col-md-8" v-for="(item, ky) in props.incident.comments" :key="`${ky}-${item.id}-comment`">
                                                                        <div class="card-body">
                                                                            <div>
                                                                                <b>{{ item.stage_at }}</b>
                                                                            </div>
                                                                            <div v-html="item.comment"></div>
                                                                            <small>By {{ item.made_by }} on {{ formatDate(item.created_at) }}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </b-card-text>
                                                    </div>
                                                    <div v-else class="d-flex flex-column align-items-center" style="padding-top: 15vh; padding-bottom: 30vh">
                                                        <div class="mb-4 pt-5"><img :src="icondata" :height="80" /></div>
                                                        <div>Incident Report has no comments.</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="">
                                                    <label for="request_description">Add Comment:</label>
                                                    <VueEditor v-model="state.incidentComment" id="request_description"></VueEditor>
                                                </div>
                                                <div class="mt-4">
                                                    <b-button @click="submitComment" variant="primary" class="w-lg" :disabled="state.submittingComment"
                                                        ><i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.submittingComment"></i>Submit Comment</b-button
                                                    >
                                                </div>
                                            </div>
                                        </b-tab>
                                    </b-tabs>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <CreateIncident
                        :implementingPartners="props.implementingPartners"
                        :showTitle="false"
                        :incidentId="props.incident.id"
                        :incidentDate="props.incident.incidentDate"
                        :incidentDescription="props.incident.incident_details"
                        :incidentReportedByName="props.incident.reported_by_name"
                        :incidentReportedByEmail="props.incident.reported_by_email"
                        :incidentLocations="props.incidentLocations"
                    />
                </div>
            </div>
            <!--end col-->
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-body mx-1">
                        <div class="d-flex flex-row justify-content-between mb-4">
                            <div><h5 style="color: #666; font-size: 14px">Timeline</h5></div>
                            <div>
                                <h5 style="color: #666; font-size: 14px">
                                    <small></small>
                                </h5>
                            </div>
                        </div>
                        <simplebar style="max-height: 35vh" id="my-timeline">
                            <div v-if="props.incident.incident_timeline.length > 0">
                                <ul class="verti-timeline list-unstyled" v-for="time in reversedTimelines" v-bind:key="'timeline__' + time.id">
                                    <li class="event-list">
                                        <div class="event-timeline-dot">
                                            <i :class="time.icon" class="" :style="{ color: time.color }"></i>
                                        </div>
                                        <div class="d-flex pb-3">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <div class="" style="font-size: 11px; color: #777777">{{ time.title }}</div>
                                                    <div class="text-muted">
                                                        <div style="font-size: 9px; color: #999999">{{ formatDate(time.created_at) }} by {{ time.timeline_by_name }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else>
                                <div class="d-flex flex-column align-items-center" style="padding-top: 5vh; padding-bottom: 10vh">
                                    <div><i class="bx bx-info-circle text-muted" style="font-size: large"></i></div>
                                    <div class="fw-bold text-muted"><small>No Timelines</small></div>
                                </div>
                            </div>
                        </simplebar>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body mx-1">
                        <div class="d-flex flex-row justify-content-between mb-4">
                            <div><h5 style="color: #666; font-size: 14px">Report Files</h5></div>
                            <div></div>
                        </div>
                        <simplebar style="max-height: 35vh" id="my-timeline">
                            <div v-if="incidentFiles.length > 0">
                                <div role="button" class="" v-for="doc in incidentFiles" v-bind:key="'file__' + doc.id" @click.prevent="downloadrequestFile(doc)">
                                    <div class="d-flex gap-2 mb-3">
                                        <div>
                                            <div v-if="getExtension(doc.original_url) == 'xls' || getExtension(doc.original_url) == 'xlsx'">
                                                <ExcelIcon :height="'25px'" :width="'25px'" />
                                            </div>
                                            <div v-else-if="getExtension(doc.original_url) == 'doc' || getExtension(doc.original_url) == 'docx'">
                                                <WordIcon :height="'25px'" :width="'25px'" />
                                            </div>
                                            <div v-else-if="getExtension(doc.original_url) == 'pdf'">
                                                <PdfIcon :height="'25px'" :width="'25px'" />
                                            </div>
                                            <div
                                                v-else-if="
                                                    getExtension(doc.original_url) == 'png' ||
                                                    getExtension(doc.original_url) == 'jpg' ||
                                                    getExtension(doc.original_url) == 'jpeg' ||
                                                    getExtension(doc.original_url) == 'webp' ||
                                                    getExtension(doc.original_url) == 'gif' ||
                                                    getExtension(doc.original_url) == 'svg'
                                                "
                                                @click="showImage(doc.original_url)"
                                            >
                                                <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                                    <i class="fa fa-camera"></i>
                                                </div>
                                            </div>
                                            <div v-else-if="getExtension(doc.original_url) == 'wav'" @click="showAudioPlayer(doc.original_url)">
                                                <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                                    <i class="fa fa-microphone"></i>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                                    <i class="bx bx-file"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div>
                                                <div class="" style="font-size: 11px; color: #777777">
                                                    <p
                                                        v-if="
                                                            getExtension(doc.original_url) == 'png' ||
                                                            getExtension(doc.original_url) == 'jpg' ||
                                                            getExtension(doc.original_url) == 'jpeg' ||
                                                            getExtension(doc.original_url) == 'webp' ||
                                                            getExtension(doc.original_url) == 'gif' ||
                                                            getExtension(doc.original_url) == 'svg'
                                                        "
                                                        class="text mb-0 mt-0"
                                                        @click="showImage(doc.original_url)"
                                                    >
                                                        Incident Report Image
                                                    </p>
                                                    <p v-else-if="getExtension(doc.original_url) == 'wav'" class="text mb-0 mt-0" @click="showAudioPlayer(doc.original_url)">
                                                        Incident Report Recording
                                                    </p>

                                                    <p v-else class="text mb-0 mt-0">Incident Report Form</p>
                                                </div>
                                                <div class="text-muted">
                                                    <div style="font-size: 9px; color: #999999">
                                                        Uploaded by {{ props.incident.logs[0].reporter_name }} ,
                                                        {{ formatDate(doc.created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="d-flex flex-column align-items-center" style="padding-top: 5vh; padding-bottom: 10vh">
                                    <div><i class="bx bx-info-circle text-muted" style="font-size: large"></i></div>
                                    <div class="fw-bold text-muted"><small>No files</small></div>
                                </div>
                            </div>
                        </simplebar>
                    </div>
                </div>
            </div>
        </div>
        <vue-easy-lightbox :visible="state.showImages" :imgs="state.imageUrl" :index="0" @hide="onHide"></vue-easy-lightbox>
      

        <!-- modal to confirm the signed pettycash -->
        <b-modal v-model="state.showSignedPdf" size="lg" title="Confirm that your Signature is correctly positioned " title-class="font-18" centered hide-footer hide-title>
            <!-- Modal body -->
            <div class="" style="padding: ">
                <br />
                <div class="col-12">
                    <div>
                        <div>
                            <vue-pdf-app style="height: 80vh" theme="light" :pdf="state.systemSignedArrayBuffer" file-name="IncidentReportForm.pdf"></vue-pdf-app>
                        </div>
                    </div>
                </div>

                <div style="text-align: center" class="mt-3 mb-2">
                    <b-button variant="primary" type="button" class="unicef-btn unicef-primary w-md ml-1" @click="approveRequest">
                        <span>Proceed</span>
                    </b-button>
                </div>
            </div>
        </b-modal>

        <b-modal v-model="state.showAudioPlayerModal" size="lg" title="Incident Recording" title-class="font-18" centered hide-footer>
            <!-- Modal body -->
            <div class="d-flex align-items-center justify-content-center">
                <div>
                    <AudioPlayer
                        :option="{
                            src: state.audioUrl,
                            title: `Incident: ${props.incident.reference_no} Recording`,
                        }"
                    />
                </div>
            </div>
        </b-modal>

        <!-- return request to staff Modal -->
        <!-- <b-modal v-model="state.returnRequestToStaffModal" id="modal-center" centered title="Return Report" title-class="font-18" hide-footer>
            <div class="">
                <form id="return-to-staff-form" class="mt-4">
                    <div class="form-group mt-4">
                        <label>Reason: </label>
                        <div class="mb-3">
                            <textarea
                                name="reasons"
                                v-model="state.returnReason"
                                style="resize: none"
                                class="form-control"
                                placeholder="Reasons for Returning the Request"
                                rows="5"
                                required
                            ></textarea>
                        </div>
                    </div>
                    <div class="text-muted mb-4"><span class="font-weight-bold">Note: </span>This Reason will be shared with the originator, {{props.accident.timelines[0]?.timeline_by }}</div>
                    <div class="mb-4 form-group">
                        <label>Send To: </label>
                        <select name="returnTo" class="form-control" v-model="state.retrunTo" required>
                            <option selected disabled>Return and send to ...</option>
                            <option value="originator">{{  props.accident.timelines[0]?.timeline_by }} (Originator)</option>
                            <option value="transport_associate">{{  props.accident.timelines[1]?.timeline_by }} (Transport Associate)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="closeRequest" v-model="state.closeAfterReturn" :disabled="retrunTo == 'assigningAuthority' ? true : false" />
                                <label class="form-check-label text-muted" for="closeRequest"> Close upon returning it (disable resubmissions) </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <b-button variant="primary" :disabled="state.isProcessing" @click="returnToStaff" class="unicef-btn unicef-danger px-6">
                            <span><i v-if="state.isProcessing" class="fa fa-spinner fa-spin"></i> RETURN</span>
                        </b-button>
                    </div>
                </form>
            </div>
        </b-modal> -->
    </div>
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
    border: 2px dashed #3b86fe6a;
    background-color: #fff;
    transition: 0.3s ease all;
    label {
        padding: 8px 12px;
        border-radius: 4px;
        color: #fff;
        background-color: #3b86fe;
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
.pdf-app.light {
    --pdf-toolbar-color: #0160a0;
}
</style>
