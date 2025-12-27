<template>
    <div>
        <v-chart class="chart" :option="option" autoresize />
    </div>
</template>

<script setup>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { PieChart } from "echarts/charts";
import { TitleComponent, TooltipComponent, LegendComponent } from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";
import { ref, provide, onMounted, watch } from "vue";

const props = defineProps({
    chartSpecificOptions: {
        type: Object,
    },
    chartSpecificAction: {
        type: Function,
        default: () => {},
    },
});

use([CanvasRenderer, PieChart, TitleComponent, TooltipComponent, LegendComponent]);

provide(THEME_KEY, "light");

const option = ref({
    tooltip: {
        trigger: "item",
        formatter: "{a} <br/>{b} : {c} ({d}%)",
    },
    legend: {
        orient: "horizontal",
        bottom: "bottom",
        data: [],
    },
    series: [
        {
            name: "",
            type: "pie",
            radius: "55%",
            center: ["50%", "60%"],
            data: [
                // { value: 335, name: 'Finance' },
                // { value: 310, name: 'Supply and Logistics' },
                // { value: 234, name: 'Child Care' },
                // { value: 135, name: 'ICT UNIT' },
                // { value: 1548, name: 'Administration' },
            ],
            emphasis: {
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: "rgba(0, 0, 0, 0.5)",
                },
            },
        },
    ],
});

watch(props, (newValue, oldValue) => {
    if (newValue != null) {
        Object.assign(option.value, props.chartSpecificOptions);
    }
});
onMounted(async () => {
    Object.assign(option.value, props.chartSpecificOptions);
});
</script>

<style lang="scss" scoped>
.chart {
    height: 425px;
}
</style>
