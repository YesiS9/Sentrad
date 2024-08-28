<!-- src/components/LineChart.vue -->
<template>
    <div>
      <line-chart :chart-data="data" />
    </div>
  </template>

  <script>
  import { defineComponent } from 'vue';
  import { Line } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement } from 'chart.js';

  ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

  export default defineComponent({
    name: 'LineChart',
    components: {
      Line,
    },
    props: {
      data: {
        type: Object,
        required: true,
      },
    },
  });
  </script>
