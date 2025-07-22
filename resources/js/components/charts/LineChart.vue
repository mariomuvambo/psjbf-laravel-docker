<template>
  <Line v-if="chartData" :data="chartData" :options="options" />
</template>

<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from 'chart.js';
import { Line } from 'vue-chartjs';

ChartJS.register(LineElement, PointElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps({
  chartData: {
    type: Object,
    required: true
  }
});

const options = {
  responsive: true,
  maintainAspectRatio: false
};
</script>

<style scoped>
canvas {
  max-height: 300px;
}
</style>
