<template>

    <Head title="Beneficiary Payments" />
    <Layout>
        <PageHeader title="Beneficiary Payments" :items="items">
            <div class="row d-flex justify-content-end mb-3">
                <div class="col-md-4">
                    <input type="search" class="form-control p-2" placeholder="search..." name="search"
                        @input="e => filterList(e)">
                </div>
                <div class="col-md-3">
                    <b-button-group class="w-100">

                        <b-button @click.prevent="openUploadBeneficiaryListForm" variant="success">Upload Master
                            List</b-button>
                        <b-dropdown variant="success">
                            <template #button-content>
                                <b-icon icon="filter" />
                            </template>
                            <b-dropdown-menu>
                                <b-dropdown-item>
                                    <a :href="FINANCE_URL + '/templates/beneficiaries_list.xlsx'" target="_blank" class="text-secondary" rel="noopener noreferrer">
                                        Download Master Template
                                    </a>
                                </b-dropdown-item>
                            </b-dropdown-menu>
                        </b-dropdown>
                    </b-button-group>
                </div>
            </div>
        </PageHeader>
        <div class="row d-flex justify-content-center">
            <div class="col-12" style="min-height: 80vh;">
                <div class="card shadow-md">
                    <div class="card-body p-0">
                        <a-table :columns="columns" :data-source="dataList">
                            <template #bodyCell="{ column, text, record }">
                                <div v-if="column.dataIndex === 'title'">
                                    <Link @click.prevent="goto(record.id)">{{ record.title }}</Link>
                                </div>
                                <div v-if="column.dataIndex === 'created_at'">
                                    {{ formatters.formatJustDate(text) }}
                                </div>
                                <div v-if="column.dataIndex === 'beneficiaries'">
                                    {{ formatters.numerilize(text) }}
                                </div>
                                <div v-if="column.dataIndex === 'status'">
                                    <div><a-tag
                                            :color="['pending'].includes(text) ? 'volcano' : (['CLOSED'].includes(text) ? 'green' : 'geekblue')">{{
                                            text }}</a-tag></div>
                                </div>
                                <div v-if="column.dataIndex === 'attendance'" class="text-primary" style="text-decoration: none;">
                                     <a class="pointer-link" :href="route('view-beneficiary-attendance',record.id)">View</a>{{ ' | ' }} <a class="pointer-link" @click.prevent="uploadAttendance(record.id)">Upload</a>{{ ' | ' }} <a  class="pointer-link"  @click.prevent="downloadAttendanceTemplate">Download Template</a>
                                </div>
                            </template>
                        </a-table>

                    </div>
                </div>
            </div>
            <div>
                <uploadFormModal :uploadBeneficiaryListForm="uploadBeneficiaryListForm"
                    :uploadBeneficiaryListFormEvent="closeUploadBeneficiaryListForm" :session="session" />
            </div>
            <div>
                <uploadAttendanceModal :uploadBeneficiaryListForm="uploadAttendanceList" :groupId="selectedGroupId"
                    :uploadBeneficiaryListFormEvent="closeUploadAttendanceList" :session="session" />
            </div>
        </div>

    </Layout>
</template>

<script setup>
import { ref, useAttrs, onMounted, computed } from "vue";
import { usePage, router, Head,Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { RequestHelper, formatters,getDownloadedFile } from "@/mixins/helpers";
import uploadFormModal from "@/Pages/features/beneficiaries/modals/uploadForm.vue"
import uploadAttendanceModal from "@/Pages/features/beneficiaries/modals/uploadAttendanceModal.vue"
import moment from "moment";

const page = usePage();
const session = page.props.auth.user;
const items = ref([
    { href: "/", text: 'Home' },
    { active: true, text: 'Payments' },
]);

const uploadBeneficiaryListForm = ref(false);

const openUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = true;
}
const closeUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = false;
}


const uploadAttendanceList = ref(false);
const closeUploadAttendanceList = ()=>{
    selectedGroupId.value = null;
    uploadAttendanceList.value = false;
}

