<template>
    <div class="report-view">
        <div class="container-fluid">
            <div class="row d-flex justify-content-start mb-2">
                <div class="col-md-4">
                    <!-- <h3 style="font-weight: 500; color: #333">Invoices</h3> -->
                </div>
                <div class="col">
                    <input @input.prevent="(e) => searchInvoice(e)" placeholder="search..." class="form-control py-2" type="text" />
                </div>
                <div class="col-md-4">
                    <!-- <b-button class="w-100" variant="primary" @click="newInvoice" style="width: 100%">New Invoice</b-button> -->
                    <b-button-group class="w-100">
                        <b-button variant="primary" @click="newInvoice" style="width: 100%">Extract Invoices</b-button>

                        <b-button variant="success" @click="newInvoice" style="width: 100%">New Invoice</b-button>
                    </b-button-group>
                </div>
            </div>
            <div>
                <div class="row d-flex justify-content-start">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <a-table :columns="columns" :row-key="(record) => record.id" :data-source="invoices" :pagination="pagination" :loading="loading" @change="handleTableChange">
                                    <template #bodyCell="{ column, text, record }">
                                        <span v-if="column.dataIndex === 'invoice_number'">
                                            <div v-if="record.is_draft == 'YES'">
                                                <div style="font-size: 15px; font-weight: bold">
                                                    <!-- <a href="#" @click="editInvoice(invoice.id)"><b></b>{{invoice.invoice_number}}</a> -->
                                                    <a href="#"><b> </b>{{ record.invoice_number }}</a>
                                                    <span style="font-size: 12px">
                                                        <a href="#" @click="editInvoice(record.id)" class="text-danger">(Draft)</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div v-else>
                                                <div style="font-size: 15px; font-weight: bold">
                                                    <a href="#" @click="viewInvoiceDetails(record)"><b> </b>{{ record.invoice_number }}</a>
                                                </div>
                                            </div>
                                            <div><b>Service: </b>{{ record.service_name }}</div>
                                            <div><b>Invoice Date: </b>{{ formatters.formatJustDate(record.invoice_date) }}</div>
                                            <div v-if="record.vat_amount > 0"><b>VAT: </b>{{ formatters.numerilize(record.vat_amount, "d", 2) }}</div>
                                            <div>
                                                <span v-if="record.status != 'CLOSED'">
                                                    <span v-if="record.stamped_date != null"><b>Stamped: </b>{{ formatters.formatJustDate(record.stamped_date) }}</span>
                                                    <span v-else><b>Last Updated: </b> {{ formatters.formatJustDate(record.updated_at) }}</span>
                                                </span>
                                                <span v-else style="color: green"><b>Closed: </b> {{ formatters.formatJustDate(record.closed_date) }}</span>
                                            </div>
                                        </span>

                                        <span v-if="column.dataIndex === 'status'">
                                            <a-tag :color="['REJECTED', 'RETURNED', 'CANCELED_BY_SP'].includes(record.status) ? 'volcano' : record.status == 'CLOSED' ? 'green' : 'geekblue'">
                                                {{ getStatus(text) }}
                                            </a-tag>
                                        </span>

                                        <div v-if="column.dataIndex === 'invoice_value_amount'" style="font-size: small">
                                            <span>{{ record.invoice_currency }} {{ formatters.numerilize(record.total_amount, "d", 2) }}</span>
                                        </div>
                                        <div v-if="column.dataIndex === 'created_at'">
                                            <span>{{ formatters.formatJustDate(record.created_at) }}</span>
                                        </div>

                                        <span v-if="column.dataIndex === 'action'">
                                            <b-button-group variant="primary" v-if="record.status === 'RETURNED'">
                                                <b-dropdown @click.prevent="invoiceUrl(record)" right split text="Resubmit Invoice">
                                                    <!-- <b-dropdown-item class="text-danger" @click.prevent="invoiceUrl(record)">Re-submitted Invoice</b-dropdown-item> -->
                                                    <b-dropdown-item @click="cancelInvoiceModal(record.id)">Recall Invoice</b-dropdown-item>
                                                </b-dropdown>
                                            </b-button-group>
                                            <b-button variant="danger" v-if="record.status === 'RECEIVED'" @click="cancelInvoiceModal(record.id)"> Recall Invoice </b-button>
                                        </span>
                                    </template>
                                </a-table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <cancelModal :cancelModalValue="cancelModalValue" :cancelModalEvent="cancelModalEvent" />
    </div>
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { onMounted, ref, computed } from "vue";
import { useStore } from "vuex";
import { RequestHelper, formatters } from "@/mixins/helpers";
import cancelModal from "@/Pages/features/sp/invoice/modals/cancelModal.vue";
const newInvoice = () => {
    router.get(route("sp.create-invoice"));
};
const store = useStore();
const session = usePage().props.auth.user;

