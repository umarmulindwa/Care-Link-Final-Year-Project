<script setup>
import Layout from "../../Layouts/verticalmain.vue";
import PageHeader from "@/Components/page-header.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { onMounted, reactive } from "vue";
import moment from "moment";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import SignatureRegister from "../SignatureRegister/SignatureRegister.vue";
import NewStaffApprovepdf from "./PdfForms/NewStaffApprovepdf.vue";
import Swal from "sweetalert2";
import PDFMerger from "pdf-merger-js";
import html2pdf from "html2pdf.js";
import { useStore } from "vuex";
import VuePdfApp from "vue3-pdf-app";

onMounted(() => {
    //removing default blue background
    document.body.classList.remove("side-bg");

    //getting user signature
    getUserSignature();

    //get file to append
    getFileToAppend(props.request?.files[0]);
});
const store = useStore();
//pdf merger
let merger = new PDFMerger();

const props = defineProps({
    request: Object,
    user: Object,
});

const state = reactive({
    savedSignature: null,
    isLoading: false,
    gotoNextStep: false,
    iWillSign: 0,
    fileToAppend: null,
    fileToAppendBlob: null,
    systemSignedArrayBuffer: null,
    systemSignedBlob: null,
    signedFile: null,
    downloadOfflineModal: false,
    confirmSignedModal: false,
    approved:false,
    isMerged: false,
});

//Default Layout
defineOptions({ layout: Layout });

function formatDate2(date) {
    return moment(date).format("MMMM Do YYYY [at] h:mm a");
}

function getFileToAppend(file) {
    const onboardingUrl = import.meta.env.VITE_STAFFONBOARDING_URL;

    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
        responseType: "blob",
    };

    let formdata = new FormData();

    formdata.append("file_path", file?.filename);

    axios
        .post(`${onboardingUrl}/api/downloadFIle`, formdata, options)
        .then((response) => {
            state.fileToAppendBlob = response.data;
        })
        .catch((error) => {
            console.log(error);
            Swal.fire({
                title: "Something Went Wrong",
                icon: "error",
                html: `<p style="font-size: 14px">Could not fetch form. Please, reach out to ICT for help.</p>`,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            });
        });
}

function downloadrequestFile() {
    const href = window.URL.createObjectURL(state.fileToAppendBlob);

    const anchorElement = document.createElement("a");

    anchorElement.href = href;
    anchorElement.download = "staff Onboarding Form";

    document.body.appendChild(anchorElement);
    anchorElement.click();

    document.body.removeChild(anchorElement);
    window.URL.revokeObjectURL(href);
}
async function getUserSignature() {
    const options = {
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };
    state.isLoading = true;
    await axios
        .get(`/api/staff-register/get-userSignature`, options)
        .then((res) => {
            if (res.data.results != null) {
                state.savedSignature = res.data.results?.image;
            }
            state.isLoading = false;
        })
        .catch((error) => {
            console.log(error);
            state.isLoading = false;
        });
}

//this only accepts system signing
function makeSignature() {
    state.chiedofOperationsModal = false;
    Swal.fire({
        title: "Proceed with Approval",
        icon: "info",
        html: `<div style="font-size: 14px">
                                    You are about to proceed with approving this request.<br/>
                                    A PDF form evidencing this approval will be automatically signed by the system (on your behalf).
                                        </div>
                                    `,
        // input: "checkbox",
        // inputPlaceholder: "<div style='font-size: 14px'>I want to sign the PDF Form myself</div>",
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
        if (result.isConfirmed) {
            store.commit("LoggedInUser/userSigns", state.iWillSign);

            generateFile();
        }
    });
}

function showDownloadOffline() {
    Swal.close();
    state.downloadOfflineModal = true;
}

function saveUploadedSignedForm() {
    document.getElementById("offlineSignedEquipmentReq").click();
}

