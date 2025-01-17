<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Portofolio' : 'Edit Portofolio' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="nama_kategori">Kategori Seni</label>
              <Multiselect
                v-model="formData.nama_kategori"
                :options="kategoriOptions"
                :searchable="true"
                :close-on-select="true"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="Pilih atau cari kategori Seni"
                label="nama_kategori"
                track-by="nama_kategori"
                class="custom-multiselect"
              ></Multiselect>
            </div>
            <div class="form-group">
              <label for="judul_portofolio">Judul Portofolio</label>
              <input type="text" id="judul_portofolio" v-model="formData.judul_portofolio" placeholder="Judul Portofolio" required>
            </div>
            <div class="form-group">
              <label for="deskripsi_portofolio">Deskripsi Portofolio</label>
              <textarea id="deskripsi_portofolio" v-model="formData.deskripsi_portofolio" placeholder="Deskripsi Portofolio" rows="3" required></textarea>
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
  import Multiselect from '@vueform/multiselect';
  import '@vueform/multiselect/themes/default.css';

  const formData = reactive({
    id: '',
    kelompok_id: '',
    seniman_id: '',
    nama_kategori: '',
    judul_portofolio: '',
    deskripsi_portofolio: '',
    jumlah_karya: 0,
  });

  const kategoriOptions = ref([]);
  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');


  const getKategoriOptions = async () => {
    try {
      const response = await axios.get('/nama-kategori');
      if (response.status === 200 && response.data.status === 'success') {
        kategoriOptions.value = response.data.data.map(kategori => kategori.nama_kategori);
      } else {
        console.error('Failed to fetch kategori seni options:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching kategori seni options:', error.message);
    }
  };

  const getPortofolio = async (id) => {
    try {
      const response = await axios.get(`/portofolio/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const portofolioData = response.data.data;
        formData.id = portofolioData.id;
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
    formData.kelompok_id = localStorage.getItem('kelompok_id');
    formData.seniman_id = localStorage.getItem('seniman_id');
    await getKategoriOptions();

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
    console.log('Server response:', response);

    if (response.status === 200 && response.data.status === 'success') {
      router.push({ name: 'Registrasi' });
    } else {
      console.error(mode.value === 'add' ? 'Failed to add portofolio:' : 'Failed to edit portofolio:', response.data.message);
      Swal.fire('Error', response.data.message || 'Failed to save portofolio', 'error');
    }
  } catch (error) {
    console.error('Error saving data:', error.message);
    if (error.response) {
      console.error('Server response:', error.response.data);
      Swal.fire('Error', error.response.data.message || 'An error occurred', 'error');
    }
  }
};


  const closeForm = () => {
    router.push({ name: 'Registrasi' });
  };
  </script>

  <style lang="scss" scoped>
  @import '@vueform/multiselect/themes/default.css';
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
    max-height: 500px;
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
