<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Portofolio' : 'Edit Portofolio' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="kelompok_id">ID Kelompok</label>
              <input type="text" id="kelompok_id" v-model="formData.kelompok_id" placeholder="ID Kelompok" required>
            </div>
            <div class="form-group">
              <label for="seniman_id">ID Seniman</label>
              <input type="text" id="seniman_id" v-model="formData.seniman_id" placeholder="ID Seniman" required>
            </div>
            <div class="form-group">
              <label for="judul_portofolio">Judul Portofolio</label>
              <input type="text" id="judul_portofolio" v-model="formData.judul_portofolio" placeholder="Judul Portofolio" required>
            </div>
            <div class="form-group">
              <label for="tgl_dibuat">Tanggal Dibuat</label>
              <input type="date" id="tgl_dibuat" v-model="formData.tgl_dibuat" required>
            </div>
            <div class="form-group">
              <label for="deskripsi_portofolio">Deskripsi Portofolio</label>
              <textarea id="deskripsi_portofolio" v-model="formData.deskripsi_portofolio" placeholder="Deskripsi Portofolio" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="jumlah_karya">Jumlah Karya</label>
              <input type="number" id="jumlah_karya" v-model="formData.jumlah_karya" placeholder="Jumlah Karya" required>
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
    kelompok_id: '',
    seniman_id: '',
    judul_portofolio: '',
    tgl_dibuat: '',
    deskripsi_portofolio: '',
    jumlah_karya: ''
  });

  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');

  const getPortofolio = async (id) => {
    try {
      const response = await axios.get(`/portofolio/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const portofolioData = response.data.data;
        Object.assign(formData, portofolioData);
        mode.value = 'edit';
      } else {
        console.error('Failed to fetch portofolio:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching portofolio:', error.message);
    }
  };

  onMounted(async () => {
    const { id } = route.params;
    if (id) {
      await getPortofolio(id);
    }
  });

  const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    const result = await Swal.fire({
        title: `Apakah Anda yakin ingin ${action} portofolio ini?`,
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
        response = await axios.post('/portofolio', formData);
      } else if (mode.value === 'edit' && formData.id) {
        response = await axios.put(`/portofolio/${formData.id}`, formData);
      } else {
        console.error('Invalid mode or missing formData.id for edit.');
        return;
      }

      if (response.status === 200 && response.data.status === 'success') {
        if (mode.value === 'add') {
            console.log('Adding portofolio:', response.data.data);
        } else {
            console.log('Editing portofolio:', response.data.data);
        }
        router.push({ name: 'DataPortofolio' });
      } else {
        console.error(mode.value === 'add' ? 'Failed to add portofolio:' : 'Failed to edit portofolio:', response.data.message);
      }
    } catch (error) {
      console.error('Error saving data:', error.message);
      if (error.response) {
        console.error('Server response:', error.response.data);
      }
    }
  };

  const closeForm = () => {
    formData.kelompok_id = '';
    formData.seniman_id = '';
    formData.judul_portofolio = '';
    formData.tgl_dibuat = '';
    formData.deskripsi_portofolio = '';
    formData.jumlah_karya = '';
    mode.value = 'add';
    router.push({ name: 'DataPortofolio' });
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
    max-width: 600px;
    max-height: 800px;
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

      input[type="text"],
      input[type="date"],
      input[type="number"],
      select,
      textarea {
        width: 35vw;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
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
        background-color: #4caf50;
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
