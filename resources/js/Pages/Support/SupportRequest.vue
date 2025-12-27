<script setup>
import { onMounted, reactive } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import InstructionsModal from "./InstructionsModal.vue";
import HorizontalSupportLayout from "../../Layouts/HorizontalSupportLayout.vue";
import { VueEditor } from "vue3-editor";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import Swal from "sweetalert2";

const state = reactive({
    faqs: [
        {
            title: "Account and Profile",
            subTitle: "How to go about 'These Credentials Don't Match our Records' error.",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "The updated Invoice submission process.",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "How do I add my Service Provider's Vendor Number?",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "Video on How to submit Signed FACE forms to UNICEF.",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "How do I process FACE forms on the platform?",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "How do I submit Signed FACE forms to UNICEF?",
            active: false,
        },
        {
            title: "Account and Profile",
            subTitle: "I created an account on the platform but I haven’t received my Account Credentials.",
            active: false,
        },
        {
            title: "Account and Profile",
            subTitle: "How do I create an account on the Platform?",
            active: false,
        },
        {
            title: "General Support",
            subTitle: "Invoice Submission on the Platform: Why do I need to select the Type of Payment?",
            active: false,
        },
        {
            title: "Account and Profile",
            subTitle: "How to Login to the Platform",
            active: false,
        },
    ],
    showDetailModal: false,
    currentTitle: "",
    currentSubTile: "",
    currentIndex: 0,
    supportRequestModal: false,
    supportDescription: "",
    issueType: "",
    supportIssue: "",
    userName: "",
    userEmail: "",
    selectedSupportReportFile: [],
    selectedFileNames: [],
    showSubmissionLoader: false,
});

//mounted
onMounted(() => {
    //removing default blue bacground
    document.body.classList.remove("side-bg");
});

function showModal(index, faq) {
    state.currentTitle = faq.title;
    state.currentSubTile = faq.subTitle;
    state.currentIndex = index;
    state.showDetailModal = true;
}
function getSupportFiles(event) {
    state.selectedSupportReportFile.unshift(event.target.files[0]);
    state.selectedFileNames.unshift(event.target.files[0].name);
}

function deleteSelectedFile(index) {
    state.selectedSupportReportFile.splice(index, 1);
    state.selectedFileNames.splice(index, 1);
}
function getExtension(name) {
    // gets filename extension
    const regex = new RegExp("[^.]+$");
    const extension = name.match(regex);
    return extension;
}