//generating pdf for the form
async function generateFile() {
    state.itemsModal = false;
    Swal.fire({
        title: "Please wait...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    state.isProcessing = true;
    let name = "StaffOnBoardingFile.pdf";

    const element = document.getElementById("onboardedStaffequipmentPdfForm");

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
            scale: 6,
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
            const blobMerge = state.fileToAppendBlob;
            const fileToMerge = new File([blobMerge], "filetoAppend.pdf", { type: "application/pdf" });
              if (state.isMerged == true) {
                // reset merger to avoid duplicates
                merger.reset();
            }
            await merger.add(fileToMerge);
            await merger.add(file);
            state.isMerged = true

            //generating form array buffer that will be used in pdftron and pdfvueApp
            state.systemSignedArrayBuffer = await merger.saveAsBuffer();

            if (state.iWillSign != 1) {
                state.systemSignedBlob = await merger.saveAsBlob();

                let mergedfile = new File([state.systemSignedBlob], name, {
                    type: "application/pdf",
                    lastModified: new Date(),
                });

                state.signedFile = mergedfile;
                //show signed pdf modal
                state.confirmSignedModal = true;
            }

            Swal.close();
        });
}
function offlineSignedUploadedFile(e) {
    state.signedFile = e.target.files[0];
    state.downloadOfflineModal = false;
    //approve request
    approveRequest();
}

