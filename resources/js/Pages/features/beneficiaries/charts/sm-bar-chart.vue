<!-- src/components/BarChart.vue -->
<template>
    <div v-if="isLoading"><span class="bx bx-spin-hover bx-rotate-90"></span></div>
    <div v-else ref="chart" :style="{ width: '100%', height: '100%' }"></div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import * as echarts from 'echarts';
import { RequestHelper } from '@/mixins/helpers';
import { usePage } from '@inertiajs/vue3';

const chart = ref(null);
const props = defineProps({
    masterlist_id: {
        type: Number,
        required: true,

    },

});
const session = usePage().props.auth.user;
const FINANCE_URL = import.meta.env.VITE_FINANCE_APP_URL
const dataItems = ref([]);
const isLoading = ref(false);
const getAttendees = async () => {
    dataItems.value = [];
    isLoading.value = true;
    await RequestHelper.getRequest(`${FINANCE_URL}/api/beneficiaries/attendance/statistics/${props.masterlist_id}`, {}, session?.api_token).then(({ data }) => {
        let items = data.results;
        Array.from(items).map((item) => {
            item.value = item.total_attendants;
            item.label = item.month;
            dataItems.value.push(item);

        })
        isLoading.value = false;

    });
}

const plotGraph = () => {
    const myChart = echarts.init(chart.value);
    let valueList = dataItems.value.map(x => x.value);
    let xList = dataItems.value.map(x => x.label);

    let option = {
        tooltip: {},
        xAxis: {
            axisLabel: {
                interval: 0,
                rotate: 30,
            },
            data: xList
        },
        yAxis: {
            show: true,
            name: 'Attendance',
        },
        series: [
            {
                name: 'beneficiaries',
                type: 'bar',
                color: '#4362ef',
                data: valueList,
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: "rgba(0, 0, 0, 0.5)",
                    },
                },
            }
        ],
        animationDuration: 0,
        animationDurationUpdate: 3000,
        animationEasing: "linear",
        animationEasingUpdate: "linear",
    };

    myChart.setOption(option);
}

onMounted(async () => {
    await getAttendees();
    await plotGraph();
});
</script>

<style scoped>
/* Add any styles for your chart here */
</style>