function submitSupportRequest() {
    state.showSubmissionLoader = true;
    let formData = new FormData();

    state.selectedSupportReportFile.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    formData.append("detailedDescription", state.supportDescription);
    formData.append("issueType", state.issueType);
    formData.append("issue", state.supportIssue);
    formData.append("name", usePage().props.auth.user ? usePage().props.auth.user.name : state.userName);
    formData.append("email", usePage().props.auth.user ? usePage().props.auth.user.email : state.userEmail);

    const config = {
        headers: {
            "content-type": "multipart/form-data",
            //Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
        },
    };

    axios
        .post("/api/support/submitRequest", formData, config)
        .then((res) => {
            console.log(res);
            if (res.status == 200) {
                state.supportRequestModal = false;
                state.showSubmissionLoader = false;
                Swal.fire({
                    html: "<p>Support Request Submitted</p>",
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
                    state.supportDescription = "";
                    state.issueType = "";
                    state.supportIssue = "";
                    state.userName = "";
                    state.userEmail = "";
                    state.selectedSupportReportFile = [];
                    state.selectedFileNames = [];
                    state.showSubmissionLoader = false;
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
                    state.supportDescription = "";
                    state.issueType = "";
                    state.supportIssue = "";
                    state.userName = "";
                    state.userEmail = "";
                    state.selectedSupportReportFile = [];
                    state.selectedFileNames = [];
                    state.showSubmissionLoader = false;

                    Swal.close();
                }
            });
        });
}
</script>
<template>
    <Head title="Support" />
    <div>
        <HorizontalSupportLayout>
            <h2 class="mb-3 text-center">Need Some Help?</h2>
            <div class="text-center">Begin by going over the Frequently Asked Questions below, or send us a Support Request</div>
            <div class="mt-5 ps-2 pe-2 position-relative">
                <div role="button" class="card mb-4" v-for="(faq, index) in state.faqs" :key="index">
                    <div class="card-body" role="button" @mouseenter="faq.active = true" @mouseleave="faq.active = false" @click="showModal(index, faq)">
                        <div :class="faq.active ? 'text-primary' : ''">
                            <h5>{{ faq.title }}</h5>
                        </div>
                        <div>{{ faq.subTitle }}</div>
                    </div>
                </div>
            </div>
            <InstructionsModal
                :subTitle="state.currentSubTile"
                :showModal="state.showDetailModal"
                :title="state.currentTitle"
                :faqIndex="state.currentIndex"
                @closeClicked="state.showDetailModal = false"
            />
        </HorizontalSupportLayout>

        <div class="d-flex justify-content-end mb-5" style="width: 96%; position: fixed; bottom: 0px; margin-left: auto">
            <div role="button" class="avatar-lg rounded-circle bg-success text-white d-flex flex-column justify-content-center align-items-center" style="" @click="state.supportRequestModal = true">
                <div>
                    <i class="bx bx-support" style="font-size: large"></i>
                </div>
                <div class="">Write to Us</div>
            </div>
        </div>
        <b-modal v-model="state.supportRequestModal" size="lg" title="WHAT DIFFICULTY ARE YOU FACING?" title-class="font-18" centered hide-footer hide-title>
            <div class="mb-4 mt-3">
                <label>What is this Issue About</label>
                <select class="form-control" id="request_type" v-model="state.issueType">
                    <option value="" disabled>Select</option>
                    <option value="Account and Profile">Account and Profile</option>
                    <option value="General Support">General Support</option>
                </select>
            </div>
            <div v-if="!usePage().props.auth.user">
                <div class="mb-4">
                    <label>Your Name:</label>
                    <input class="form-control" type="text" v-model="state.userName" />
                </div>

                <div class="mb-4">
                    <label>Email:</label>
                    <input class="form-control" type="email" v-model="state.userEmail" />
                </div>
            </div>
            <div class="mb-4">
                <label>Issue</label>
                <textarea class="form-control" :maxlength="100" rows="1" placeholder="" v-model="state.supportIssue"></textarea>
            </div>
            <div class="mb-4">
                <label for="request_description">Detailed Description</label>
                <VueEditor v-model="state.supportDescription" id="request_description"></VueEditor>
            </div>
            <div class="mb-5">
                <label>Attach any relevant documents</label>

                <div @dragenter.prevent="toggleActive" @dragleave.prevent="toggleActive" @dragover.prevent @drop.prevent="toggleActive" :class="{ 'active-dropzone': state.active }" class="dropzone">
                    <div class="mx-auto">
                        <i class="bx bxs-cloud-upload" style="font-size: 5em; color: #b5b5b5"></i>
                    </div>
                    <div class="fw-bold text-muted text-center">DRAG & DROP</div>
                    <label for="dropzoneFile">Select Files</label>
                    <input type="file" id="dropzoneFile" class="dropzoneFile btn btn-primary" multiple @change="getSupportFiles" />
                </div>
            </div>
            <div v-if="state.selectedFileNames.length > 0" class="mb-5">
                <div role="button" class="" v-for="(doc, index) in state.selectedFileNames" v-bind:key="'file__' + doc + index">
                    <div class="d-flex gap-2 mb-3 align-items-center justify-content-between col-6">
                        <div class="d-flex gap-3 align-items-center">
                            <div>
                                <div v-if="getExtension(doc) == 'xls' || getExtension(doc) == 'xlsx'">
                                    <ExcelIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div v-else-if="getExtension(doc) == 'doc' || getExtension(doc) == 'docx'">
                                    <WordIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div v-else-if="getExtension(doc) == 'pdf'">
                                    <PdfIcon :height="'33px'" :width="'33px'" />
                                </div>
                                <div
                                    v-else-if="
                                        getExtension(doc) == 'png' ||
                                        getExtension(doc) == 'jpg' ||
                                        getExtension(doc) == 'jpeg' ||
                                        getExtension(doc) == 'webp' ||
                                        getExtension(doc) == 'gif' ||
                                        getExtension(doc) == 'svg'
                                    "
                                >
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="fa fa-camera" style="font-size: x-large"></i>
                                    </div>
                                </div>
                                <div v-else-if="getExtension(doc) == 'wav'">
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="fa fa-microphone" style="font-size: x-large"></i>
                                    </div>
                                </div>
                                <div v-else>
                                    <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                        <i class="bx bx-file" style="font-size: x-large"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <div>
                                    <div class="" style="font-size: 13px; color: #777777">
                                        <p class="text mb-0 mt-0">{{ doc }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <i class="bx bxs-x-circle text-danger fs-5 mt-2 ms-5" role="button" @click="deleteSelectedFile(index)"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-3">
                <b-button variant="primary" @click="submitSupportRequest" :disabled="state.showSubmissionLoader">
                    <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="state.showSubmissionLoader"></i>Submit Request
                </b-button>
            </div>
        </b-modal>
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
