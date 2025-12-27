<template>
    <Head title="Submit Invoice" />
    <Layout>
        <div>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 font-size-18">Invoices List</h4>

                        <div>
                            <b-button @click.prevent="createInvoice" id="create_invoice_btn" size="md" variant="primary">Create Invoice</b-button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <table class="table table-striped table-centered align-middle">
                                <thead>
                                    <tr style="font-size: 16px">
                                        <th scope="col" style="width: 100px"># Decription</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Invoice Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(invoice, k) in invoices" :key="invoice.id + `_invoice_${k}`">
                                        <td>
                                            <h5 class="text-truncate font-size-14">
                                                <b>Invoice Number:</b> <a href="javascript: void(0);" class="text-primary">{{ invoice.invoice_number }}</a>
                                            </h5>

                                            <div class="text-muted mb-0"><b>Service:</b> {{ invoice.service?.commodity_title_en }}</div>
                                        </td>
                                        <td>{{ formatters.formatJustDate(invoice.created_at) }}</td>
                                        <td>
                                            <span> {{ invoice.invoice_currency }}&nbsp;{{ formatters.formatCurrency(invoice.total_amount, null) }} </span>
                                        </td>
                                        <td>
                                            <span
                                                class="font-size-12 badge"
                                                :class="{
                                                    'bg-success': `${invoice.status}` === 'CLOSED',
                                                    'bg-info': `${invoice.status}` === 'RECEIVED',
                                                    'bg-danger': ['CANCELED_BY_SP', 'DRAFT', 'REJECTED', 'RETURNED'].includes(`${invoice.status}`),
                                                    'bg-warning': !['CANCELED_BY_SP', 'DRAFT', 'REJECTED', 'CLOSED', 'RECEIVED', 'RETURNED'].includes(`${invoice.status}`),
                                                }"
                                                >{{ invoiceStatus(invoice.status) }}</span
                                            >
                                        </td>

                                        <td>
                                            <!-- <b-dropdown class="card-drop" variant="primary" right toggle-class="p-0" menu-class="dropdown-menu-end p-2">
                                                <template v-slot:button-content> Action </template>
                                                <b-dropdown-item href="javascript: void(0);">Recall</b-dropdown-item>
                                                <b-dropdown-item href="javascript: void(0);">Cancel</b-dropdown-item>
                                                <b-dropdown-item href="javascript: void(0);" v-show="['CANCELED_BY_SP', 'DRAFT', 'REJECTED'].includes(`${invoice.status}`)">Resubmit</b-dropdown-item>
                                            </b-dropdown> -->

                                            <div v-if="['RECEIVED'].includes(invoice.status)">
                                                <b-dropdown class="card-drop" variant="secondary" right toggle-class="p-1" menu-class="dropdown-menu-end p-2">
                                                    <template v-slot:button-content> Action </template>
                                                    <b-dropdown-item href="javascript: void(0);">Recall</b-dropdown-item>
                                                    <b-dropdown-item href="javascript: void(0);">Cancel</b-dropdown-item>
                                                </b-dropdown>
                                            </div>

                                            <div v-else-if="['CANCELED_BY_SP'].includes(invoice.status)">
                                                <b-button variant="info">Resubmit</b-button>
                                            </div>

                                            <div v-else-if="['DRAFT'].includes(invoice.status)">
                                                <b-button variant="info">Submit</b-button>
                                            </div>
                                            <div v-else>
                                                <b-button variant="primary">View</b-button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row" v-show="loading">
                <div class="col-12">
                    <div class="text-center my-3">
                        <a href="javascript:void(0);" class="text-success">
                            <i class="bx bx-loader bx-spin font-size-18 align-middle mr-2"></i>
                            Load more
                        </a>
                    </div>
                </div>
                <!-- end col-->
            </div>
            <!-- end row -->
        </div>
    </Layout>
</template>

<script setup>
import Layout from "@/Layouts/verticalvendor.vue";
import { RequestHelper, formatters } from "@/mixins/helpers";
import { notify } from "@/mixins/notify";
import { ref, onMounted, useAttrs, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const props = defineProps({
    auth: {
        type: Object,
        require: false,
    },
});

const createInvoice = () => {
    return router.get(route("sp.create-invoice"));
};

const invoiceStatus = (status) => {
    let val = "";
    switch (status) {
        case "DRAFT":
            val = "Draft";
            break;

        case "RECEIVED":
            val = "Submitted to UNICEF";
            break;

        case "CANCELED_BY_SP":
            val = "Recalled";
            break;
        case "RETURNED":
            val = "Return by UNICEF";
            break;

        case "CLOSED":
            val = "Completed";
            break;

        default:
            val = "Currently by UNICEF";
            break;
    }
    return val;
};

const { user } = props?.auth;

const invoices = ref([]);
const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;
const loading = ref(true);
const getInvoices = async () => {
    loading.value = true;
    RequestHelper.getRequest(
        BSC_URL + "/api/bsc-invoices/invoices/provider",
        {
            user_type: user?.user_type,
            user_id: user?.id,
        },
        user?.api_token
    )
        .then(({ data }) => {
            const results = data.results;
            loading.value = false;
            invoices.value = results;
        })
        .catch((err) => {
            console.error(err);
            invoices.value = [];

            loading.value = false;
        });
};
onMounted(() => {
    getInvoices();
});
</script>

<style lang="scss" scoped></style>
