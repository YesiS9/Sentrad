<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Penilaian' : 'Edit Penilaian' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="nilai">Nilai</label>
              <input type="number" id="nilai" v-model="formData.nilai" placeholder="Nilai" required>
            </div>
            <div class="form-group">
              <label for="komentar">Komentar</label>
              <textarea id="komentar" v-model="formData.komentar" placeholder="Komentar" rows="3" required></textarea>
            </div>
            <div class="form-actions">
              <button type="submit">{{ mode === 'add' ? 'Tambah' : 'Simpan' }}</button>
              <button type="button" @click="closeForm">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </template>

  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from '../services/api.js';
  import Swal from 'sweetalert2';

  const formData = reactive({
    regisIndividu_id: '',
    regisKelompok_id: '',
    nilai: '',
    komentar: '',
  });

  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');

  const getPenilaian = async (id) => {
    try {
      const response = await axios.get(`/penilaian/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const penilaianData = response.data.data;
        Object.assign(formData, {
          id: penilaianData.id,
          regisIndividu_id: penilaianData.regisIndividu_id,
          regisKelompok_id: penilaianData.regisKelompok_id,
          nilai: penilaianData.nilai,
          komentar: penilaianData.komentar,
        });
        mode.value = 'edit';
      } else {
        console.error('Failed to fetch penilaian:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching penilaian:', error.message);
    }
  };

  onMounted(() => {
    const { id } = route.params;
    if (id) {
      if (route.name === 'FormPenilaianIndividu') {
        formData.regisIndividu_id = id;
      } else if (route.name === 'FormPenilaianKelompok') {
        formData.regisKelompok_id = id;
      }
    }
  });

  const handleSubmit = async () => {
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
        response = await axios.post('/penilaian', formData);
      } else if (mode.value === 'edit' && formData.id) {
        response = await axios.put(`/penilaian/${formData.id}`, formData);
      } else {
        console.error('Invalid mode or missing formData.id for edit.');
        return;
      }

      if (response.status === 200 && response.data.status === 'success') {
        router.push({ name: 'Penilaian' });
        closeForm();
      } else {
        console.error(mode.value === 'add' ? 'Failed to add penilaian:' : 'Failed to edit penilaian:', response.data.message);
        console.log('API Response:', response);
      }
    } catch (error) {
      console.error('Error saving data:', error.message);
      if (error.response) {
        console.error('Server response:', error.response.data);
      }
    }
  };

  const closeForm = () => {
    formData.regisIndividu_id = '';
    formData.regisKelompok_id = '';
    formData.nilai = '';
    formData.komentar = '';
    mode.value = 'add';
    router.push({ name: 'Penilaian' });
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
    height: 100vh;
    background-color: #f7941e;
  }
  .auth-form {
    background-color: #fff;
    width: 90vw;
    height: 90vh;
    max-width: 500px;
    max-height: 650px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
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
      input[type="text"], input[type="email"], input[type="password"], input[type="number"], textarea {
        width: 30vw;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
      }
      textarea {
        resize: vertical;
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
        background-color: #f7941e;
      }
      button[type="submit"]:hover {
        background-color: #e6830d;
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
