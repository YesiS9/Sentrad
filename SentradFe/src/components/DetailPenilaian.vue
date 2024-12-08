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
            <div class="komentar-edit">
                <p><strong>Komentar:</strong></p>
                <textarea v-model="tempKomentar"></textarea>
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
                <tr v-for="(rubrik, index) in tempRubrikPenilaians" :key="index">
                    <td>{{ rubrik.nama_rubrik }}</td>
                    <td>
                    <input
                        type="number"
                        v-model.number="rubrik.skor"
                        class="skor-input"
                        min="0"
                        max="100"
                    />
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
            <div class="button-container">
            <button @click="confirmSave" class="save-button">Ubah</button>
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
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const penilaianKarya = ref(null);
const rubrikPenilaians = ref([]);

const tempKomentar = ref('');
const tempRubrikPenilaians = ref([]);

const getPenilaianDetail = async (id) => {
  try {
    const response = await axios.get(`/penilaianKarya/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      penilaianKarya.value = response.data.data.penilaianKarya;
      rubrikPenilaians.value = response.data.data.rubrik_penilaians;

      tempKomentar.value = penilaianKarya.value.komentar;
      tempRubrikPenilaians.value = JSON.parse(JSON.stringify(rubrikPenilaians.value));
    } else {
      console.error('Failed to fetch penilaian detail:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching penilaian detail:', error.message);
  }
};

const confirmSave = () => {
  Swal.fire({
    title: 'Konfirmasi',
    text: "Apakah Anda yakin ingin menyimpan perubahan?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, simpan',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      saveChanges();
    }
  });
};

const saveChanges = async () => {
    console.log('Data being updated:', {
      komentar: tempKomentar.value,
      rubrik_penilaians: tempRubrikPenilaians.value,
    });
  try {
    const response = await axios.put(`/penilaianKarya/${penilaianKarya.value.id}`, {
      komentar: tempKomentar.value,
      rubrik_penilaians: tempRubrikPenilaians.value,
    });
    if (response.status === 200 && response.data.status === 'success') {
      penilaianKarya.value.komentar = tempKomentar.value;
      rubrikPenilaians.value = JSON.parse(JSON.stringify(tempRubrikPenilaians.value));

      Swal.fire('Berhasil!', 'Perubahan berhasil disimpan.', 'success').then(() => {
        router.push('/penilaian-registrasi');
      });
    } else {
      console.error('Failed to update penilaian:', response.data.message);
      Swal.fire('Gagal', 'Gagal menyimpan perubahan.', 'error');
    }
  } catch (error) {
    console.error('Error updating penilaian:', error.message);
    Swal.fire('Error', 'Terjadi kesalahan saat menyimpan perubahan.', 'error');
  }
};

const goBack = () => {
  router.push('/penilaian-registrasi');
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

.komentar-edit textarea {
  width: 100%;
  min-height: 100px;
  margin-top: 0.5rem;
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

.skor-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  text-align: center;
}

.button-container {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
}

.save-button {
  background-color: #f7941e;
  color: #fff;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

</style>
