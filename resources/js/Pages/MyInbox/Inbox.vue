<script setup>
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

import Layout from "../../Layouts/main.vue";
// import PageHeader from "../../components/page-header.vue";
import Sidepanel from "./sidepanel.vue";
import { emailData } from "./emailData";
import { reactive, onMounted, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import moment from "moment";
import Checkbox from "@/Components/Checkbox.vue";
import Swal from "sweetalert2";
//Default Layout
defineOptions({ layout: Layout });

//props
const props = defineProps({
    AdminLogs: Array,
    myInbox: Array,
    bsclogs: Array,
    sentEmails: Array,
    outboxEmails: Array,
    allEmails: Array,
    hrlogs: Array,
    myInboxCount: String,
    allAdminLogsCount: String,
    allBscLogsCount: String,
    fiananceLogsCount: String,
    allFinanceLogsCount: String,
    allSentCount: String,
    allOutboxCount: String,
    supplyLogs: Array,
    supplyLogsCount: String,
    ictLogs: Array,
    ictLogsCount: String,
    myBlackListedEmails: Array,
    allBlacklistedEmails: Array,
});
//data
const state = reactive({
    emailData: emailData,
    paginatedEmailData: emailData,
    title: "Inbox",
    items: [
        {
            text: "Email",
            href: "/",
        },
        {
            text: "Inbox",
            active: true,
        },
    ],
    showAdminLogs: false,
    showAdminEmailLogIndex: 0,
    showMyInboxMail: false,
    showMyInboxMailIndex: 0,
    showBscEmailLogIndex: 0,
    showBscLogs: false,
    showMySentMail: false,
    showMySentMailIndex: 0,
    showOutboxMail: false,
    showAdminEmailLogIndex: 0,
    showAllIndex: 0,
    showAllMail: false,
    filterBy: "",
    showHrIndex: 0,
    showHrMail: false,
    showOutboxMailIndex: 0,
    showSupplyEmailLogIndex: 0,
    showSupplyLogs: false,
    showICTEmailLogIndex: 0,
    showICTLogs: false,
    ignoreEmails: [],
    confirmEmailIgnoreModal: false,
});

//methods
//mounted
onMounted(() => {
    //changing filter by value
    switch (usePage().url) {
        case "/inbox":
            state.filterBy = "My Inbox";
            break;
        case "/outbox":
            state.filterBy = "Outbox";
            break;
        case "/sent":
            state.filterBy = "Sent";
            break;
        case "/reminders":
            state.filterBy = "Reminders";
            break;
        case "/logs/administration":
            state.filterBy = "Administration Logs";
            break;
        case "/logs/finance":
            state.filterBy = "Finance Logs";
            break;
        case "/logs/availability":
            state.filterBy = "Availability Planner Logs";
            break;
        case "/logs/vistortracker":
            state.filterBy = "Visitor tracker Logs";
            break;
        case "/logs/supply":
            state.filterBy = "Supply Logs";
            break;
        case "/logs/system":
            state.filterBy = "System Logs";
            break;
        case "/logs/hr":
            state.filterBy = "Hr Logs";
            break;

        case "/logs/ict":
            state.filterBy = "ICT Logs";
            break;
        case "/logs/blacklist":
            state.filterBy = "Blacklisted Emails";
            break;
        default:
            state.filterBy = "All";
    }
});
//computed
const reversedInboxMails = computed(() => {
    if (props.myInbox.length > 0) {
        return props.myInbox;
    } else {
        return props.myInbox;
    }
});

const reversedAllEmails = computed(() => {
    const allEmails = props.allEmails;
    return allEmails;
});
const currentUser = computed(() => {
    return usePage().props.auth.user;
});

const reversedBscLogsMails = computed(() => {
    return props.bsclogs;
});
const isSuperAdmin = computed(() => {
    const isSuperAdminPerm = usePage().props.auth.user.allPermissions.includes("s_admin");
    if (isSuperAdminPerm) {
        return true;
    } else {
        return false;
    }
});
function dateFormat(date2format) {
    return moment(date2format).format("Do MMM YYYY");
}
//ignore email function
function ingnoreEmail(e, subject) {
    if (e.target.checked === true) {
        if (!state.ignoreEmails.includes(subject)) {
            state.ignoreEmails.push(subject);
        }
    } else {
        const currentIgnoredEmails = state.ignoreEmails;
        const newIgnoredEmails = currentIgnoredEmails.filter((emailSubject) => emailSubject != subject);
        state.ignoreEmails = newIgnoredEmails;
    }
}
function stopRecievingEmails() {
    state.confirmEmailIgnoreModal = false;
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
    formData.append("emails", JSON.stringify(state.ignoreEmails));
    axios
        .post(`/api/staff-register/blacklistemail`, formData, options)
        .then((res) => {
            Swal.fire({
                icon: "success",
                title: "Success",
                html: "<p class='font-size: 13px'>Emails have been blacklisted.</p>",
                showConfirmButton: true,
                allowOutsideClick: false,
                showCloseButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#32CD32",
            }).then((result) => {
                if (result.isConfirmed) {
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
                    Swal.close();
                }
            });
        });
}
function removeBlackList(email) {
    Swal.fire({
        title: "Are you sure?",
        html: `<p>You are about to remove this email from the blacklist.</p>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
        confirmButtonText: "Yes, Proceed",
    }).then((result) => {
        if (result.value) {
            state.confirmEmailIgnoreModal = false;
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
            formData.append("email", email.id);
            axios
                .post(`/api/staff-register/removeblacklistemail`, formData, options)
                .then((res) => {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        html: "<p class='font-size: 13px'>Email has been removed from the blacklist.</p>",
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
//show admin mail log
function showAdminEmailLog(index) {
    state.showSupplyEmailLogIndex = index;
    state.showAdminLogs = true;
}
//showing inbox Email
function showMyinboxEmail(index) {
    state.showMyInboxMailIndex = index;
    state.showMyInboxMail = true;
}
//showing sent mail
function showSentMail(index) {
    state.showMySentMailIndex = index;
    state.showMySentMail = true;
}
//showing outbox email
function showOutboxMail(index) {
    state.showOutboxMailIndex = index;
    state.showOutboxMail = true;
}
//showing all emails for the current user
function showAllMail(index) {
    state.showAllIndex = index;
    state.showAllMail = true;
}
//showing hr email logs
function showHrLog(index) {
    state.showHrIndex = index;
    state.showHrMail = true;
}
//showing Bsc Email Logs
function showBscEmailLog(index) {
    (state.showBscEmailLogIndex = index), (state.showBscLogs = true);
}

// show supply email log
function showSupplyEmailLog(index) {
    state.showSupplyEmailLogIndex = index;
    state.showSupplyLogs = true;
}

function seeAdminLogs() {
    router.visit("/logs/administration");
}

// show ict email log
function showICTEmailLog(index) {
    state.showICTEmailLogIndex = index;
    state.showICTLogs = true;
}
</script>

<template>
    <Head title="Emails" />

    <PageHeader :title="state.title" :items="state.items" />
    <div class="row">
        <!-- Right Sidebar -->
        <div class="col-12">
            <div class="mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="btn-toolbar p-3">
                            <div class="d-flex flex-row justify-content-between col-12 mb-4">
                                <div class="d-flex w-100 align-items-center justify-content-between">
                                    <div class="rounded-pill d-none d-lg-block" style="background-color: #f9f9f9; border: 0px">
                                        <div class="input-group d-flex flex-row align-items-center">
                                            <div class="mt-1">
                                                <span class="" style="font-size: medium"><i class="bx bx-search-alt"></i></span>
                                            </div>
                                            <div class="">
                                                <input type="text" class="form-control" placeholder="Search.." style="background-color: #f9f9f9; border: 0px" v-model="state.searchText" />
                                            </div>

                                            <div class="hstack gap-3" role="button">
                                                <div class="vr" style="color: #dbdbdb"></div>
                                                <div class="btn-group dropbottomstart">
                                                    <div data-bs-toggle="dropdown" class="d-flex flex-row gap-3">
                                                        <div class="">{{ state.filterBy }}</div>
                                                        <div>
                                                            <i class="mdi mdi-chevron-down" aria-expanded="false"></i>
                                                        </div>
                                                    </div>

                                                    <ul class="dropdown-menu">
                                                        <!-- Dropdown menu links -->
                                                        <Link :href="route('AllLogs')" :only="['allEmails']">
                                                            <b-dropdown-item>
                                                                <span
                                                                    :class="usePage().url == '/logs/all' ? 'fw-medium  text-danger' : ''"
                                                                    :only="['myInbox', 'myInboxCount', 'allAdminLogsCount', 'allBscLogsCount', 'allSentCount', 'allOutboxCount']"
                                                                    >All</span
                                                                ></b-dropdown-item
                                                            >
                                                        </Link>
                                                        <Link :href="route('myInbox')" :only="['myInbox', 'myInboxCount', 'allAdminLogsCount', 'allBscLogsCount', 'allSentCount', 'allOutboxCount']">
                                                            <b-dropdown-item> <span :class="usePage().url == '/inbox' ? 'fw-medium  text-danger' : ''">My Inbox</span></b-dropdown-item>
                                                        </Link>
                                                        <Link :href="route('sent')" :only="['sentEmails', 'allSentCount']">
                                                            <b-dropdown-item> <span :class="usePage().url == '/sent' ? 'fw-medium  text-danger' : ''">Sent</span></b-dropdown-item>
                                                        </Link>
                                                        <Link :href="route('outbox')" :only="['outboxEmails', 'allOutboxCount']">
                                                            <b-dropdown-item><span :class="usePage().url == '/outbox' ? 'fw-medium  text-danger' : ''">Outbox</span></b-dropdown-item>
                                                        </Link>
                                                        <Link>
                                                            <b-dropdown-item><span :class="usePage().url == '/reminders' ? 'fw-medium  text-danger' : ''">Reminders</span></b-dropdown-item>
                                                        </Link>
                                                        <div v-if="isSuperAdmin">
                                                            <!-- <Link :href="route('AdminLogs')" :only="['AdminLogs', 'allAdminLogsCount']">
                                                                <b-dropdown-item
                                                                    ><span :class="usePage().url == '/logs/administration' ? 'fw-medium  text-danger' : ''">Administration Logs</span></b-dropdown-item
                                                                >
                                                            </Link> -->
                                                            <div @click="seeAdminLogs">
                                                                <b-dropdown-item
                                                                    ><span :class="usePage().url == '/logs/administration' ? 'fw-medium  text-danger' : ''">Administration Logs</span></b-dropdown-item
                                                                >
                                                            </div>
                                                            <Link :href="route('bscFinance')" :only="['bsclogs', 'allBscLogsCount']">
                                                                <b-dropdown-item><span :class="usePage().url == '/logs/finance' ? 'fw-medium  text-danger' : ''">Finance Logs</span></b-dropdown-item>
                                                            </Link>
                                                            <Link>
                                                                <b-dropdown-item
                                                                    ><span :class="usePage().url == '/logs/availability' ? 'fw-medium  text-danger' : ''"
                                                                        >Availability Planner Logs</span
                                                                    ></b-dropdown-item
                                                                >
                                                            </Link>
                                                            <Link>
                                                                <b-dropdown-item
                                                                    ><span :class="usePage().url == '/logs/vistortracker' ? 'fw-medium  text-danger' : ''">Visitor Tracker Logs</span></b-dropdown-item
                                                                >
                                                            </Link>
                                                            <Link :href="route('SupplyLogs')" :only="['supplyLogs']">
                                                                <b-dropdown-item><span :class="usePage().url == '/logs/supply' ? 'fw-medium  text-danger' : ''">Supply Logs</span></b-dropdown-item>
                                                            </Link>
                                                            <Link :href="route('HrLogs')" :only="['hrlogs']">
                                                                <b-dropdown-item><span :class="usePage().url == '/logs/hr' ? 'fw-medium  text-danger' : ''">HR Logs</span></b-dropdown-item>
                                                            </Link>

                                                            <Link :href="route('ICTLogs')" :only="['ictLogs']">
                                                                <b-dropdown-item><span :class="usePage().url == '/logs/ict' ? 'fw-medium  text-danger' : ''">ICT Logs</span></b-dropdown-item>
                                                            </Link>
                                                            <Link>
                                                                <b-dropdown-item><span :class="usePage().url == '/logs/system' ? 'fw-medium  text-danger' : ''">System Logs</span> </b-dropdown-item>
                                                            </Link>
                                                        </div>
                                                        <Link :href="route('blacklist')" :only="['myBlackListedEmails', 'allBlacklistedEmails']">
                                                            <b-dropdown-item
                                                                ><span :class="usePage().url == '/logs/blacklist' ? 'fw-medium  text-danger' : ''">Blacklisted Emails</span>
                                                            </b-dropdown-item>
                                                        </Link>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="state.ignoreEmails.length > 0" @click="state.confirmEmailIgnoreModal = true">
                                        <b-button variant="danger">Stop Receiving these Email(s)</b-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-if="usePage().url == '/inbox'">
                            <div v-if="props.myInbox.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">Your inbox is empty!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showMyInboxMail">
                                    <div @click="state.showMyInboxMail = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.myInbox[state.showMyInboxMailIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in reversedInboxMails" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showMyinboxEmail(index)" role="button" class="title"
                                                        ><span v-if="email.submitted_by == currentUser.name">System</span><span v-else>{{ email.submitted_by }}</span></a
                                                    >
                                                </div>
                                                <div @click="showMyinboxEmail(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-else-if="usePage().url == '/sent'">
                            <div v-if="props.sentEmails.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">You don't have any sent emails!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showMySentMail">
                                    <div @click="state.showMySentMail = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.sentEmails[state.showMySentMailIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in sentEmails" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showSentMail(index)" role="button" class="title">{{ email.to }}</a>
                                                </div>
                                                <div @click="showSentMail(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-else-if="usePage().url == '/outbox'">
                            <div v-if="props.outboxEmails.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">Your outbox is empty!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showOutboxMail">
                                    <div @click="state.showOutboxMail = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.outboxEmails[state.showOutboxMailIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.outboxEmails" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showOutboxMail(index)" role="button" class="title">{{ email.to }}</a>
                                                </div>
                                                <div @click="showOutboxMail(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-else-if="usePage().url == '/logs/all'">
                            <div v-if="props.allEmails.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showAllMail">
                                    <div @click="state.showAllMail = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.allEmails[state.showAllIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in reversedAllEmails" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <a @click="showAllMail(index)" role="button" class="title">{{ email.to }}</a>
                                                </div>
                                                <div @click="showAllMail(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3" v-else-if="usePage().url == '/logs/administration'">
                            <div v-if="props.AdminLogs.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showAdminLogs">
                                    <div @click="state.showAdminLogs = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.AdminLogs[state.showAdminEmailLogIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.AdminLogs" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showAdminEmailLog(index)" role="button" class="title">{{ email.submitted_by }}</a>
                                                </div>
                                                <div @click="showAdminEmailLog(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3" v-else-if="usePage().url == '/logs/finance'">
                            <div v-if="props.bsclogs.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showBscLogs">
                                    <div @click="state.showBscLogs = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.bsclogs[state.showBscEmailLogIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.bsclogs" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showBscEmailLog(index)" role="button" class="title">{{ email.submitted_by }}</a>
                                                </div>
                                                <div @click="showBscEmailLog(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-else-if="usePage().url == '/logs/hr'">
                            <div v-if="props.hrlogs.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showHrMail">
                                    <div @click="state.showHrMail = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.hrlogs[state.showHrIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.hrlogs" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showHrLog(index)" role="button" class="title">{{ email.submitted_by }}</a>
                                                </div>
                                                <div @click="showHrLog(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">{{ moment(email.created_at).fromNow() }}</div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Supply Plans -->
                        <div class="mt-3" v-else-if="usePage().url == '/logs/supply'">
                            <div v-if="props.supplyLogs.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showSupplyLogs">
                                    <div @click="state.showSupplyLogs = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.supplyLogs[state.showSupplyEmailLogIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.supplyLogs" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showSupplyEmailLog(index)" role="button" class="title">{{ email.submitted_by }}</a>
                                                </div>
                                                <div @click="showSupplyEmailLog(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">
                                                        {{ moment(email.created_at).fromNow() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- ICT Logs -->
                        <div class="mt-3" v-else-if="usePage().url == '/logs/ict'">
                            <div v-if="props.ictLogs.length == 0" class="text-center" style="padding-top: 10rem; padding-bottom: 20rem">
                                <div><i class="mdi mdi-email-minus-outline text-muted" style="font-size: xx-large"></i></div>
                                <div class="fw-bold text-muted">No Emails Found!</div>
                            </div>
                            <div v-else>
                                <div v-if="state.showICTLogs">
                                    <div @click="state.showICTLogs = false" class="ps-4 pb-3 d-flex col-1" role="button" style="font-size: large"><i class="bx bx-arrow-back"></i></div>
                                    <div v-html="props.ictLogs[state.showICTEmailLogIndex].body"></div>
                                </div>
                                <div v-else>
                                    <ul class="message-list">
                                        <li :class="{ unread: email.unread === true }" v-for="(email, index) in props.ictLogs" :key="`${index + email.subject}`">
                                            <div>
                                                <div class="col-mail col-mail-1">
                                                    <div class="checkbox-wrapper-mail">
                                                        <input :id="`chk-${index}`" type="checkbox" @change="ingnoreEmail($event, email.subject)" />
                                                        <label :for="`chk-${index}`"></label>
                                                    </div>
                                                    <!-- <span :class="`star-toggle far fa-star text-${email.submitted_by}`"></span> -->
                                                    <a @click="showICTEmailLog(index)" role="button" class="title">{{ email.submitted_by }}</a>
                                                </div>
                                                <div @click="showICTEmailLog(index)" class="d-flex flex-row justify-content-between pe-4">
                                                    <div role="button" class="subject">{{ email.subject }}</div>
                                                    <div role="button" class="date">
                                                        {{ moment(email.created_at).fromNow() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" v-else-if="usePage().url == '/logs/blacklist'">
                            <b-tabs>
                                <b-tab :title="'All'" v-if="isSuperAdmin">
                                    <div class="table-responsive mb-4 mt-5" v-if="props.allBlacklistedEmails.length > 0">
                                        <table class="table table-nowrap table-hover mb-0 align-middle">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Staff</th>
                                                    <th scope="col">Email</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(email, index) in props.allBlacklistedEmails" :key="index">
                                                    <th scope="row">{{ index + 1 }}</th>
                                                    <td>{{ email.staff }}</td>
                                                    <td>{{ email.subject }}</td>
                                                    <td>{{ dateFormat(email.created_at) }}</td>
                                                    <td><b-button variant="primary" @click="removeBlackList(email)">Undo</b-button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else>
                                        <div class="text-center mt-5 mb-5">
                                            <div class="mb-2"><i class="fas fa-ban text-muted" style=""></i></div>
                                            No Blacklisted Emails
                                        </div>
                                    </div>
                                </b-tab>
                                <b-tab active :title="'Mine'">
                                    <div class="table-responsive mb-4 mt-5" v-if="props.myBlackListedEmails.length > 0">
                                        <table class="table table-nowrap table-hover mb-0 align-middle">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Staff</th>
                                                    <th scope="col">Email</th>
                                                    <th scope="col">Date</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(email, index) in props.myBlackListedEmails" :key="index">
                                                    <th scope="row">{{ index + 1 }}</th>
                                                    <td>{{ email.staff }}</td>
                                                    <td>{{ email.subject }}</td>
                                                    <td>{{ dateFormat(email.created_at) }}</td>
                                                    <td><b-button variant="primary" @click="removeBlackList(email)">Undo</b-button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-else>
                                        <div class="text-center mt-5 mb-5">
                                            <div class="mb-2"><i class="fas fa-ban text-muted" style=""></i></div>
                                            No Blacklisted Emails
                                        </div>
                                    </div>
                                </b-tab>
                            </b-tabs>
                        </div>
                    </div>
                </div>
                <b-modal
                    v-model="state.confirmEmailIgnoreModal"
                    id="confirmEmailIgnoreModal"
                    title-class="font-18"
                    title="Confirm"
                    centered
                    header-class="d-flex justify-content-center"
                    hide-header
                    hide-footer
                >
                    <div class="mb-5">
                        <div class="text-center fw-bold">Are you Sure?</div>
                        <div class="text-center mb-3">You will nolonger receive these email(s)</div>
                    </div>
                    <div class="mb-3">
                        <simplebar style="max-height: 50vh">
                            <div class="mb-4">
                                <label>Blacklist Email(s)</label>
                            </div>
                            <ul>
                                <li v-for="(email, index) in state.ignoreEmails" :key="index">{{ email }}</li>
                            </ul>
                        </simplebar>
                    </div>
                    <div class="border-top">
                        <div class="d-flex gap-3 justify-content-end mt-3">
                            <div>
                                <b-button variant="danger" @click="state.confirmEmailIgnoreModal = false">Cancel</b-button>
                            </div>
                            <div>
                                <b-button variant="primary" @click="stopRecievingEmails">Yes, Proceed</b-button>
                            </div>
                        </div>
                    </div>
                </b-modal>

                <div class="row justify-content-md-between align-items-md-center">
                    <!-- <div class="col-xl-7">Showing {{ startIndex }} - {{ endIndex }} of {{ rows }}</div> -->
                    <!-- end col-->
                    <!-- <div class="col-xl-5">
                            <div class="text-md-end float-xl-end mt-2 pagination-rounded">
                                <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" @input="onPageChange"></b-pagination>
                            </div>
                        </div> -->
                    <!-- end col-->
                </div>
            </div>
        </div>
    </div>
</template>
