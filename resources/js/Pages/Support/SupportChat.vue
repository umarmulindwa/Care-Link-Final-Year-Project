<script>
import simplebar from "simplebar-vue";
import { chatData, chatMessagesData } from "./data";
import VueEasyLightbox from "vue-easy-lightbox";
import avatar1 from "../../../images/users/avatar-1.jpg";
import { usePage, router } from "@inertiajs/vue3";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import moment from "moment";
import VuePdfApp from "vue3-pdf-app";
import "vue3-pdf-app/dist/icons/main.css";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    components: {
        simplebar,
        PdfIcon,
        ExcelIcon,
        WordIcon,
        VuePdfApp,
        VueEasyLightbox,
    },
    data() {
        return {
            avatar1,
            chatMessagesData: [],
            title: "Chat",
            items: [
                {
                    text: "Skote",
                    href: "javascript:void(0)",
                },
                {
                    text: "Chat",
                    active: true,
                },
            ],
            submitted: false,
            form: {
                message: "",
            },
            username: "Steven Franklin",
            currentIssue: "",
            viewFileModal: false,
            viewPdfFile: null,
            showSingleImage: false,
            responseMessage: "",
            responseTitle: "",
            supportResponseFiles: [],
            tempResFiles: [],
            submitResponseLoader: false,
            responseIsSubmited: false,
            isReversed: false,
            threadId: null,
            currentEmail: "",
        };
    },
    props: {
        allRequests: {
            type: Array,
            default: [],
        },
        generalRequests: {
            type: Array,
            default: [],
        },
        accountRequests: {
            type: Array,
            default: [],
        },
    },
    mounted() {
        let firstThread = this.allRequests[0];
        var container = document.querySelector("#my-element .simplebar-content-wrapper");
        container.scrollTo({ top: 500, behavior: "smooth" });

        var container2 = document.querySelector("#containerElement .simplebar-content-wrapper");
        container2.scrollTo({ top: 500, behavior: "smooth" });
        document.body.classList.remove("side-bg");

        //scroll the chat to the bottom
        setTimeout(() => {
            var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
            var scrollHeight = document.getElementById("containerElement").scrollHeight;
            Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
        }, 200);

        //clicking the first thread
        document.getElementById(`${firstThread.id}_ ${firstThread.name}`).click();
        //revese charts
        this.chatMessagesData = this.chatMessagesData.reverse();
    },
    computed: {
        currentUser: () => {
            return usePage().props.auth.user;
        },
        reversedChatArray: () => {
            const rev = chatMessagesData.reverse();
            return rev;
        },
    },
    methods: {
        //from now date
        fromNow(data) {
            return moment(data).calendar();
        },
        rearrage() {
            this.chatMessagesData = this.chatMessagesData.reverse();
        },
        /**
         * Get the name of user
         */
        chatUsername(email) {
            const app = this;
            app.chatMessagesData = [];
            //getting the chat thread
            const filteredArray = app.allRequests.filter((req) => req.email === email);
            const chatThread = filteredArray[0];

            app.username = chatThread.name;
            app.currentEmail = chatThread.email;
            app.currentIssue = chatThread.issue;
            app.threadId = chatThread.id;

            //push responses from the super admin for the latest message
            chatThread.responses.forEach((res) => {
                app.chatMessagesData.push({
                    align: "right",
                    name: res.name,
                    message: res.body,
                    issue: res.title,
                    files: res.ReponseFiles,
                    time: moment(res.created_at).calendar(),
                });
            });

            //push the latest message from the requestor
            app.chatMessagesData.push({
                align: "left",
                name: chatThread.name,
                message: chatThread.description,
                issue: chatThread.issue,
                files: chatThread.files,
                time: moment(chatThread.created_at).calendar(),
            });

            //do the same for all old chats in the same thread
            if (chatThread.hasOwnProperty("requestThread")) {
                chatThread.requestThread.forEach((chat) => {
                    //push responses from the super admin for the latest message
                    chat.responses.forEach((res) => {
                        app.chatMessagesData.push({
                            align: "right",
                            name: res.name,
                            message: res.body,
                            issue: res.title,
                            files: res.ReponseFiles,
                            time: moment(res.created_at).calendar(),
                        });
                    });

                    //push the latest message from the requestor
                    app.chatMessagesData.push({
                        align: "left",
                        name: chat.name,
                        message: chat.description,
                        issue: chat.issue,
                        files: chat.files,
                        time: moment(chat.created_at).calendar(),
                    });
                });
            }
            app.chatMessagesData = app.chatMessagesData.reverse();

            //scroll chat to the bottom
            setTimeout(() => {
                var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                var scrollHeight = document.getElementById("containerElement").scrollHeight;
                Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
            }, 200);
        },

        submitResponse() {
            //push responses from the super admin for the latest message
            const app = this;
            const el = document.getElementById("lastResponseMessage");
            const config = {
                headers: {
                    "content-type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };
            const formData = new FormData();

            app.chatMessagesData.push({
                align: "right",
                name: usePage().props.auth.user.name,
                message: app.responseMessage,
                issue: app.responseTitle,
                files: app.tempResFiles,
                time: moment().calendar(),
            });

            app.submitResponseLoader = true;

            app.supportResponseFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file.file);
            });
            formData.append("title", app.responseTitle);
            formData.append("response", app.responseMessage);
            formData.append("support_request_id", app.threadId);
            formData.append("email", app.username);
            formData.append("name", app.currentEmail);

            axios
                .post("/api/support/submitResponse", formData, config)
                .then((res) => {
                    console.log(res);
                    if (res.status == 200) {
                        app.submitResponseLoader = false;
                        app.responseIsSubmited = true;
                    }
                })
                .catch((error) => {
                    console.log({ error });
                    app.submitResponseLoader = false;
                    app.responseIsSubmited = false;

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
                            app.responseMessage = "";
                            app.responseTitle = "";

                            Swal.close();
                        }
                    });
                });

            app.responseMessage = "";
            app.responseTitle = "";
            app.supportResponseFiles = [];
            //scroll to last message
            setTimeout(() => {
                var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                var scrollHeight = document.getElementById("containerElement").scrollHeight;
                Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
            }, 200);
        },

        /**
         * Char form Submit
         */
        // eslint-disable-next-line no-unused-vars
        formSubmit(e) {
            this.submitted = true;

            // stop here if form is invalid

            const message = this.form.message;
            const currentDate = new Date();
            this.chatMessagesData.push({
                align: "right",
                name: `${usePage().props.auth.user.name}`,
                message,
                time: currentDate.getHours() + ":" + currentDate.getMinutes(),
            });
            this.handleScroll();

            this.submitted = false;
            this.form = {};
        },

        handleScroll() {
            if (this.$refs.current.$el) {
                setTimeout(() => {
                    var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                    var scrollHeight = document.getElementById("containerElement").scrollHeight;
                    Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
                }, 500);
            }
        },

        getExtension(name) {
            // gets filename extension
            const regex = new RegExp("[^.]+$");
            const extension = name.match(regex);
            return extension;
        },

        pdfUrl(url) {
            this.viewPdfFile = url;
            this.viewFileModal = true;
        },
        addSupportReponseFile() {
            document.getElementById("supportResponseFile").click();
        },
        onChange(e) {
            this.supportResponseFiles.push({ file: e.target.files[0], name: e.target.files[0].name });
            this.tempResFiles.push({ filepath: e.target.files[0].name });
        },
        deleteResponseFile(index) {
            this.supportResponseFiles.splice(index, 1);
        },
        closeSupportThread() {
            //it is always the currently seen chat thread that will be closed
            const app = this;

            console.log("thisss", app.currentEmail);
            let formData = new FormData();

            formData.append("email", app.currentEmail);

            const config = {
                headers: {
                    "content-type": "multipart/form-data",
                    Authorization: `Bearer ${usePage().props.auth.user.api_token}`,
                },
            };

            axios
                .post("/api/support/closeSupportRequest", formData, config)
                .then((res) => {
                    console.log(res);
                    if (res.status == 200) {
                        Swal.fire({
                            title: "Support Request Closed",
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
                            router.visit("supportCenter");
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
        },
    },
};
</script>
<template>
    <div class="d-lg-flex">
        <div class="chat-leftsidebar me-lg-4">
            <div class="">
                <div class="py-4 border-bottom mb-4">
                    <div class="d-flex">
                        <div class="align-self-center me-3">
                            <img :src="currentUser.profile_photo_url" class="avatar-xs rounded-circle" alt="" />
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="font-size-15 mt-0 mb-1">{{ currentUser.name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="mdi mdi-circle text-success align-middle me-1"></i>
                                Active
                            </p>
                        </div>
                    </div>
                </div>

                <div class="chat-leftsidebar-nav">
                    <b-tabs pills fill content-class="py-4">
                        <b-tab title="Tab 1" active>
                            <template v-slot:title>
                                <div>
                                    <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                    <span class="d-none d-sm-block">All</span>
                                </div>
                            </template>
                            <b-card-text>
                                <div>
                                    <h5 class="font-size-14 mb-3">Recent</h5>
                                    <simplebar style="max-height: 410px" id="my-element">
                                        <ul class="list-unstyled chat-list">
                                            <li class v-for="data of allRequests" :key="`${data.id}_ ${data.name}`" @click="chatUsername(data.email)" :class="{ active: username == data.name }">
                                                <a href="javascript: void(0);" :id="`${data.id}_ ${data.name}`">
                                                    <div class="d-flex">
                                                        <!-- <div class="align-self-center me-3">
                                                            <i :class="`mdi mdi-circle text-${data.color} font-size-10`"></i>
                                                        </div> -->

                                                        <div class="avatar-xs align-self-center me-3">
                                                            <span class="avatar-title rounded-circle bg-soft bg-primary text-primary">{{ data.name.charAt(0) }}</span>
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <h5 class="text-truncate font-size-14 mb-1">
                                                                {{ data.name }}
                                                            </h5>
                                                            <p class="text-truncate mb-0">
                                                                {{ data.issue }}
                                                            </p>
                                                        </div>
                                                        <div class="font-size-11">{{ fromNow(data.created_at) }}</div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </simplebar>
                                </div>
                            </b-card-text>
                        </b-tab>
                        <b-tab>
                            <template v-slot:title>
                                <i class="bx bx-group font-size-20 d-sm-none"></i>
                                <span class="d-none d-sm-block">General Support</span>
                            </template>
                            <b-card-text>
                                <div>
                                    <simplebar style="max-height: 410px" id="my-element">
                                        <ul class="list-unstyled chat-list">
                                            <li class v-for="data of generalRequests" :key="`${data.id}_ ${data.name}`" @click="chatUsername(data.email)" :class="{ active: username == data.name }">
                                                <a href="javascript: void(0);">
                                                    <div class="d-flex">
                                                        <!-- <div class="align-self-center me-3">
                                                            <i :class="`mdi mdi-circle text-${data.color} font-size-10`"></i>
                                                        </div> -->

                                                        <div class="avatar-xs align-self-center me-3">
                                                            <span class="avatar-title rounded-circle bg-soft bg-primary text-primary">{{ data.name.charAt(0) }}</span>
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <h5 class="text-truncate font-size-14 mb-1">
                                                                {{ data.name }}
                                                            </h5>
                                                            <p class="text-truncate mb-0">
                                                                {{ data.issue }}
                                                            </p>
                                                        </div>
                                                        <div class="font-size-11">{{ fromNow(data.created_at) }}</div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </simplebar>
                                </div>
                            </b-card-text>
                        </b-tab>
                        <b-tab>
                            <template v-slot:title>
                                <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                <span class="d-none d-sm-block">Account & Profile</span>
                            </template>
                            <b-card-text>
                                <div>
                                    <simplebar style="max-height: 410px" id="my-element">
                                        <ul class="list-unstyled chat-list">
                                            <li class v-for="data of accountRequests" :key="`${data.id}_ ${data.name}`" @click="chatUsername(data.email)" :class="{ active: username == data.name }">
                                                <a href="javascript: void(0);">
                                                    <div class="d-flex">
                                                        <!-- <div class="align-self-center me-3">
                                                            <i :class="`mdi mdi-circle text-${data.color} font-size-10`"></i>
                                                        </div> -->

                                                        <div class="avatar-xs align-self-center me-3">
                                                            <span class="avatar-title rounded-circle bg-soft bg-primary text-primary">{{ data.name.charAt(0) }}</span>
                                                        </div>
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <h5 class="text-truncate font-size-14 mb-1">
                                                                {{ data.name }}
                                                            </h5>
                                                            <p class="text-truncate mb-0">
                                                                {{ data.issue }}
                                                            </p>
                                                        </div>
                                                        <div class="font-size-11">{{ fromNow(data.created_at) }}</div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </simplebar>
                                </div>
                            </b-card-text>
                        </b-tab>
                    </b-tabs>
                </div>
            </div>
        </div>
        <div class="w-100 user-chat">
            <div class="card">
                <div class="p-4 border-bottom">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="col-7">
                            <h5 class="font-size-15 mb-1">{{ username }}</h5>
                            <p class="text-muted mb-0">Issue: {{ currentIssue }}</p>
                        </div>
                        <div class="col-2">
                            <ul class="list-inline user-chat-nav text-end mb-0">
                                <li class="list-inline-item d-none d-sm-inline-block">
                                    <b-dropdown toggle-class="nav-btn" menu-class="dropdown-menu-end" right variant="white">
                                        <template v-slot:button-content>
                                            <i class="bx bx-cog"></i>
                                        </template>
                                        <b-dropdown-item @click="rearrage">Rearrange</b-dropdown-item>
                                        <b-dropdown-item @click="closeSupportThread">Mark as Closed</b-dropdown-item>
                                    </b-dropdown>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="chat-users">
                    <div class="chat-conversation p-3">
                        <simplebar style="max-height: 400px; min-height: 470px" id="containerElement" ref="current">
                            <ul class="list-unstyled">
                                <li v-for="data of chatMessagesData" :key="data.message" :class="{ right: `${data.align}` === 'right' }">
                                    <div class="conversation-list">
                                        <div class="ctext-wrap">
                                            <div class="mb-3">
                                                <div class="text-primary fw-bold">{{ data.name }}</div>
                                                <div>
                                                    <small class="text-muted">Issue: {{ data.issue }}</small>
                                                </div>
                                            </div>

                                            <div v-html="data.message"></div>
                                            <div class="d-flex justify-content-between align-items-center mt-4">
                                                <div class="me-4">
                                                    <p class="chat-time mb-0">
                                                        <i class="bx bx-time-five align-middle me-1"></i>
                                                        <small>{{ data.time }}</small>
                                                    </p>
                                                </div>
                                                <div class="d-flex flex-row gap-2" v-if="data.files.length > 0">
                                                    <div v-for="(file, index) in data.files" :key="index">
                                                        <div
                                                            @click="pdfUrl(file.filepath)"
                                                            role="button"
                                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                            style="background-color: #b4d2ff4e"
                                                            v-if="getExtension(file.filepath) == 'pdf'"
                                                        >
                                                            <PdfIcon :height="'20px'" :width="'20px'" />
                                                        </div>
                                                        <div
                                                            v-else-if="getExtension(file.filepath) == 'xls' || getExtension(file.filepath) == 'xlsx'"
                                                            role="button"
                                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                            style="background-color: #b4d2ff4e"
                                                        >
                                                            <ExcelIcon :height="'20px'" :width="'20px'" />
                                                        </div>
                                                        <div
                                                            v-else-if="getExtension(file.filepath) == 'doc' || getExtension(file.filepath) == 'docx'"
                                                            role="button"
                                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                            style="background-color: #b4d2ff4e"
                                                        >
                                                            <WordIcon :height="'20px'" :width="'20px'" />
                                                        </div>
                                                        <div
                                                            v-else-if="
                                                                getExtension(file.filepath) == 'png' ||
                                                                getExtension(file.filepath) == 'jpg' ||
                                                                getExtension(file.filepath) == 'jpeg' ||
                                                                getExtension(file.filepath) == 'webp' ||
                                                                getExtension(file.filepath) == 'gif' ||
                                                                getExtension(file.filepath) == 'svg'
                                                            "
                                                            role="button"
                                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                            style="background-color: #b4d2ff4e"
                                                        >
                                                            <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }" @click="showSingleImage = true">
                                                                <i class="fa fa-camera"></i>
                                                            </div>

                                                            <vue-easy-lightbox :visible="showSingleImage" :imgs="`storage${file.filepath}`" @hide="showSingleImage = false"></vue-easy-lightbox>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </simplebar>
                    </div>
                    <div class="p-3 chat-input-section">
                        <form @submit.prevent="submitResponse" class="row">
                            <div class="col">
                                <div class="d-flex flex-row gap-2 mb-4" v-if="supportResponseFiles.length > 0">
                                    <div v-for="(file, index) in supportResponseFiles" :key="index">
                                        <div style="position: absolute">
                                            <i class="bx bxs-x-circle text-danger mb-3" role="button" @click="deleteResponseFile(index)"></i>
                                        </div>
                                        <div
                                            role="button"
                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="background-color: #b4d2ff4e"
                                            v-if="getExtension(file.name) == 'pdf'"
                                        >
                                            <PdfIcon :height="'20px'" :width="'20px'" />
                                        </div>
                                        <div
                                            v-else-if="getExtension(file.name) == 'xls' || getExtension(file.name) == 'xlsx'"
                                            role="button"
                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="background-color: #b4d2ff4e"
                                        >
                                            <ExcelIcon :height="'20px'" :width="'20px'" />
                                        </div>
                                        <div
                                            v-else-if="getExtension(file.name) == 'doc' || getExtension(file.name) == 'docx'"
                                            role="button"
                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="background-color: #b4d2ff4e"
                                        >
                                            <WordIcon :height="'20px'" :width="'20px'" />
                                        </div>
                                        <div
                                            v-else-if="
                                                getExtension(file.name) == 'png' ||
                                                getExtension(file.name) == 'jpg' ||
                                                getExtension(file.name) == 'jpeg' ||
                                                getExtension(file.name) == 'webp' ||
                                                getExtension(file.name) == 'gif' ||
                                                getExtension(file.name) == 'svg'
                                            "
                                            role="button"
                                            class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                            style="background-color: #b4d2ff4e"
                                        >
                                            <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }">
                                                <i class="fa fa-camera"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="position-relative">
                                    <div class="mb-4">
                                        <div class="input-group">
                                            <input v-model="responseTitle" type="text" class="form-control chat-input rounded" placeholder="Enter Title" style="font-size: 13px" />
                                            <input type="file" @change="onChange" hidden id="supportResponseFile" />
                                            <b-button variant="secondary" class="" v-b-tooltip.hover placement="top" title="Add Files" @click="addSupportReponseFile">
                                                <ul class="list-inline mb-0">
                                                    <li class="list-inline-item text-white">
                                                        <a href="javascript: void(0);">
                                                            <i class="mdi mdi-file-document-outline text-white"></i>
                                                        </a>
                                                    </li>
                                                    Add File
                                                </ul>
                                            </b-button>
                                        </div>
                                    </div>

                                    <textarea v-model="responseMessage" class="form-control" rows="4" placeholder="Enter Message..." style="font-size: 13px"></textarea>
                                </div>
                            </div>
                            <div class="col-auto" style="margin-top: 17%">
                                <button type="submit" class="btn btn-primary btn-rounded chat-send w-md" :disabled="showSubmissionLoader">
                                    <span class="d-none d-sm-inline-block me-2"> <i class="bx bx-loader bx-spin font-size-16 align-middle me-2" v-if="showSubmissionLoader"></i>Send</span>
                                    <i class="mdi mdi-send"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <b-modal v-model="viewFileModal" title-class="font-18" centered hide-footer hide-title size="lg">
            <vue-pdf-app v-if="viewPdfFile != null" style="height: 80vh" theme="light" :pdf="`storage${viewPdfFile}`" file-name="SupportDocument.pdf"></vue-pdf-app>
        </b-modal>
    </div>
</template>
<style scoped>
/* pdf viewer customisation */

.pdf-app.light {
    --pdf-toolbar-color: #0160a0;
}
</style>
