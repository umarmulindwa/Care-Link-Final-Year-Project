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
import { BarChart } from "echarts/charts";
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    ToolboxComponent,
    DataZoomComponent,
    DataZoomInsideComponent,
    DataZoomSliderComponent,
    VisualMapComponent,
    DatasetComponent,
} from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";
import { ref, provide, watch, onMounted } from "vue";

const props = defineProps({
    chartSpecificOptions: {
        type: Object,
    },
    chartSpecificAction: {
        type: Function,
        default: () => {},
    },
});

use([
    CanvasRenderer,
    BarChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    ToolboxComponent,
    DataZoomComponent,
    DataZoomInsideComponent,
    DataZoomSliderComponent,
    VisualMapComponent,
    DatasetComponent,
]);

provide(THEME_KEY, "light");

const option = ref({
    isLoading: false,
    tooltip: {
        trigger: "axis",
        axisPointer: {
            type: "shadow",
        },
    },
    dataset: {
        source: [
            // ["score", "amount", "product"],
            // [89.3, 58212, "Matcha Latte"],
            // [57.1, 78254, "Milk Tea"],
            // [74.4, 41032, "Cheese Cocoa"],
            // [50.1, 12755, "Cheese Brownie"],
            // [43.7, 20145, "Matcha Cocoa"],
            // [68.1, 79146, "Tea"],
            // [19.6, 91852, "Orange Juice"],
            // [10.6, 101852, "Lemon Juice"],
            // [32.7, 20112, "Walnut Brownie"],
        ],
    },
    grid: { containLabel: true },
    xAxis: { name: "score" },
    yAxis: {
        axisLabel: {
            interval: 0,
            rotate: 30,
        },
        type: "category",
        inverse: true,
        animationDuration: 300,
        animationDurationUpdate: 300,
    },
    visualMap: {
        show: false,
        orient: "horizontal",

        min: 10,
        max: 100,
        text: ["High Score", "Low Score"],
        // Map the score column to color
        dimension: 0,
        inRange: {
            //   color: ['#FF0000','#FFA500','#FFFF00','#00FF00','#0000FF','#4B0082','#8A2BE2']
            color: ["#1b5e20", "#2e7d32", "#c6ff00", "#fbc02d", "#ffb74d", "#f57c00", "#d32f2f"], //small is good
            //   color: ['#d32f2f','#f57c00','#ffb74d','#fbc02d','#c6ff00','#2e7d32','#1b5e20'] //large is good
        },
    },
    series: [
        {
            type: "bar",
            realtimeSort: true,
            encode: {
                // Map the "amount" column to X axis.
                x: "score",
                // Map the "product" column to Y axis
                y: "product",
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
    animationDuration: 0,
    animationDurationUpdate: 3000,
    animationEasing: "linear",
    animationEasingUpdate: "linear",
});

watch(props, (newValue, oldValue) => {
    if (newValue != null) {
        Object.assign(option.value, props.chartSpecificOptions);
    }
});
onMounted(async () => {
    Object.assign(option.value, props.chartSpecificOptions);
});
/**
 * deep red #d32f2f
 * deep orange #f57c00
 * light orange '#ffb74d'
 * deep yellow #fbc02d
 * light green #c6ff00
 * deep green  #388e3c
 *
 *
 */
</script>

<style lang="scss" scoped>
.chart {
    height: 450px;
}
</style>