function downloadOfflineSigining() {
    const element = document.getElementById("onboardedStaffequipmentPdfForm");
    Swal.fire({
        title: "Downloading...",
        allowOutsideClick: false,
        showCancelButton: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    let options = {
        margin: 0.5,
        filename: "StaffOnboarding.pdf",
        pagebreak: {
            after: "#break",
        },
        image: {
            type: "jpeg",
            quality: 1.0,
        },
        html2canvas: {
            scale: 6,
            useCORS: true,
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

            let file = new File([blob], "StaffOnboarding.pdf", {
                type: "application/pdf",
                lastModified: new Date(),
            });
            const blobMerge = state.fileToAppendBlob;
            const fileToMerge = new File([blobMerge], "filetoAppend.pdf", { type: "application/pdf" });
             if (state.isMerged == true) {
                // reset merger to avoid duplicates
                merger.reset();
            }
            await merger.add(fileToMerge);
            await merger.add(file);
            state.isMerged =true;
            await merger.save("StaffOnboardingRequest.pdf");
            Swal.close();
        });
}
function approveRequest() {
    state.confirmSignedModal = false;
    if (state.signedFile != null) {

    const onboardingUrl = import.meta.env.VITE_STAFFONBOARDING_URL;

    const options = {
        "content-type": "multipart/form-data",
        headers: { Authorization: `Bearer ${usePage().props.auth.user.api_token}` },
    };

    let formdata = new FormData();

    formdata.append("ReqId",  props.request.id);
    formdata.append("File",  state.signedFile);



    axios
        .post(`${onboardingUrl}/api/staffApproveForm`, formdata, options)
        .then((response) => {
            state.approved = true
              Swal.fire({
                title: "Approved Successfully",
                icon: "success",
                html: `<p style="font-size: 14px">You have successfully approved the request.</p>`,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            });

        })
        .catch((error) => {
            console.log(error);
            Swal.fire({
                title: "Something Went Wrong",
                icon: "error",
                html: `<p style="font-size: 14px">There is something wrong that has happened. Please, reach out to ICT for help.</p>`,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#43ad60",
                allowOutsideClick: false,
                allowEscapeKey: false,
                closeOnClickOutside: false,
            });
        })

    } else {
        Swal.fire({
            title: "Something Went Wrong",
            icon: "error",
            html: `<p style="font-size: 14px">Signed Form not recognized. Please, reach out to ICT for help.</p>`,
            showCloseButton: false,
            showCancelButton: false,
            focusConfirm: true,
            confirmButtonText: "OK",
            confirmButtonColor: "#43ad60",
            allowOutsideClick: false,
            allowEscapeKey: false,
            closeOnClickOutside: false,
        });
    }
}
</script>
<template>
    <Head title="Staff Onboarding" />

    <PageHeader :title="'Staff Onboarding'" :items="items" />

    <div class="row">
        <!--end col-->
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div v-if="state.gotoNextStep">
                        <div class="table-responsive mb-5">
                            <table class="table table-bordered mx-0">
                                <tbody>
                                    <tr>
                                        <th>Title</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">{{ props.request.title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contract Ref #</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">{{ props.request.contract_ref }}</td>
                                    </tr>

                                    <tr>
                                        <th>Contract Type</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">{{ props.request.type_of_contract }}</td>
                                    </tr>

                                    <tr>
                                        <th>Section</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">{{ props.request.section }}</td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">{{ props.request.email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive mb-5" v-if="props.request.IctServicesCount > 0">
                            <table class="table table-bordered mx-0">
                                <tbody>
                                    <tr>
                                        <th>ICT Services</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                            <span class="me-4" v-for="(service, index) in JSON.parse(props.request.IctServicesValues)" :key="`${index}`">
                                                {{ service }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive mb-5" v-if="props.request.IctEquipmentCount > 0">
                            <table class="table table-bordered mx-0">
                                <tbody>
                                    <tr>
                                        <th>ICT Equipment</th>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                            <span class="me-4" v-for="(equipment, index) in JSON.parse(props.request.IctEquipmentValues)" :key="`${index}`">
                                                {{ equipment }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-if="props.request.items.length > 0">
                            <table class="table table-bordered mx-0 mb-5">
                                <tbody>
                                    <tr>
                                        <th>Item Description</th>
                                        <th>Serial Number</th>
                                        <th>Modal</th>
                                    </tr>
                                    <tr v-for="(item, key) in props.request.items" :key="key">
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                            {{ item.item_description }}
                                        </td>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                            {{ item.serial_number }}
                                        </td>
                                        <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                            {{ item.model }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mb-4">
                        <div v-if="props.request.status == 'Approved' || state.approved == true">Request Approved</div>
                            <b-button v-else variant="primary pe-4 ps-4" @click="makeSignature">Approve</b-button>
                        </div>
                        <NewStaffApprovepdf :requestDetails="props.request" :signature="state.savedSignature" :i-will-sign="state.iWillSign" class="visually-hidden" />
                    </div>
                    <div v-else class="mb-4">
                        <div class="col-md-12 mb-4">
                            <div class="mb-5">
                                <p><span class="fw-medium">Welcome</span> to the unicef Platform</p>
                                <p>The UNICEF ICT team has assigned to you some equipment.</p>
                                <p>To eventually receive the equipment, you are required to complete the steps below.</p>
                            </div>
                            <div class="table-responsive mb-5">
                                <table class="table table-bordered mx-0 mb-5">
                                    <tbody>
                                        <tr>
                                            <th>STEP 1</th>
                                            <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                                <div class="d-flex flex-row justify-content-between align-items-center">
                                                    <span span class="fw-medium">Add your Signature</span>
                                                    <span v-if="state.savedSignature != null"><i class="bx bx-check-square text-success"></i></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>STEP 2</th>
                                            <td class="border-end border-1 border-opacity-10 border-secondary" style="background-color: #fff">
                                                <div><span span class="fw-medium">Sign Requisition to confirm</span></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    <!--  -->
                                    <b-button v-if="state.savedSignature == null" variant="primary pe-4 ps-4" data-bs-toggle="modal" data-bs-target="#signatureRegister" :disabled="state.isLoading"
                                        ><span v-if="state.isLoading"><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i></span>Get Started</b-button
                                    >
                                    <b-button v-else variant="primary pe-4 ps-4" @click="state.gotoNextStep = true">Proceed to Step 2</b-button>
                                </div>
                                <!-- signature register modal -->
                                <SignatureRegister @signatureSaved="getUserSignature" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <NewStaffApprovepdf :requestDetails="props.request" :signature="state.savedSignature" :i-will-sign="state.iWillSign" class="visually-hidden" />
        </div>
        <!--end col-->
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body mx-1">
                    <div class="d-flex flex-row justify-content-between mb-4">
                        <div><h5 style="color: #666; font-size: 14px">Timeline</h5></div>
                    </div>

                    <div id="" style="overflow: auto; height: 35vh" class="customscrollbar">
                        <div>
                            <ul class="verti-timeline list-unstyled" v-for="time in props.request?.timeline" v-bind:key="'timeline__' + time.id">
                                <li class="event-list">
                                    <div class="event-timeline-dot text-center">
                                        <i :class="time.icon" class="" :style="{ color: time.color, fontSize: '15px' }"></i>
                                    </div>
                                    <div class="d-flex pb-3">
                                        <div class="flex-grow-1">
                                            <div>
                                                <div class="" style="font-size: 11px; color: #777777">{{ time.title }}</div>
                                                <div class="text-muted">
                                                    <div style="font-size: 9px; color: #999999">{{ formatDate2(time.created_at) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body mx-1">
                    <div class="d-flex flex-row justify-content-between mb-4">
                        <div><h5 style="color: #666; font-size: 14px">File Downloads</h5></div>
                        <div></div>
                    </div>

                    <div id="" style="overflow: auto; height: 35vh" class="customscrollbar">
                        <div>
                            <div role="button" class="" v-for="doc in props.request?.files" v-bind:key="'file__' + doc.id" @click.prevent="downloadrequestFile()">
                                <div class="d-flex gap-2 mb-3">
                                    <div>
                                        <div v-if="doc.filename.endsWith('xls') || doc.filename.endsWith('xlsx')">
                                            <ExcelIcon :height="'33px'" :width="'33px'" />
                                        </div>
                                        <div v-else-if="doc.filename.endsWith('doc') || doc.filename.endsWith('docx')">
                                            <WordIcon :height="'33px'" :width="'33px'" />
                                        </div>
                                        <div v-else-if="doc.filename.endsWith('pdf')">
                                            <PdfIcon :height="'33px'" :width="'33px'" />
                                        </div>
                                    </div>
                                    <div class="">
                                        <div>
                                            <div class="" style="font-size: 11px; color: #777777">{{ doc.title }}</div>
                                            <div class="text-muted">
                                                <div style="font-size: 9px; color: #999999">{{ formatDate2(doc.created_at) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                        <div @click="downloadOfflineSigining" style="" class="text-primary" role="button">Click to Download</div>
                                        <small class="text-muted">StaffOnboardingForm.pdf</small> <br />
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
                                        <input id="offlineSignedEquipmentReq" type="file" @change="offlineSignedUploadedFile" ref="offlineSignedEquipmentReqCash" accept="application/pdf" hidden />

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
            <!-- modal to confirm the Approved form -->
            <b-modal v-model="state.confirmSignedModal" size="lg" title="Confirm that your Signature is correctly positioned (on all documents)" title-class="font-18" centered hide-footer hide-title>
                <!-- Modal body -->
                <div class="" style="">
                    <div v-if="state.iWillSign != 1" class="small text-muted text-center">Before proceeding, verify that the signed document has your signature in the correct location.</div>
                    <br />
                    <div class="col-12">
                        <div v-if="state.systemSignedArrayBuffer != null">
                            <vue-pdf-app style="height: 80vh" theme="light" :pdf="state.systemSignedArrayBuffer" file-name="PettyCashForm.pdf"></vue-pdf-app>
                        </div>
                        <div v-else>Something went wrong.</div>
                    </div>

                    <div style="text-align: center" class="mt-3 mb-2">
                        <b-button v-if="state.iWillSign == 0" variant="primary" type="button" class="unicef-btn unicef-primary w-md ml-1" @click="approveRequest">
                            <span>Proceed</span>
                        </b-button>
                    </div>
                </div>
            </b-modal>
        </div>
    </div>
</template>
<style scoped lang="scss">
/* pdf viewer customisation */

.pdf-app.light {
    --pdf-toolbar-color: #0160a0;
}

.image {
    background: #34495e;
    border: 1px solid #34495e;
    width: 100px;
    height: 100px;
}

.image-contain {
    object-fit: contain;
    object-position: center;
}

.image-cover {
    object-fit: cover;
    object-position: right top;
}

.dropzone {
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