const cancelModalValue = ref(false);
const cancelModalEvent = () => {
    // cancelModalValue.value = !cancelModalValue.value;
    store.dispatch("modalStore/hideCancelInvoiceModal");
};

const cancelInvoiceModal = () => {
    cancelModalValue.value = true;
    store.dispatch("modalStore/showCancelInvoiceModal");
};

const invoiceUrl = (record) => {
    router.get(route("sp.update-invoice", record?.id));
};

const getStatus = (itemStatus) => {
    let status = "";
    switch (itemStatus) {
        case "ACCEPTED":
            status = "Received by UNICEF";
            break;
        case "CLOSED":
            status = "Closed";
            break;
        case "REJECTED":
            status = "Rejected";
            break;
        case "RETURNED":
            status = "Returned";
            break;
        case "CANCELED_BY_SP":
            status = "Recalled";
            break;
        case "RECEIVED":
            status = "Submitted";
            break;

        case "STAMPED":
            status = "Received by UNICEF";
            break;

        default:
            status = itemStatus;
            break;
    }
    return `${status}`.replaceAll("_", " ");
};

const columns = [
    {
        title: "Invoice",
        dataIndex: "invoice_number",
        key: "invoice_number",
        scopedSlots: { customRender: "invoice_number" },

        sorter: true,
        width: "35%",
    },
    {
        title: "Status",
        dataIndex: "status",
        key: "status",
        sorter: true,
        scopedSlots: { customRender: "status" },

        filters: [
            {
                text: "Closed",
                value: "CLOSED",
            },
            {
                text: "Accepted",
                value: "ACCEPTED",
            },
        ],
        width: "15%",
    },
    {
        title: "Invoice Amount",
        dataIndex: "invoice_value_amount",
        key: "invoice_value_amount",
        scopedSlots: { customRender: "invoice_value_amount" },
        sorter: true,
    },
    {
        title: "Submitted",
        dataIndex: "created_at",
        key: "created_at",
        scopedSlots: { customRender: "created_at" },
        sorter: true,
    },
    {
        title: "Action",
        key: "action",
        dataIndex: "action",
        scopedSlots: { customRender: "action" },
        width: "25%",
    },
];

const customPagination = ref({});
const invoices = ref([]);
const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;
const loading = ref(true);
const search = ref("");
const getInvoices = async (params = {}) => {
    loading.value = true;
    return RequestHelper.getRequest(
        BSC_URL + "/api/bsc-invoices/invoices/provider",
        {
            user_type: session?.user_type,
            user_id: session?.id,
            search: search.value || "",
            ...params,
        },
        session.api_token
    );
};

const fetchInvoices = async (params) => {
    getInvoices({
        per_page: 10,
        ...params,
    })
        .then((res) => {
            const results = res.data.results;
            loading.value = false;
            invoices.value = results.data;

            let paginationNew = { ...customPagination.value };
            paginationNew.total = results.total;

            customPagination.value = paginationNew;
        })
        .catch((err) => {
            console.error(err);
            invoices.value = [];

            loading.value = false;
        });
};

const searchInvoice = (e) => {
    search.value = e.target.value;
    customPagination.value.page = 1;

    fetchInvoices();
};

const handleTableChange = (pagination, filters, sorter) => {
    console.log({ pagination, filters, sorter });
    const pager = { ...pagination };
    pager.current = pagination.current;
    customPagination.value = pager;

    fetchInvoices({ per_page: customPagination.value.pageSize, page: customPagination.current, sortField: sorter.field, sortOrder: sorter.order, ...filters });
};
onMounted(() => {
    fetchInvoices();
});
</script>

<style lang="scss" scoped>
.report-view {
    min-height: 68vh;

    .ant-table-striped :deep(.table-striped) td {
        background-color: #fafafa;
        font-size: 10px;
    }

    .ant-table-striped :deep(tr) td {
        background-color: #fafafa;
        font-size: 10px;
    }
}
</style>
