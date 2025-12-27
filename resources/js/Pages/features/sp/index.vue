<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Dashboard</h4>

                    <div>
                        <b-button @click.prevent="createInvoice" id="create_invoice_btn" size="md" variant="primary">Create Invoice</b-button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div>
                    <div class="row d-flex">
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="col-12">
                    <div>
                        <div class="row d-flex">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Invoice Submissions</h4>
                                        <div>
                                            From: <span>{{ formatters.formatJustDate(store.state.vendorInvoiceStore.startDate) }}</span
                                            >, To:
                                            <span>{{ formatters.formatJustDate(store.state.vendorInvoiceStore.endDate) }}</span>
                                        </div>
                                        <!-- Spline Area chart -->
                                        <apexchart class="apex-charts" height="340" type="area" dir="ltr" :series="submissionChart.series" :options="submissionChart.chartOptions"></apexchart>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body vendor-logs">
                                        <h4 class="card-title mb-4">Logs</h4>
                                        <SimpleBar>
                                            <ul class="verti-timeline list-unstyled">
                                                <li v-for="(log, k) in store.state.vendorInvoiceStore.vendorLogs" :key="k + 'log'" class="event-list">
                                                    <div class="event-timeline-dot">
                                                        <i class="bx bx-right-arrow-circle font-size-18"></i>
                                                    </div>
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3"></div>
                                                        <div class="flex-grow-1">
                                                            <div>
                                                                <b>{{ log.title }}</b>
                                                                <p class="mb-0 text-muted">{{ formatters.formatFromNow(log.created_at) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            <!-- <div class="text-center mt-4">
                                                <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light btn-sm">View More <i class="mdi mdi-arrow-right ms-1"></i></a>
                                            </div> -->
                                        </SimpleBar>
                                    </div>
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, useAttrs, computed } from "vue";
import { RequestHelper, formatters } from "@/mixins/helpers";
import { router, usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";

const propsAttrs = defineProps({
    user: {
        type: Object,
    },
    url: {
        type: String,
    },
});

const createInvoice = () => {
    return router.get(route("sp.create-invoice"));
};

const invoiceSubmissions = ref(null);
const isLoading = ref(false);

const BSC_URL = import.meta.env.VITE_FINANCE_APP_URL;

const store = useStore();

const submissionChart = computed(() => {
    return {
        series: [
            {
                name: "Invoices",
                data: store.getters["vendorInvoiceStore/dataset"]["yAxis"],
            },
        ],
        chartOptions: {
            chart: {
                toolbar: {
                    show: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 3,
            },
            colors: ["#556ee6", "#34c38f"],
            xaxis: {
                // type: "datetime",
                categories: store.getters["vendorInvoiceStore/dataset"]["xAxisValue"],
            },
            grid: {
                borderColor: "#f1f1f1",
            },
            tooltip: {
                // x: {
                //     format: "dd/MM/yy HH:mm",
                // },
            },
        },
    };
});

const getChartData = async () => {
    isLoading.value = true;
    await RequestHelper.getRequest(BSC_URL + "/api/bsc-invoices/sp/invoice-submissions", {}, propsAttrs.user?.api_token)
        .then(({ data }) => {
            console.log("getChartData", data);
            invoiceSubmissions.value = data.results;
            store.dispatch("vendorInvoiceStore/updateSubmissionAction", invoiceSubmissions.value);
            isLoading.value = false;
        })
        .catch((err) => {
            console.log(err);
            isLoading.value = false;
        });
};
onMounted(() => {
    getChartData();
    store.dispatch("vendorInvoiceStore/getVendorLogs", {
        url: BSC_URL + "/api/bsc-invoices/sp/logs",
        params: {},
        token: propsAttrs.user?.api_token,
    });
});
</script>

<style lang="scss" scoped></style>
