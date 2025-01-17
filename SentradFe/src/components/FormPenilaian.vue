<template>
    <main>
      <div class="auth-container">
        <div class="auth-content">
          <aside class="info-section">
            <h4>Aturan Penilaian</h4>
            <div v-for="(info, index) in aturanPenilaian" :key="index" class="info-paragraph">
              <p>{{ info.info }}</p>
            </div>
          </aside>
          <div class="auth-form">
            <h3>{{ mode === 'add' ? 'Tambah Penilaian' : 'Edit Penilaian' }}</h3>
            <form @submit.prevent="handleSubmit">
              <div class="form-group">
                <label><strong>Rubrik</strong></label>
                <div class="rubrik-list">
                  <div v-for="(rubrik, index) in rubrikOptions" :key="index" class="rubrik-item">
                    <label :for="'rubrik-' + index" class="rubrik-label">
                      <input
                        type="checkbox"
                        :id="'rubrik-' + index"
                        v-model="selectedRubriks"
                        :value="rubrik.id"
                        @change="handleRubrikChange"
                        :disabled="selectedRubriks.length >= 5 && !selectedRubriks.includes(rubrik.id)"
                      />
                      <span>{{ rubrik.nama_rubrik }}</span>
                    </label>
                    <input
                      v-if="selectedRubriks.includes(rubrik.id)"
                      type="number"
                      v-model="rubrikSkors[rubrik.id]"
                      placeholder="Skor"
                      min="0"
                      max="100"
                      required
                    />
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="komentar"><strong>Komentar</strong></label>
                <textarea
                  id="komentar"
                  v-model="formData.komentar"
                  placeholder="Komentar"
                  rows="3"
                  required
                ></textarea>
              </div>
              <div class="form-actions">
                <button type="submit">{{ mode === 'add' ? 'Tambah' : 'Simpan' }}</button>
                <button type="button" @click="closeForm">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </template>

  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from '../services/api.js';
  import Swal from 'sweetalert2';
  import { useToast } from 'vue-toastification';

  const rubrikOptions = ref([]);
  const aturanPenilaian = ref([]);
  const selectedRubriks = ref([]);
  const rubrikSkors = reactive({});
  const toast = useToast();

  const formData = reactive({
    kuota_id: '',
    komentar: '',
    rubrik_penilaians: [],
    regisIndividu_id: '',
    regisKelompok_id: '',
  });

  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');

  onMounted(async () => {
    try {
      const response = await axios.get('/rubrik');
      if (response.status === 200) {
        rubrikOptions.value = response.data.data;
      } else {
        console.error('Failed to fetch rubrik data');
      }

      const aturanResponse = await axios.get('/info');
      if (aturanResponse.status === 200) {
        aturanPenilaian.value = aturanResponse.data.data;
      } else {
        console.error('Failed to fetch aturan penilaian');
      }

      const penilaiId = localStorage.getItem('penilai_id');
      const kuotaResponse = await axios.post('/get-kuota-id', { penilai_id: penilaiId });
      if (kuotaResponse.status === 200 && kuotaResponse.data.kuota_id) {
        formData.kuota_id = kuotaResponse.data.kuota_id;
      } else {
        console.error('Failed to fetch kuota ID');
      }

      const { individuId, kelompokId, id } = route.params;

      if (individuId) {
        formData.regisIndividu_id = individuId;
        localStorage.setItem('regisIndividuId', individuId);
      } else if (kelompokId) {
        formData.regisKelompok_id = kelompokId;
        localStorage.setItem('regisKelompokId', kelompokId);
      } else {
        formData.regisIndividu_id = localStorage.getItem('regisIndividuId');
        formData.regisKelompok_id = localStorage.getItem('regisKelompokId');
      }

      if (id) {
        mode.value = 'edit';
        await loadPenilaianData(id);
      }
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  });

  const loadPenilaianData = async (id) => {
    try {
      const response = await axios.get(`/penilaianKarya/${id}`);
      if (response.status === 200) {
        const data = response.data.data;
        formData.komentar = data.komentar;
        formData.kuota_id = data.kuota_id;

        selectedRubriks.value = data.rubrik_penilaians.map((rubrik) => rubrik.id); 
        data.rubrik_penilaians.forEach((rubrik) => {
          rubrikSkors[rubrik.id] = rubrik.skor;
        });
      } else {
        console.error('Failed to fetch penilaian data');
      }
    } catch (error) {
      console.error('Error fetching penilaian data:', error);
    }
  };

  const handleRubrikChange = () => {
    formData.rubrik_penilaians = selectedRubriks.value.map((rubrikId) => {
      const rubrik = rubrikOptions.value.find(r => r.id === rubrikId);
      return {
        id: rubrikId,
        nama_rubrik: rubrik ? rubrik.nama_rubrik : '',
        skor: rubrikSkors[rubrikId] || 0,
      };
    });
  };

  const handleSubmit = async () => {
    if (selectedRubriks.value.length < 5) {
      toast.error('Pilih minimal 5 rubrik sebelum menyimpan penilaian.');
      return;
    }

    handleRubrikChange();

    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    const result = await Swal.fire({
      title: `Apakah Anda yakin ingin ${action} penilaian ini?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
      return;
    }

    try {
      let response;
      if (mode.value === 'add') {
        response = await axios.post('/penilaianKarya', formData);
      } else {
        response = await axios.put(`/penilaianKarya/${route.params.id}`, formData);
      }

      if (response.status === 200 && response.data.status === 'success') {
        router.push({ name: 'PenilaianKarya' });
        closeForm();
      } else {
        console.error('Error:', response.data.message);
      }
    } catch (error) {
      console.error('Error saving data:', error.message);
    }
  };

  const closeForm = () => {
    selectedRubriks.value = [];
    Object.keys(rubrikSkors).forEach((key) => (rubrikSkors[key] = ''));
    formData.komentar = '';
    router.push({ name: 'PenilaianKarya' });
  };
  </script>

<style lang="scss" scoped>
main {
  background-color: #f7941e;
}

.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f7941e;
}

.auth-content {
  display: flex;
  gap: 2rem;
  width: 90vw;
  max-width: 1200px;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.info-section {
  flex: 1;
  background-color: #fefae0;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  h4 {
    margin-bottom: 1rem;
    color: #333;
  }
  .info-paragraph {
    margin-bottom: 1rem;
    p {
      margin: 0;
      color: #555;
      line-height: 1.5;
    }
  }
}

.auth-form {
  flex: 2;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;

  h3 {
    margin-bottom: 1rem;
  }

  .form-group {
    width: 100%;
    margin-bottom: 1rem;
    text-align: left;

    label {
      display: block;
      margin-bottom: 0.5rem;
    }

    textarea {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: vertical;
    }
  }

  .rubrik-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: space-between;
    margin-bottom: 1rem;
  }

  .rubrik-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;

    input[type="number"] {
      width: 80px;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      text-align: center;
    }
  }

  .form-actions {
    margin-top: 1rem;
    text-align: right;
    width: 100%;
    button {
      margin-left: 0.5rem;
      background-color: #f7941e;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
    }
    button[type="submit"] {
      background-color: #45a049;
    }
    button[type="submit"]:hover {
      background-color: #45a049;
    }
    button[type="button"] {
      background-color: #f44336;
    }
    button[type="button"]:hover {
      background-color: #d32f2f;
    }
  }
}
</style>
