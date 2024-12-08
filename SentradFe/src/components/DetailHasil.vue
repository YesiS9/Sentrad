<template>
    <div class="page">
      <Sidebar />
      <main class="penilaian-detail" v-bind="$attrs">
        <div class="card penilaian-detail-card" v-if="penilaianKarya">
          <div class="card-header">
            <h2>Detail Penilaian</h2>
            <button @click="goBack" class="back-button">
              <i class="material-icons">arrow_back_ios</i>
            </button>
          </div>
          <div class="penilaian-info">
            <p><strong>Tanggal Penilaian:</strong> {{ formatDateTime(penilaianKarya.tgl_penilaian) }} WIB</p>
            <p><strong>Total Nilai:</strong> {{ penilaianKarya.total_nilai }}</p>
            <div class="komentar-view">
              <p><strong>Komentar:</strong></p>
              <p class="komentar-text">{{ penilaianKarya.komentar }}</p>
            </div>
          </div>
          <div class="rubrik-penilaian">
            <h3>Rubrik Penilaian</h3>
            <table class="rubrik-table">
              <thead>
                <tr>
                  <th>Nama Rubrik</th>
                  <th>Skor</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(rubrik, index) in rubrikPenilaians" :key="index">
                  <td>{{ rubrik.nama_rubrik }}</td>
                  <td>{{ rubrik.skor }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </template>
<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '../components/SidebarPenilai.vue';

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const penilaianKarya = ref(null);
const rubrikPenilaians = ref([]);

const getPenilaianDetail = async (id) => {
  try {
    const response = await axios.get(`/show-byregis/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      penilaianKarya.value = response.data.data.penilaianKarya;
      rubrikPenilaians.value = response.data.data.rubrik_penilaians;
    } else {
      console.error('Failed to fetch penilaian detail:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching penilaian detail:', error.message);
  }
};

const goBack = () => {
  router.push('/registrasi-user');
};

const formatDateTime = (dateTime) => {
  if (!dateTime) return 'Tanggal tidak tersedia';
  const [date, time] = dateTime.split(' ');
  const [year, month, day] = date.split('-');
  const [hours, minutes] = time.split(':');
  return `${day}/${month}/${year} ${hours}:${minutes}`;
};

onMounted(() => {
  if (id) {
    getPenilaianDetail(id);
  }
});
</script>
<style lang="scss" scoped>
.page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.penilaian-detail {
  padding: 2rem;
  background-color: #f5d99d;
}

.card {
  padding: 1.5rem;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.back-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
  color: #333;
}

h2, h3 {
  color: #333;
  margin: 0;
}

.penilaian-info p {
  margin: 0.5rem 0;
  color: #333;
}

.komentar-view {
  margin-top: 1rem;
}

.komentar-text {
  padding: 0.5rem;
  background-color: #f9f9f9;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.rubrik-penilaian {
  margin-top: 2rem;
}

.rubrik-table {
  width: 100%;
  border-collapse: collapse;
}

.rubrik-table th, .rubrik-table td {
  border: 1px solid #ddd;
  padding: 0.75rem;
}
</style>
