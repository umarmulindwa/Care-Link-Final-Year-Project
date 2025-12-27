<script setup>
import CKEditor from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import { reactive, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

//data
//state
const state = reactive({
    showModal: false,
    editor: ClassicEditor,
    editorData: "<p>Content of the editor.</p>",
});
//props
const props = defineProps({
    inboxCount: String,
    sentCount: String,
    adminLogsCount: String,
    bscLogsCount: String,
    financeLogsCount: String,
    outboxCount: String,
});

const isSuperAdmin = computed(() => {
    const isSuperAdminPerm = usePage().props.auth.user.allPermissions.includes("s_admin");
    if (isSuperAdminPerm != null) {
        return true;
    } else {
        return false;
    }
});
</script>

<template>
    <div class="email-leftbar card">
        <b-button variant="danger" @click="state.showModal = true">Compose</b-button>
        <div class="mail-list mt-4" :class="isSuperAdmin ? '' : 'mb-5'">
            <Link :href="route('myInbox')" :class="usePage().url == '/inbox' ? 'active' : ''">
                <i class="mdi mdi-email-outline me-2"></i> Inbox
                <span class="ms-1 float-end">({{ props.inboxCount }})</span>
            </Link>

            <a href="javascript:void(0)" class="">
                <i class="mdi mdi-timer-outline me-2"></i> Reminders
                <span class="ms-1 float-end">(0)</span>
            </a>

            <a href="javascript:void(0)"> <i class="mdi mdi-file-outline me-2"></i>Draft </a>
            <Link :href="route('outbox')" :class="usePage().url == '/outbox' ? 'active' : ''">
                <i class="mdi mdi-email-remove-outline me-2"></i>Outbox
                <span class="ms-1 float-end">({{ props.outboxCount }})</span>
            </Link>
            <Link :href="route('sent')" :class="usePage().url == '/sent' ? 'active' : ''">
                <i class="mdi mdi-email-check-outline me-2"></i>Sent Mail
                <span class="ms-1 float-end">({{ props.sentCount }})</span>
            </Link>
            <a href="javascript:void(0)"> <i class="mdi mdi-trash-can-outline me-2"></i>Trash </a>
        </div>

        <div>
            <h6 class="mt-4">Email Logs</h6>

            <div class="mail-list mt-1">
                <Link :href="route('adminLogs')" :class="usePage().url == '/logs/administration' ? 'text-danger' : ''">
                    <span class="mdi mdi-arrow-right-drop-circle text-info float-end"></span>Administration <span>({{ props.adminLogsCount }})</span></Link
                >
                <Link :href="route('financeLogs')" :class="usePage().url == '/logs/bsc' ? 'text-danger' : ''">
                    <span class="mdi mdi-arrow-right-drop-circle text-warning float-end"></span>Finance <span>({{ props.financeLogsCount }})</span></Link
                >
                <Link :href="route('bscLogs')" :class="usePage().url == '/logs/bsc' ? 'text-danger' : ''">
                    <span class="mdi mdi-arrow-right-drop-circle text-warning float-end"></span>BSC <span>({{ props.bscLogsCount }})</span></Link
                >
                <a href="javascript: void(0);"> <span class="mdi mdi-arrow-right-drop-circle text-primary float-end"></span>Availability Planner </a>

                <a href="javascript: void(0);"> <span class="mdi mdi-arrow-right-drop-circle text-success float-end"></span>Visitor Tracker </a>
                <a href="javascript: void(0);"> <span class="mdi mdi-arrow-right-drop-circle text-success float-end"></span>Supply </a>
                <a href="javascript: void(0);"> <span class="mdi mdi-arrow-right-drop-circle text-success float-end"></span>HR </a>
                <a href="javascript: void(0);"> <span class="mdi mdi-arrow-right-drop-circle text-danger float-end"></span>System Logs </a>
                 <Link :href="route('blacklist')" :class="usePage().url == '/blacklist' ? 'text-danger' : ''">
                    <span class="mdi mdi-arrow-right-drop-circle text-warning float-end"></span>Blacklisted Emails</Link
                >
            </div>
        </div>

        <b-modal v-model="state.showModal" title="New Message" centered>
            <form>
                <div class="mb-3">
                    <input type="email" class="form-control" placeholder="To" />
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Subject" />
                </div>
                <div class="mb-3">
                    <ckeditor v-model="state.editorData" :editor="editor"></ckeditor>
                </div>
            </form>
            <template v-slot:modal-footer>
                <b-button variant="secondary" @click="showModal = false">Close</b-button>
                <b-button variant="primary">
                    Send
                    <i class="fab fa-telegram-plane ms-1"></i>
                </b-button>
            </template>
        </b-modal>
    </div>
</template>
