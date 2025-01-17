<template>
    <div class="admin-page">
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
            <p>{{ totalPenilai }}</p>
            </div>
            <div class="card success-card">
            <h3>Total Seniman</h3>
            <p>{{ totalSeniman }}</p>
            </div>
            <div class="card danger-card">
            <h3>Total Registrasi Individu</h3>
            <p>{{ totalIndividu }}</p>
            </div>
            <div class="card info-card">
            <h3>Total Registrasi Kelompok</h3>
            <p>{{ totalKelompok }}</p>
            </div>
        </div>
        <div class="charts">
            <UserChart />
            <RegistrasiChart />
        </div>
        </main>
    </div>

  </template>

  <script>
  import { ref, onMounted } from 'vue';
  import axios from '../services/api.js';
  import Sidebar from '../components/SidebarAdmin.vue';
  import UserChart from '../components/UserChart.vue';
  import RegistrasiChart from '../components/RegistrasiChart.vue';

  export default {
    components: {
      Sidebar,
      UserChart,
      RegistrasiChart,
    },
    setup() {
      const totalUsers = ref(0);
      const totalPenilai = ref(0);
      const totalSeniman = ref(0);
      const totalIndividu = ref(0);
      const totalKelompok = ref(0);

      onMounted(async () => {
        try {
          const userResponse = await axios.get('/total-user');
          totalUsers.value = userResponse.data.total_users;

          const penilaiResponse = await axios.get('/total-penilai');
          totalPenilai.value = penilaiResponse.data.total_penilais;

          const senimanResponse = await axios.get('/total-seniman');
          totalSeniman.value = senimanResponse.data.total_senimans;

          const individuResponse = await axios.get('/total-registrasi-individu');
          totalIndividu.value = individuResponse.data.total_individus;

          const kelompokResponse = await axios.get('/total-registrasi-kelompok');
          totalKelompok.value = kelompokResponse.data.total_kelompoks;
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      });

      return {
        totalUsers,
        totalPenilai,
        totalSeniman,
        totalIndividu,
        totalKelompok,
      };
    },
  };
  </script>

  <style scoped lang="scss">
  .admin-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .dashboard-container {
    display: flex;
  }

  .dashboard-page {
    flex: 1;
    background-color: #f5d99d;
    padding: 2rem;
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
    gap: 20px;
  }

  .charts > * {
    flex: 1;
  }
  </style>
