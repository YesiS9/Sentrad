<template>
    <div style="width: 500px; height: 300px;">
      <apexchart type="bar" :options="chartOptions" :series="chartData" />
    </div>
  </template>

  <script>
  import axios from '../services/api.js';
  import VueApexCharts from 'vue3-apexcharts';

  export default {
    components: {
      apexchart: VueApexCharts,
    },
    data() {
      return {
        chartOptions: {
          chart: {
            id: 'user-chart',
            background: '#fff',
            height: 200,
            width: '100%',
          },
          title: {
            text: 'User Count by Role',
            align: 'center',
            style: {
              fontSize: '15px',
              fontWeight: 'bold',
              color: '#333',
            },
          },
          xaxis: {
            categories: ['Seniman', 'Penilai'],
            title: {
              text: 'Roles',
            },
          },
          yaxis: {
            title: {
              text: 'User Count',
            },
          },
          grid: {
            borderColor: '#e1e1e1',
          },
          plotOptions: {
            bar: {
              distributed: true,
            },
          },
          colors: ['#28a745', '#ffc107'],
        },
        chartData: [
          {
            name: 'User Count',
            data: [0, 0],
          },
        ],
      };
    },
    async mounted() {
      try {
        const response = await axios.get('/user-count');
        console.log(response.data);
        this.chartData = response.data.series;
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },
  };
  </script>
