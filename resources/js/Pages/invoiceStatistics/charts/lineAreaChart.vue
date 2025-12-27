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
import { ref, provide, onMounted,watch } from "vue";
import { LineChart } from "echarts/charts";
import _ from "lodash";

use([CanvasRenderer, LineChart, TitleComponent, TooltipComponent, LegendComponent, GridComponent, ToolboxComponent, DataZoomComponent, DataZoomInsideComponent, DataZoomSliderComponent]);
provide(THEME_KEY, "light");

const props = defineProps({
    isLoading: false,
    yearMonths: Object,
    chartSpecificOptions: {
        type: Object,
    },
    chartSpecificAction: {
        type: Function,
        default: () => {},
    },
});

const option = ref({
    isLoading: false,
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
    series: [
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
    height: 550px;
}
</style>
