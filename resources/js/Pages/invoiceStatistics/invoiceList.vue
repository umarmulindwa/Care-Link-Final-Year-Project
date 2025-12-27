<template>
    <div class="report-view">
        <a-table
            class="ant-table-striped"
            :row-class-name="(_record, index) => (index % 2 === 1 ? 'table-striped' : null)"
            :columns="columns"
            :row-key="(record) => record.id"
            :data-source="dataList"
            :pagination="pagination"
            :loading="loading"
            @change="handleTableChange"
            :fixed="'top'"
        >
            <template #bodyCell="{ column, text, record }">
                <template v-if="column.dataIndex === 'vendor_number'">
                    <div>{{ record.provider?.name }}</div>
                    <!-- <div><b>Vendor No. </b>{{ text }}</div> -->
                    <div><b>Inv No. </b>{{ record.invoice_number }}</div>
                    <!-- <div><b>Inv Date: </b>{{ formatters.formatJustDate(record.invoice_date) }}</div>  -->
                </template>
                <template v-if="column.dataIndex === 'status'">
                    <div v-if="record.invoice_updates?.length > 0">{{ getStatus(record.invoice_updates[0]["status"] != "COMPLETED" ? record.invoice_updates[0]["status"] : text) }}</div>
                    <div v-else>{{ getStatus(text) }}</div>
                </template>
                <template v-if="column.dataIndex === 'created_at'">
                    <div>
                        {{ formatters.formatJustDate(record.created_at) }}
                    </div>
                </template>
            </template>
        </a-table>
    </div>
</template>

<script setup>
import { RequestHelper, formatters } from "@/mixins/helpers";
import { onMounted, ref, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
const session = usePage().props.auth.user;
import invoiceItems from "./invoice-items.vue";
import { usePagination } from "vue-request";

const getStatus = (itemStatus) => {
    let status = "";
    switch (itemStatus) {
        case "ACCEPTED":
            status = "Accepted";
            break;
        case "CLOSED":
            status = "Closed";
            break;
        case "REJECTED":
            status = "Rejected";
            break;
        case "SUBMITTED":
            status = "Submitted";
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
        dataIndex: "vendor_number",
        sorter: true,
        width: "35%",
    },
    {
        title: "Status",
        dataIndex: "status",
        sorter: true,
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
        width: "35%",
    },
    {
        title: "Submitted",
        dataIndex: "created_at",
        sorter: true,
    },
];

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;
const dataList = ref([]);
const totalItems = ref(0);
const perPage = ref(20);
const currentPage = ref(1);
const getInvoices = async (params) => {
    console.log({ params });
    await RequestHelper.getRequest(FINANCE_URL + "/api/bsc-invoices/invoices/all", params, session.api_token)
        .then((res) => {
            const { data, total, current_page, per_page } = res.data.results;
            dataList.value = data || [];
            totalItems.value = total || 0;
            perPage.value = per_page || 10;
            currentPage.value = current_page || 10;
        })
        .catch((err) => {
            console.log({ err });
        });
};

const {
    data: dataSource,
    run,
    loading,
    current,
    totalPage,
    pageSize,
} = usePagination(getInvoices, {
    defaultParams: [
        {
            sortBy: "RECEIVED",
            perPage: 5,
        },
    ],
    formatResult: function (res) {
        console.log("formatResult", { res });
        return { data: "nothing" };
    },
    pagination: {
        currentKey: "page",
        pageSizeKey: "results",
    },
});

const queryResult = computed(() => {
    const { results } = dataSource.value != undefined ? dataSource.value.data : {};
    return results || [];
});
const pagination = computed(() => ({
    total: totalItems.value,
    // page: 5,
    // current: current.value,
    current: currentPage.value,
    pageSize: perPage.value,
}));
const handleTableChange = (pag, filters, sorter) => {
    run({
        defaultParams: [
            {
                sortBy: "RECEIVED",
            },
        ],
        results: pag.pageSize,
        page: pag?.current,
        sortField: sorter.field,
        sortOrder: sorter.order,
        ...filters,
    });
};

onMounted(async () => {
    // await getInvoices();
});
</script>

<style scoped>
.report-view {
    height: 425px;
    overflow-x: hidden;
    overflow-y: auto;
}
.ant-table-striped :deep(.table-striped) td {
    background-color: #fafafa;
    font-size: 10px;
}

.ant-table-striped :deep(tr) td {
    font-size: 10px;
}
</style>
