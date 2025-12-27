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
                                    <a class="text-secondary" :href="FINANCE_URL + '/templates/beneficiaries_list.xlsx'"
                                        target="_blank" rel="noopener noreferrer">
                                        <img src="/images/icons/icon.excel.2.png" style="height: 20px;width: 20px;"
                                            class="img-fluid" alt="" />
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
                <div class="">
                    <div class="">


                        <b-card v-for="(record,i) in dataList" :key="'ml'+i" class="shadow-md  mb-3 p-3">
                            <b-card-text>
                                <div class="row d-flex justify-content-between mb-2">
                                    <div>
                                        <span class="julius-font-size-medium text-grey">{{ record.title }}</span><br>
                                        <span class="julius-font-size-normal text-grey">{{formatters.formatJustDate(record.created_at)}}</span>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-between"  style="border:#ccc 1px solid;padding: 10px;border-radius: 10px;">
                                    <div class="col-md-2 center-content" >

                                            <div class="child-content  ">
                                                <span class="fa fa-users julius-font-size-medium text-grey" ></span><sub><span
                                                    class="fa fa-check-circle julius-font-size-normal text-success"></span></sub>
                                            </div>



                                    </div>
                                    <div class="col-md-2 center-content">
                                        <div class="child-content pointer-link" @click.prevent="goto(record.id)">
                                            <span class="julius-font-size-medium text-purple">{{record.no_of_beneficiaries}}</span><sub><span
                                                class="julius-font-size-normal text-purple">Beneficiaries</span></sub>
                                        </div>

                                    </div>
                                    <div class="col-md-5">
                                        <div style="height: 250px;">
                                            <sm-bar-chart :masterlist_id="record.id" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="pointer-link" @click.prevent="openUploadBeneficiaryListForm"
                                            style="display: flex;flex-direction: row; gap:10px;grid-template-columns: auto auto;">
                                            <div style="border-right: 1px solid #ccc;  padding: 0 10px;"><span
                                                    class="fa fa-cloud-upload-alt julius-font-size-medium text-grey"></span>
                                            </div>
                                            <div class="julius-font-size-normal text-purple py-2 ">Upload New
                                                Beneficiaries</div>
                                        </div>
                                        <div @click.prevent="goto(record.id)" class="pointer-link"
                                            style="display: flex;flex-direction: row; gap:10px;grid-template-columns: auto auto;">
                                            <div style="border-right: 1px solid #ccc;  padding: 0 11px;"><span
                                                    class="fa fa-edit julius-font-size-medium text-grey"></span></div>
                                            <div class="julius-font-size-normal text-purple py-2" >Edit Beneficiaries
                                            </div>
                                        </div>
                                        <div
                                            @click.prevent="goto(record.id)" class="pointer-link"
                                            style="display: flex;flex-direction: row; gap:10px;grid-template-columns: auto auto;">
                                            <div style="border-right: 1px solid #ccc;  padding: 0 13.5px;"><span
                                                    class="fa fa-trash-alt julius-font-size-medium text-grey"></span>
                                            </div>
                                            <div class="julius-font-size-normal text-purple py-2">Delete Beneficiaries
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row d-flex justify-content-between mb-2">
                                    <div class="col-12 text-grey my-2">
                                        <span class="julius-font-size-normal">A Master list is uploaded by an IP once and thereafter, may be updated to reflect new beneficiaries, departed beneficiaries and, or changes in the details of a beneficiary.</span>
                                    </div>
                                    <div class="col-12 text-purple my-2">
                                        <div style="gap: 10px;grid-template-columns: auto auto">
                                        <span class="julius-font-size-normal pointer-link" @click.prevent="()=>router.get(route('view-beneficiary-attendance',record.id))">View Attendance <span class="fa fa-arrow-right"></span></span>&nbsp;&nbsp;&nbsp;<span class="julius-font-size-normal">|</span>&nbsp;&nbsp;&nbsp;
                                        <span class="julius-font-size-normal pointer-link"  @click.prevent="uploadAttendance(record.id)">Upload Attendance  <span class="fa fa-arrow-up"></span></span>

                                        </div>
                                    </div>
                                </div>
                            </b-card-text>
                        </b-card>
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

import { ref, useAttrs, onMounted, computed, reactive } from "vue";
import { usePage, router, Head, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { RequestHelper, formatters, getDownloadedFile } from "@/mixins/helpers";
import uploadFormModal from "@/Pages/features/beneficiaries/modals/uploadForm.vue"
import uploadAttendanceModal from "@/Pages/features/beneficiaries/modals/uploadAttendanceModal.vue"
import moment from "moment";

import smBarChart from "@/Pages/features/beneficiaries/charts/sm-bar-chart.vue"
import { format } from "d3";

const page = usePage();
const session = page.props.auth.user;
const items = ref([
    { href: "/", text: 'Home' },
    { active: true, text: 'Payments' },
]);

const dataList = ref([]);



const uploadBeneficiaryListForm = ref(false);

const openUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = true;
}
const closeUploadBeneficiaryListForm = () => {
    uploadBeneficiaryListForm.value = false;
}


const uploadAttendanceList = ref(false);
const closeUploadAttendanceList = () => {
    selectedGroupId.value = null;
    uploadAttendanceList.value = false;
}

const downloadAttendanceTemplate = async () => {
    await RequestHelper.downloadFile(FINANCE_URL + `/api/beneficiaries/attendance/download-template`, {}, session.api_token).then(({ data }) => {
        getDownloadedFile(data, `attendance-${moment((new Date()).toDateString()).format('Y-MMM')}-${Math.random()}.xlsx`)
    })
}
const selectedGroupId = ref(null)
const uploadAttendance = (recordId) => {
    selectedGroupId.value = recordId;
    uploadAttendanceList.value = true;
}

const filterList = (e) => {
    console.log("filterList", e.target.value);
}

const goto = (group_id) => {
    router.get(route("beneficiary-payments-details", group_id))
}

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const getBeneficiaryGroups = async () => {
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/list-beneficiaries-groups`, {}, session?.api_token).then(({ data }) => {
        dataList.value = data.results.map(item => {
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

onMounted(() => {
    getBeneficiaryGroups();
});

</script>

<style lang="scss" scoped>
.center-content{
   position: relative;
   .child-content{
     position: absolute;
     top: 50%;
     left: 50%;
     transform: translate(-50%, -50%);
     background: #fff;
     padding: 10px;
     border-radius: 5px;
    //  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
     z-index: 1;
   }
}
</style>