const downloadAttendanceTemplate =async ()=>{
    await RequestHelper.downloadFile(FINANCE_URL + `/api/beneficiaries/attendance/download-template`,{},session.api_token).then(({data})=>{
        getDownloadedFile(data,`attendance-${moment((new Date()).toDateString()).format('Y-MMM')}-${Math.random()}.xlsx`)
    })
}
const selectedGroupId = ref(null)
const uploadAttendance = (recordId)=>{
    selectedGroupId.value = recordId;
    uploadAttendanceList.value = true;
}

const filterList = (e) => {
    console.log("filterList", e.target.value);
}

const goto = (group_id)=>{
    router.get(route("beneficiary-payments-details",group_id))
}

const columns = [
    {
        title: "Master List",
        dataIndex: "title",
        key: "title",
        scopedSlots: { customRender: "title" },
        sorter: true,
        width: "20%",
    },
    {
        title: "Submitted At",
        dataIndex: "created_at",
        key: "created_at",
        scopedSlots: { customRender: "created_at" },
        sorter: true,
        width: "15%",
    },
    {
        title: "Beneficiaries",
        dataIndex: "no_of_beneficiaries",
        key: "no_of_beneficiaries",
        scopedSlots: { customRender: "no_of_beneficiaries" },
        sorter: true,
        width: "15%",
    },
    {
        title: "Status",
        dataIndex: "status",
        key: "status",
        scopedSlots: { customRender: "status" },
        sorter: true,
        width: "20%",
    },

    {
        title: "Attendance",
        dataIndex: "attendance",
        key: "attendance",
        width: "30%",
    }
];

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const getBeneficiaryGroups = async () => {
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/list-beneficiaries-groups`, {}, session?.api_token).then(({ data }) => {
        dataList.value = data.results.map(item=>{
            item.key = item.id;
            return item;
        });
    }).catch(err => {
        console.log(err);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });
    })
}

const downloadBeneficiaryTemplate = async () => {
    Swal.fire({
        text: "Processing, please wait...",
        didOpen: () => {
            Swal.showLoading();
        },
    });
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiary-templates/beneficiaries`, {}, session?.api_token).then(({ data }) => {
        console.log({data});

        // window.location.href = data.public_path;
        // const blob = new Blob([data], { type: data.type });
        // const blobURL = window.URL.createObjectURL(blob);
        // const date = new Date();
        // let output = `${date.getDate()}${date.toLocaleString('default', { month: 'long' })}${date.getFullYear()}`;
        // const filename  = `Templates Beneficiaries ${output} list.xlsx`;


        // const a = document.createElement('a');
        // a.href = blobURL;
        // a.download = filename;
        // document.body.appendChild(a);
        // a.click();

        // document.body.removeChild(a);
        // window.URL.revokeObjectURL(blobURL);
        Swal.close();
    }).catch(err => {
        console.log(err);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });
    })
}

const dataList = ref([]);
onMounted(() => {
    getBeneficiaryGroups();
});
// [
//         {
//             key: "1",
//             title: "List of Beneficiaries",
//             created_at: "2021-07-20 16:45:00",
//             beneficiaries: "10",
//             status: "Pending",
//             view_details: "View Details",
//         },
//         {
//             key: "2",
//             title: "List of Beneficiaries",
//             created_at: "2013-07-20 09:15:00",
//             beneficiaries: "10",
//             status: "Done",
//             view_details: "View Details",
//         },
//         {
//             key: "3",
//             title: "List of Beneficiaries",
//             created_at: "2021-07-20 12:00:00",
//             beneficiaries: "10",
//             status: "Reviewed",
//             view_details: "View Details",
//         },
//         {
//             key: "4",
//             title: "List of Beneficiaries",
//             created_at: "2021-07-20 12:00:00",
//             beneficiaries: "10",
//             status: "Pending",
//             view_details: "View Details",
//         }

//     ];










</script>

<style lang="scss" scoped></style>
