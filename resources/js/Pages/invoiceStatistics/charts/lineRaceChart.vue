<template>
    <div>
        <v-chart class="chart" :option="option" autoresize />
    </div>
</template>

<script setup>
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { TitleComponent, TooltipComponent, LegendComponent, GridComponent, ToolboxComponent, DataZoomComponent, DataZoomInsideComponent, DataZoomSliderComponent } from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";
import { ref, provide } from "vue";
import { LineChart } from "echarts/charts";

use([CanvasRenderer, LineChart,Linera, TitleComponent, TooltipComponent, LegendComponent, GridComponent, ToolboxComponent, DataZoomComponent, DataZoomInsideComponent, DataZoomSliderComponent]);
provide(THEME_KEY, "light");

const option = {
     tooltip: {
        trigger: "axis",
        axisPointer: {
            type: "shadow",
        },
    },
     xAxis: {
    data: ['Jane Doe', 'Patrick M', 'Akki Atem', 'Alex M', 'Enid O']
  },
  yAxis: {},
  series: [
    {
      data: [10, 22, 28, 23, 19],
          type: 'line',
        smooth: true,
      areaStyle: {}
    },
    {
      data: [25, 14, 23, 35, 10],
        type: 'line',
        smooth: true,
      areaStyle: {
        color: '#ff0',
        opacity: 0.5
      }
    }
  ]
};

</script>

<style lang="scss" scoped>

.chart {
    height: 450px;
}
</style>
