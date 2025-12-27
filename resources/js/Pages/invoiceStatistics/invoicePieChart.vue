<template>
    <div class="report-view">
        <div>Invoice Submissions by Sections ({{ selectedPeriod }})</div>
        <pieChart v-model:chartSpecificOptions="invoicesSectionsOptions" :chartSpecificAction="invoicesSectionsAction" />
    </div>
</template>

<script setup>
import { RequestHelper, formatters } from "@/mixins/helpers";
import { onMounted, ref, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import pieChart from "./charts/pieChart.vue";

const startDate = ref(null);
const endDate = ref(null);
const selectedPeriod = ref("last 30 days");
const optionsPeriods = ref([
    { value: null, text: "Please select an option" },
    { value: "current month", text: "Current month" },
    { value: "last 30 days", text: "Last 30 days" },
    { value: "last month", text: "Last month" },
    { value: "last 3 months", text: "Last 3 months" },
    { value: "last 4 months", text: "Last 4 months" },
    { value: "last 6 months", text: "Last 6 months" },
    { value: "last 12 months", text: "Last 12 months" },
    { value: "all", text: "All" },
    { value: "custom", text: "Specify Period" },
]);

const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL;
const invoicesSectionsOptions = ref({});

const session = usePage().props.auth.user;
const invoicesSectionsAction = async () => {
    invoicesSectionsOptions.value.isLoading = true;

    await RequestHelper.getRequest(
        FINANCE_URL + "/api/bsc-reports/invoice-by-sections",
        {
            period: selectedPeriod.value || "",
            start: startDate.value || "",
            end: endDate.value || "",
        },
        session?.api_token
    )
        .then(({ data }) => {
            let results = data.results;

            let dataset = [];
            let dataValue = [];
            let series = [];
            let datasource = [];
            datasource.push(["count", "Section"]);
            results.map((ele) => {
                datasource.push([ele.itemCount, ele.itemSection]);
                dataset.push(ele.itemSection.replaceAll("_", " "));
                dataValue.push(ele.itemCount);
                series.push({
                    value: ele.itemCount,
                    name: ele.itemSection,
                });
            });

            let newOptions = {};
            if (results.length > 0) {
                newOptions = {
                    legend: {
                        orient: "horizontal",
                        bottom: "bottom",
                        data: dataset,
                    },
                    series: [
                        {
                            name: "",
                            type: "pie",
                            radius: "50%",
                            center: ["50%", "35%"],
                            data: series,
                            label: {
                                show: true,
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: "rgba(0, 0, 0, 0.5)",
                                },
                            },
                        },
                    ],
                };
            }

            invoicesSectionsOptions.value.listItems = data.results;
            Object.assign(invoicesSectionsOptions.value, newOptions);
            invoicesSectionsOptions.value.isLoading = false;
        })
        .catch((err) => {
            console.error(err);
        });
};

onMounted(async () => {
    await invoicesSectionsAction();
});
</script>

<style lang="scss" scoped>
.report-view {
    height: 425px;
    overflow-x: hidden;
    overflow-y: auto;
}
</style>
