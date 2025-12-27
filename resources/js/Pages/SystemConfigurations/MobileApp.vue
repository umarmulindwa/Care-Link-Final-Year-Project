<script setup>
import Layout from "../../Layouts/main.vue";
import { reactive, onMounted, computed, ref } from "vue";
import { RequestHelper, formatters } from "@/mixins/helpers";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import mobileAppFormModel from "./modals/mobileAppForm.vue";
import Swal from "sweetalert2";

//Default Layout
defineOptions({ layout: Layout });
//props
const props = defineProps({
    auth: Object,
    user: Object,
});
//data
const state = reactive({
    title: "Mobile App Downloads",
    items: [
        {
            text: "Dashboard",
            href: "/",
        },
        {
            text: "Mobile App Downloads",
            active: true,
        },
    ],
});

const appUploadList = ref([]);
const getMobileUploads = async () => {
    Swal.fire({
        text: "Processing, please wait...",
        didOpen: () => {
            Swal.showLoading();
        },
    });
    await RequestHelper.getRequest("/api/mobile-api/download-data", {}, props.user.api_token)
        .then(({ data }) => {
            appUploadList.value = data.results;
            Swal.close();
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
        });
};

const downloadAPK = async () => {
    await RequestHelper.downloadFile("/api/mobile-api/download-apk", {}, props.user.api_token)
        .then(({ data }) => {
            console.log(typeof data);
            const url = window.URL.createObjectURL(new Blob([data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", data.name);
            document.body.appendChild(link);
            link.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(link);
            Swal.close();
        })
        .catch((err) => {
            console.error(err);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
        });
};

const mobileAppForm = ref(false);
const updateNewApk = () => {
    mobileAppForm.value = true;
};

const mobileAppFormEvent = async () => {
    mobileAppForm.value = !mobileAppForm.value;
    router.get(route("mobile-app"));
};

//methods
//mounted
onMounted(async () => {
    document.body.classList.remove("side-bg");
    await getMobileUploads();
});

//computed
</script>

<template>
    <Head title="System Configuration" />

    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center mb-5 mt-4">
                    <h4>{{ state.title }}</h4>
                </div>
            </div>
        </div>
        <div class="row d-flex flex-column justify-content-between">
            <div class="row d-flex justify-content-end m-0 p-0 w-full">
                <div class="col-md-3">
                    <b-button variant="primary" @click.prevent="updateNewApk" class="w-100">Add New</b-button>
                </div>
            </div>
            <form id="checked_invoice_form" class="table-responsive mb-0 custom-table-striped m-0 p-0 w-full">
                <table class="align-middle font-size-14">
                    <thead class="text-left">
                        <tr>
                            <th>FILE DATE</th>
                            <th>VERSION UPDATES</th>
                            <th>VERSION</th>
                            <th>SIZE</th>
                            <th>DOWNLOAD</th>
                        </tr>
                    </thead>
                    <tbody class="text-left">
                        <tr v-for="(item, i) in appUploadList" :key="`mobile-file-${i}`">
                            <td>{{ item.date_formatted }}</td>
                            <td><div v-html="item.comment"></div></td>
                            <td>{{ item.version }}</td>
                            <td>{{ item.size }}</td>
                            <td>
                                <a :href="item.file_path" class="table-link">APK</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <mobileAppFormModel :session="props.user" v-model:mobileAppForm="mobileAppForm" :mobileAppFormEvent="mobileAppFormEvent" />
    </div>
</template>
