<template>
    <Sidebar />
    <main class="dashboard-page">
      <h1>Dashboard</h1>
      <div class="cards">
        <div class="card primary-card">
          <h3>Total User</h3>
          <p>{{ totalUsers }}</p>
        </div>
        <div class="card warning-card">
          <h3>Total Penilai</h3>
          <p>{{ totalEvaluators }}</p>
        </div>
        <div class="card success-card">
          <h3>Total Seniman</h3>
          <p>{{ totalArtists }}</p>
        </div>
        <div class="card danger-card">
          <h3>Total Registrasi Individu</h3>
          <p>{{ totalIndividualRegistrations }}</p>
        </div>
        <div class="card info-card">
          <h3>Total Registrasi Kelompok</h3>
          <p>{{ totalGroupRegistrations }}</p>
        </div>
      </div>
      <!-- <div class="charts">
        <LineChart :data="userChartData" />
        <LineChart :data="individualRegistrationChartData" />
        <LineChart :data="groupRegistrationChartData" />
      </div> -->
    </main>
</template>

<script>
import { ref } from 'vue';
import Sidebar from '../components/SidebarAdmin.vue';
import LineChart from '../components/LineChart.vue';

export default {
  components: {
    Sidebar,
    LineChart
  },
  setup() {
    const totalUsers = ref(1000);
    const totalEvaluators = ref(50);
    const totalArtists = ref(200);
    const totalIndividualRegistrations = ref(500);
    const totalGroupRegistrations = ref(100);

    const userChartData = ref({
      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
      datasets: [
        {
          label: 'Users',
          data: [10, 50, 100, 200, 300, 500],
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
        },
      ],
    });

    const individualRegistrationChartData = ref({
      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
      datasets: [
        {
          label: 'Individual Registrations',
          data: [5, 20, 35, 50, 65, 80],
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
        },
      ],
    });

    const groupRegistrationChartData = ref({
      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
      datasets: [
        {
          label: 'Group Registrations',
          data: [2, 10, 15, 25, 30, 45],
          borderColor: 'rgba(255, 206, 86, 1)',
          backgroundColor: 'rgba(255, 206, 86, 0.2)',
        },
      ],
    });

    return {
      totalUsers,
      totalEvaluators,
      totalArtists,
      totalIndividualRegistrations,
      totalGroupRegistrations,
      userChartData,
      individualRegistrationChartData,
      groupRegistrationChartData,
    };
  },
};
</script>

<style scoped lang="scss">
.dashboard-container {
  display: flex;
}

.dashboard-page {
  flex: 1;
  background-color: #f5d99d;
  padding: 2rem; /* Add padding for better layout */
}

.cards {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}
.card {
  flex: 1;
  padding: 20px;
  border-radius: 8px;
  color: white;
}
.primary-card { background-color: #007bff; }
.warning-card { background-color: #ffc107; }
.success-card { background-color: #28a745; }
.danger-card { background-color: #dc3545; }
.info-card { background-color: #17a2b8; }
.charts {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
</style>
