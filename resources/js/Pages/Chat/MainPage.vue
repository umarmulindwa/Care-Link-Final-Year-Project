<script>
import simplebar from "simplebar-vue";
import { chatData, chatMessagesData } from "./data";
import VueEasyLightbox from "vue-easy-lightbox";
import avatar1 from "@/images/users/avatar-1.jpg";
import { usePage, router } from "@inertiajs/vue3";
import PdfIcon from "../../Components/icons/PdfIcon.vue";
import ExcelIcon from "../../Components/icons/ExcelIcon.vue";
import WordIcon from "../../Components/icons/WordIcon.vue";
import moment from "moment";
import VuePdfApp from "vue3-pdf-app";
import "vue3-pdf-app/dist/icons/main.css";
import axios from "axios";
import Swal from "sweetalert2";
import Layout from "../../Layouts/main.vue";

export default {
    components: {
        simplebar,
        PdfIcon,
        ExcelIcon,
        WordIcon,
        VuePdfApp,
        VueEasyLightbox,
        Layout,
    },
    setup() {
        return {
            usePage,
        };
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
            username: "",
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
            activeTab: "General",
            activePrivateChat: [],
            chatThread: [],
        };
    },
    props: {
        generalChat: {
            type: Array,
            default: [],
        },
        privateChats: {
            type: Array,
            default: [],
        },
        allUsers: {
            type: Array,
            default: [],
        },
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
        const app = this;
        let firstThread = app.allRequests[0];
        var container = document.querySelector("#my-element .simplebar-content-wrapper");
        container.scrollTo({ top: 500, behavior: "smooth" });

        var container2 = document.querySelector("#containerElement .simplebar-content-wrapper");
        container2.scrollTo({ top: 500, behavior: "smooth" });
        document.body.classList.remove("side-bg");

        //the active chat thread is the general chats
        app.chatThread = app.generalChat;

        // //scroll the chat to the bottom
        setTimeout(() => {
            var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
            var scrollHeight = document.getElementById("containerElement").scrollHeight;
            Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
        }, 200);

        this.activePrivateChat = this.allUsers[0];
    },
    computed: {
        currentUser: () => {
            return usePage().props.auth.user;
        },
        currentUserEmail: () => {
            return usePage().props.auth.user.email;
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
        chatUsername(details) {
            const app = this;

            app.activePrivateChat = details;
            //the default active private thread is that of the first user
            const firstUserEmail = app.activePrivateChat.email;
            const privateChats = app.privateChats;
            //filter private chats for only the first user
            const filterPrivateChats = privateChats.filter((item) => {
                return item.to_email == firstUserEmail || item.sent_by_email == firstUserEmail;
            });
            app.chatThread = filterPrivateChats;
            // //scroll chat to the bottom
            setTimeout(() => {
                var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                var scrollHeight = document.getElementById("containerElement").scrollHeight;
                Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
            }, 200);
        },
        showGeneralChat() {
            const app = this;
            app.activeTab = "General";
            app.chatThread = app.generalChat;
            //scroll chat to the bottom
            setTimeout(() => {
                var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                var scrollHeight = document.getElementById("containerElement").scrollHeight;
                Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
            }, 200);
        },
        showPrivateChat() {
            const app = this;
            app.activeTab = "Private";

            //the default active private thread is that of the first user
            const firstUserEmail = app.activePrivateChat.email;
            const privateChats = app.privateChats;
            //filter private chats for only the first user
            const filterPrivateChats = privateChats.filter((item) => {
                return item.to_email == firstUserEmail || item.sent_by_email == firstUserEmail;
            });
            app.chatThread = filterPrivateChats;
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

            app.submitResponseLoader = true;

            app.supportResponseFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file.file);
            });
            formData.append("title", app.responseTitle);
            formData.append("message", app.responseMessage);
            formData.append("support_request_id", app.threadId);
            formData.append("messageToName", app.activePrivateChat.name);
            formData.append("messageToEmail", app.activePrivateChat.email);
            formData.append('profilePicUrl', usePage().props.auth.user.profile_photo_url)

            if (app.activeTab == "General") {
                axios
                    .post("/api/chat/generalchat", formData, config)
                    .then((res) => {
                        if (res.status == 200) {
                            app.submitResponseLoader = false;
                            app.responseIsSubmited = true;
                            router.visit("/chat", { preserveState: false, only: ["generalChat"], onSuccess: (page) => {} });
                        }
                    })
                    .catch((error) => {
                        console.log({ error });
                        app.submitResponseLoader = false;
                        app.responseIsSubmited = false;

                        Swal.fire({
                            title: "Something Went Wrong",
                            icon: "error",
                            html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to Support for help.</p>`,
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
            } else {
                axios
                    .post("/api/chat/privateChat", formData, config)
                    .then((res) => {
                        console.log(res);
                        if (res.status == 200) {
                            app.submitResponseLoader = false;
                            app.responseIsSubmited = true;
                            router.visit("/chat", {
                                preserveState: true,
                                only: ["privateChats"],
                                onSuccess: (page) => {
                                    this.showPrivateChat();
                                },
                            });
                        }
                    })
                    .catch((error) => {
                        console.log({ error });
                        app.submitResponseLoader = false;
                        app.responseIsSubmited = false;

                        Swal.fire({
                            title: "Something Went Wrong",
                            icon: "error",
                            html: `<p style="font-size: 14px">There is something that went wrong and it is not your fault. Please, reach out to Support for help.</p>`,
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
            }

            app.responseMessage = "";
            app.responseTitle = "";
            app.supportResponseFiles = [];
            //scroll to last message
            // setTimeout(() => {
            //     var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
            //     var scrollHeight = document.getElementById("containerElement").scrollHeight;
            //     Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
            // }, 200);
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
                // setTimeout(() => {
                //     var Element = document.querySelector("#containerElement .simplebar-content-wrapper");
                //     var scrollHeight = document.getElementById("containerElement").scrollHeight;
                //     Element.scrollTo({ top: scrollHeight + 1180, behavior: "smooth" });
                // }, 500);
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
    },
};
</script>
<template>
    <Layout>
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
                            <b-tab active @click="showGeneralChat">
                                <template v-slot:title>
                                    <span class="">General Chat</span>
                                </template>
                                <b-card-text>
                                    <div></div>
                                </b-card-text>
                            </b-tab>
                            <b-tab @click="showPrivateChat">
                                <template v-slot:title>
                                    <span class="">Private Chat</span>
                                </template>
                                <b-card-text>
                                    <div>
                                        <simplebar style="max-height: 410px" id="my-element">
                                            <ul class="list-unstyled chat-list">
                                                <li class v-for="data of allUsers" :key="`${data.id}_ ${data.name}`" @click="chatUsername(data)" :class="{ active: username == data.name }">
                                                    <a href="javascript: void(0);">
                                                        <div class="d-flex">
                                                            <div class="">
                                                                <div class="d-flex flex-row gap-4 align-items-center justify-content-center">
                                                                    <div>
                                                                        <img :src="data.profile_photo_url" class="avatar-xs rounded-circle" alt="" />
                                                                    </div>
                                                                    <div class="d-flex flex-row justify-content-between">
                                                                        <div>
                                                                            <h5 class="text-truncate font-size-14">
                                                                                {{ data.name }}
                                                                            </h5>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                <h5 class="font-size-15 mb-1" v-if="activeTab != 'General'">{{ activePrivateChat.name }}</h5>
                                <h5 class="font-size-15 mb-1" v-else>General Chat</h5>
                            </div>
                            <div class="col-2">
                                <ul class="list-inline user-chat-nav text-end mb-0">
                                    <li class="list-inline-item d-none d-sm-inline-block">
                                        <b-dropdown toggle-class="nav-btn" menu-class="dropdown-menu-end" right variant="white">
                                            <template v-slot:button-content>
                                                <i class="bx bx-cog"></i>
                                            </template>
                                            <b-dropdown-item @click="rearrage">Rearrange</b-dropdown-item>
                                        </b-dropdown>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="chat-users">
                        <div class="chat-conversation p-3">
                            <simplebar style="max-height: 45vh; min-height: 45vh" id="containerElement" ref="current">
                                <ul class="list-unstyled">
                                    <li v-for="data of chatThread" :key="`${data.id}_${data.message}`" :class="{ right: `${data.sent_by_email}` === usePage().props.auth.user.email }">
                                        <div class="conversation-list">
                                            <div class="ctext-wrap">
                                                <div class="mb-3">
                                                    <div class="text-primary fw-bold">{{ data.sent_by_name }}</div>
                                                    <div>
                                                        <small class="text-muted">Issue: {{ data.title }}</small>
                                                    </div>
                                                </div>

                                                <div v-html="data.message"></div>
                                                <div class="d-flex justify-content-between align-items-center mt-4">
                                                    <div class="me-4">
                                                        <p class="chat-time mb-0">
                                                            <i class="bx bx-time-five align-middle me-1"></i>
                                                            <small>{{ fromNow(data.created_at) }}</small>
                                                        </p>
                                                    </div>
                                                    <div class="d-flex flex-row gap-2" v-if="data.files.length > 0">
                                                        <div v-for="(file, index) in data.files" :key="index">
                                                            <div
                                                                @click="pdfUrl(file.original_url)"
                                                                role="button"
                                                                class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                                style="background-color: #b4d2ff4e"
                                                                v-if="getExtension(file.original_url) == 'pdf'"
                                                            >
                                                                <PdfIcon :height="'20px'" :width="'20px'" />
                                                            </div>
                                                            <div
                                                                v-else-if="getExtension(file.original_url) == 'xls' || getExtension(file.original_url) == 'xlsx'"
                                                                role="button"
                                                                class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                                style="background-color: #b4d2ff4e"
                                                            >
                                                                <ExcelIcon :height="'20px'" :width="'20px'" />
                                                            </div>
                                                            <div
                                                                v-else-if="getExtension(file.original_url) == 'doc' || getExtension(file.original_url) == 'docx'"
                                                                role="button"
                                                                class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                                style="background-color: #b4d2ff4e"
                                                            >
                                                                <WordIcon :height="'20px'" :width="'20px'" />
                                                            </div>
                                                            <div
                                                                v-else-if="
                                                                    getExtension(file.original_url) == 'png' ||
                                                                    getExtension(file.original_url) == 'jpg' ||
                                                                    getExtension(file.original_url) == 'jpeg' ||
                                                                    getExtension(file.original_url) == 'webp' ||
                                                                    getExtension(file.original_url) == 'gif' ||
                                                                    getExtension(file.original_url) == 'svg'
                                                                "
                                                                role="button"
                                                                class="m-0 avatar-xs rounded-circle d-flex align-items-center justify-content-center"
                                                                style="background-color: #b4d2ff4e"
                                                            >
                                                                <div :style="{ 'border-color': '#6a1b9a', color: '#6a1b9a' }" @click="showSingleImage = true">
                                                                    <i class="fa fa-camera"></i>
                                                                </div>
                                                                <div>
                                                                    <vue-easy-lightbox
                                                                        style="z-index: 999"
                                                                        :visible="showSingleImage"
                                                                        :imgs="`${file.original_url}`"
                                                                        @hide="showSingleImage = false"
                                                                    ></vue-easy-lightbox>
                                                                </div>
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
                <vue-pdf-app v-if="viewPdfFile != null" style="height: 80vh" theme="light" :pdf="`${viewPdfFile}`" file-name="SupportDocument.pdf"></vue-pdf-app>
            </b-modal>
        </div>
    </Layout>
</template>
<style scoped>
/* pdf viewer customisation */

.pdf-app.light {
    --pdf-toolbar-color: #0160a0;
}
</style>
