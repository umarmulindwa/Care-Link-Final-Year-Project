<template>
    <div>
        <div v-if="option.isLoading">
            <p class="font-size-24 text-info">Loading...</p>
        </div>
        <div v-else>
            <v-chart class="chart" :option="option" autoresize />
        </div>
    </div>
</template>

<script setup>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { TitleComponent, TooltipComponent, LegendComponent, GridComponent, ToolboxComponent, DataZoomComponent, DataZoomInsideComponent, DataZoomSliderComponent } from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";
import { ref, provide, onMounted, watch } from "vue";
import { LineChart } from "echarts/charts";
import _, { isNumber } from "lodash";

use([CanvasRenderer, LineChart, TitleComponent, TooltipComponent, LegendComponent, GridComponent, ToolboxComponent, DataZoomComponent, DataZoomInsideComponent, DataZoomSliderComponent]);
provide(THEME_KEY, "light");

const props = defineProps({
    isLoading: false,
    yearMonths: Object,
});

const option = ref({
    isLoading: false,
    legend: {
                orient: "horizontal",
                 top: "top",
                data: [],
            },
    tooltip: {
        trigger: "axis",
        axisPointer: {
            type: "shadow",
        },
    },
    xAxis: {
        data: [],
    },
    yAxis: {},
    series: [],
});

const chartData = ref({});
const chartXData = ref([]);
const yData = ref({});
const groupedData = ref({});
const groupedData2 = ref([]);
onMounted(async () => {
    chartData.value["data"] = [
        {
            itemCount: 3,
            itemStaff: null,
            year: 2023,
            month: 12,
        },
        {
            itemCount: 16,
            itemStaff: "Trail Analytics Technical Support",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 21,
            itemStaff: "Julius Jack NGALAMU",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 30,
            itemStaff: "Mohamad Roman GARWAN",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 14,
            itemStaff: "William Lekson Dramoyo Okulako",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 25,
            itemStaff: "Asse Jean-Claude KAMELAN",
            year: 2023,
            month: 12,
        },

        {
            itemCount: 4,
            itemStaff: null,
            year: 2023,
            month: 12,
        },
        {
            itemCount: 10,
            itemStaff: "Trail Analytics Technical Support",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 8,
            itemStaff: "Julius Jack NGALAMU",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 8,
            itemStaff: "Mohamad Roman GARWAN",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 12,
            itemStaff: "William Lekson Dramoyo Okulako",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 19,
            itemStaff: "Asse Jean-Claude KAMELAN",
            year: 2023,
            month: 12,
        },

        {
            itemCount: 3,
            itemStaff: null,
            year: 2023,
            month: 12,
        },
        {
            itemCount: 16,
            itemStaff: "Trail Analytics Technical Support",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 21,
            itemStaff: "Julius Jack NGALAMU",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 30,
            itemStaff: "Mohamad Roman GARWAN",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 14,
            itemStaff: "William Lekson Dramoyo Okulako",
            year: 2023,
            month: 12,
        },
        {
            itemCount: 25,
            itemStaff: "Asse Jean-Claude KAMELAN",
            year: 2023,
            month: 12,
        },

        {
            itemCount: 14,
            itemStaff: null,
            year: 2024,
            month: 1,
        },
        {
            itemCount: 17,
            itemStaff: "Trail Analytics Technical Support",
            year: 2024,
            month: 1,
        },
        {
            itemCount: 6,
            itemStaff: "Julius Jack NGALAMU",
            year: 2024,
            month: 1,
        },
        {
            itemCount: 2,
            itemStaff: "Mohamad Roman GARWAN",
            year: 2024,
            month: 1,
        },
        {
            itemCount: 15,
            itemStaff: "William Lekson Dramoyo Okulako",
            year: 2024,
            month: 1,
        },
        {
            itemCount: 10,
            itemStaff: "Asse Jean-Claude KAMELAN",
            year: 2024,
            month: 1,
        },

        {
            itemCount: 4,
            itemStaff: null,
            year: 2024,
            month: 2,
        },
        {
            itemCount: 1,
            itemStaff: "Trail Analytics Technical Support",
            year: 2024,
            month: 2,
        },
        {
            itemCount: 1,
            itemStaff: "Julius Jack NGALAMU",
            year: 2024,
            month: 2,
        },
        {
            itemCount: 4,
            itemStaff: "Mohamad Roman GARWAN",
            year: 2024,
            month: 2,
        },
        {
            itemCount: 1,
            itemStaff: "William Lekson Dramoyo Okulako",
            year: 2024,
            month: 2,
        },
        {
            itemCount: 2,
            itemStaff: "Asse Jean-Claude KAMELAN",
            year: 2024,
            month: 2,
        },
        {
            itemCount: 3,
            itemStaff: "Albert Nettey",
            year: 2024,
            month: 3,
        },
        {
            itemCount: 1,
            itemStaff: null,
            year: 2024,
            month: 4,
        },
        {
            itemCount: 2,
            itemStaff: "Albert Nettey",
            year: 2024,
            month: 4,
        },
    ];

    const monthsList = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    groupedData.value = _.groupBy(chartData.value["data"], "itemStaff");

    let xList = Object.keys(props.yearMonths).map((e) => {
        return { year: parseInt(`${e}`.split("-")[0]), month: parseInt(`${e}`.split("-")[1]) };
    });
    let seriesName = [];
    Object.keys(groupedData.value).map((e, i) => {
        let itemData = [];
        seriesName[i] = e == 'null' ? 'No Receiving Officer' :e;
        for (let index = 0; index < groupedData.value[e].length; index++) {
            const element = groupedData.value[e][index];
           for (let j = 0; j < xList.length; j++) {
               const u = xList[j];


               if (element.month == u.month && element.year == u.year) {
                   itemData[j] = element['itemCount'];
               } else {
                itemData[j] = itemData[j] > 0?itemData[j]:0;
               }
           }

        }
        Object.assign(groupedData.value[e], { data: itemData })
        groupedData2.value[i] = {
        name: e == 'null' ? 'No Receiving Officer' :e,
        type: "line",
        smooth: true,
        data: itemData,
        };
    });
    chartXData.value = Object.keys(props.yearMonths).map((item, i) => {
        let monthName = `${item}`.split("-");
        let monthNum = parseInt(monthName[1]);
        const yearMonth = monthName[0];
        const newMonth = monthsList[monthNum];
        return `${yearMonth}-${newMonth}`;
    });

    option.value.xAxis.data = chartXData.value;
    option.value.legend.data = seriesName;
    option.value.series = groupedData2.value;
});
</script>

<style lang="scss" scoped>
.chart {
    height: 550px;
}
</style>
