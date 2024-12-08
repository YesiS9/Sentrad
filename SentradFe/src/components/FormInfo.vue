<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Peraturan' : 'Edit Peraturan' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-row">
              <div class="form-group">
                <label for="info">Pengumuman Peraturan Penilaian</label>
                <textarea
                  id="info"
                  v-model="formData.info"
                  placeholder="Peraturan Penilaian"
                  rows="3"
                  required
                ></textarea>
              </div>
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
  import { useToast } from 'vue-toastification';

  const formData = reactive({
    info: '',
  });
  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');
  const toast = useToast();

  const getInfo = async (id) => {
    try {
      const response = await axios.get(`/info/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        Object.assign(formData, response.data.data);
        mode.value = 'edit';
      } else {
        console.error('Gagal mendapatkan data:', response.data.message);
      }
    } catch (error) {
      console.error('Kesalahan saat mengambil data:', error.message);
    }
  };

  onMounted(async () => {
    const { id } = route.params;
    if (id) {
      await getInfo(id);
    }
  });

  const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    try {
      let response;
      if (mode.value === 'add') {
        response = await axios.post('/info', formData);
      } else {
        response = await axios.put(`/info/${route.params.id}`, formData);
      }

      if (response.status === 200 && response.data.status === 'success') {
        toast.success(`Berhasil ${action} info!`);
        router.push({ name: 'DataInfo' });
      } else {
        toast.error(response.data.message || `Gagal ${action} info!`);
      }
    } catch (error) {
      console.error('Kesalahan saat menyimpan data:', error.message);
      toast.error('Terjadi kesalahan saat menyimpan data!');
    }
  };

  const closeForm = () => {
    router.push({ name: 'DataInfo' });
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
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
  }
  h3 {
    margin-bottom: 1rem;
  }
  .form-group {
    margin-bottom: 1.5rem;
    text-align: left;
  }
  textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
    font-size: 1rem;
  }
  .form-actions {
    display: flex;
    justify-content: space-between;
  }
  button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
  }
  button[type='submit'] {
    background-color: #45a049;
  }
  button[type='submit']:hover {
    background-color: #388e3c;
  }
  button[type='button'] {
    background-color: #f44336;
  }
  button[type='button']:hover {
    background-color: #d32f2f;
  }
  </style>
