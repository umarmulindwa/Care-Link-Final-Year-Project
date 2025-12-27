<template>

    <Head title="Beneficiary Payments" />
    <Layout>
        <PageHeader title="Beneficiary Payments" :items="items">
            <div class="row d-flex justify-content-end mb-3">
                <div class="col-md-4">
                    <input class="form-control" placeholder="Search..." />
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
                                    <span class="text-primary link-pointer" @click="goto(record.id)">{{ record.title
                                        }}</span>
                                </div>
                                <div v-if="column.dataIndex === 'ip'">
                                    {{ record.provider.name }}
                                </div>


                                <div v-if="column.dataIndex === 'beneficiaries'">
                                    {{ formatters.numerilize(text) }}
                                </div>
                                <div v-if="column.dataIndex === 'approved_date'">
                                    {{ formatters.formatDate(record.created_at) }}
                                </div>
                                <div v-if="column.dataIndex === 'status'">
                                    <div v-if="text === 'Pending Payment'">
                                        <span class="text-primary">Pending Upload of Payment Evidence</span>
                                    </div>
                                    <div v-else-if="text === 'Invoiced to UNICEF'">
                                        <span class="text-primary">Invoiced to UNICEF</span> <span>{{
                                            formatters.formatDate(record.invoice_date) }}</span>
                                    </div>
                                    <div v-else-if="text === 'Acceptable'">
                                        <span class="text-primary">Accepted By UNICEF</span> <span>{{
                                            formatters.formatDate(record.updated_at) }}</span>
                                    </div>
                                    <div v-else-if="text === 'uploaded_approved'">
                                        <span class="text-primary">Uploaded Approved</span> <span>{{
                                            formatters.formatDate('2023-06-18 09:56:18') }}</span>
                                    </div>


                                </div>
                                <div v-if="column.dataIndex === 'view_details'">
                                    <div class="d-flex">
                                        <a class="link-pointer text-primary mx-1"
                                            v-if="!['Invoiced to UNICEF', 'Acceptable'].includes(record.status)"
                                            @click.prevent="uploadPayments(record.id)"
                                            style="font-size: 18px;text-decoration: none"><i
                                                class='bx bx-paperclip'></i></a>
                                        <a class="link-pointer  mx-1"
                                            @click.prevent="getBeneficiaryDetails(record.id)"><img class="img-fluid"
                                                src="images/icons/icon.excel.2.png"
                                                style="height: 25px; width: 25px;" /></a>
                                        <b-button v-if="!['Invoiced to UNICEF', 'Acceptable'].includes(record.status)"
                                            size="sm" variant="success" class="link-pointer  mx-1"
                                            @click.prevent="invoiceUnicef(record.id)">Invoice UNICEF</b-button>
                                        <!-- <b-button-group size="sm">
                                        <b-button variant="info" class="w-100">View Details</b-button>
                                        <b-button  variant="success" class="w-100">Extract</b-button>

                                        </b-button-group> -->
                                    </div>

                                </div>
                            </template>
                        </a-table>

                    </div>
                </div>
            </div>
            <div>
                <uploadVerifiedStaffModal :uploadBeneficiaryListForm="uploadList" :groupId="groupId"
                    :uploadBeneficiaryListFormEvent="closeUploadBeneficiaryListForm" :session="session" />
            </div>
        </div>

    </Layout>
</template>

<script setup>
import { ref, useAttrs, onMounted, computed } from "vue";
import { usePage, router, Head } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import Layout from "@/Layouts/verticalvendor.vue";
import PageHeader from "@/Components/page-header.vue";
import { RequestHelper, formatters, getDownloadedFile } from "@/mixins/helpers";
import uploadVerifiedStaffModal from "@/Pages/features/beneficiaries/modals/uploadVerifiedStaffModal.vue"
import moment from "moment";

const page = usePage();
const groupId = ref(null);
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
    uploadList.value = false;
}


const columns = [
    {
        title: "List of Beneficiaries",
        dataIndex: "title",
        key: "title",
        scopedSlots: { customRender: "title" },
        sorter: true,
    },
    {
        title: "IP",
        dataIndex: "ip",
        key: "vendor_id",
        scopedSlots: { customRender: "vendor_id" },
        sorter: true,
    },
    {
        title: "Created",
        dataIndex: "created_at",
        key: "created_at",
        scopedSlots: { customRender: "created_at" },
        sorter: true,
    },
    {
        title: "Beneficiaries",
        dataIndex: "no_of_beneficiaries_approved",
        key: "no_of_beneficiaries_approved",
        scopedSlots: { customRender: "no_of_beneficiaries_approved" },
        sorter: true,
    },
    {
        title: "Approved Date",
        dataIndex: "approved_date",
        key: "approved_date",
        scopedSlots: { customRender: "approved_date" },
        sorter: true,
    },
    {
        title: "Status",
        dataIndex: "status",
        key: "status",
        scopedSlots: { customRender: "status" },
        sorter: true,
    },
    {
        title: "View Details",
        dataIndex: "view_details",
        key: "view_details",
    }
];

const dataList = ref([])

const generateRandomDate = (start, end) => {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const randomTimestamp = startDate.getTime() + Math.random() * (endDate.getTime() - startDate.getTime());
    return new Date(randomTimestamp);
};

function goto(recordId) {
    router.get(route('beneficiary-reconcile-review', recordId))
}



const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;

const getApprovedBeneficiaryGroups = async () => {
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/approved-beneficiary-payments-groups`, {}, session?.api_token).then(({ data }) => {
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
const uploadList = ref(false)
const uploadPayments = (recordKey) => {
    uploadList.value = true
    groupId.value = recordKey;
}

const getBeneficiaryDetails = async (recordKey) => {
    Swal.fire({
        title: "Downloading, please wait...",
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    try {
        const response = await RequestHelper.downloadFile(`${FINANCE_URL}/api/beneficiaries/download-verified-beneficiaries/${recordKey}`, {}, session?.api_token);
        const blob = await response.data;
        const fileName = `pending payments ${moment().format("YMD Hm")} list.xlsx`

        getDownloadedFile(blob, fileName);

        Swal.close();
    } catch (error) {

        console.error(error);
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Something went wrong!",
        });

    }

    // await RequestHelper.downloadFile(`${FINANCE_URL}/api/beneficiaries/download-verified-beneficiaries/${recordKey}`, {}, session?.api_token).then(res => {
    //     getDownloadedFile(res.blob(), `pending payments ${moment().format("YMD Hm")} list.xlsx`);
    //     Swal.close();
    // }).catch(err => {
    //     console.log(err);
    //     Swal.fire({
    //         icon: "error",
    //         title: "Oops...",
    //         text: "Something went wrong!",
    //     });
    // })

}

const invoiceUnicef = async (recordKey) => {
    router.get(route("sp.create-invoice", [{ payment_id: recordKey }]))
}

onMounted(() => {
    getApprovedBeneficiaryGroups();
})






</script>

<style lang="scss" scoped></style>
