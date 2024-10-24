<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Registrasi Individu' : 'Edit Registrasi Individu' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="nama">Nama</label>
              <input type="text" id="nama" :value="formData.nama" disabled placeholder="Nama">
            </div>
            <div class="form-group">
              <label for="tgl_lahir">Tanggal Lahir</label>
              <input type="text" id="tgl_lahir" :value="formData.tgl_lahir" disabled placeholder="Tanggal Lahir">
            </div>
            <div class="form-group">
              <label for="alamat">Alamat</label>
              <input type="text" id="alamat" :value="formData.alamat" disabled placeholder="Alamat">
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="text" id="email" :value="formData.email" disabled placeholder="Email">
            </div>
            <div class="form-group">
              <label for="noTelp">No Telp</label>
              <input type="text" id="noTelp" :value="formData.noTelp" disabled placeholder="No Telp">
            </div>

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
              <label for="tgl_mulai">Tanggal Mulai Berkarya</label>
              <input type="date" id="tgl_mulai" v-model="formData.tgl_mulai" placeholder="Tanggal Mulai" required>
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
    nama_kategori: [],
    nama: '',
    tgl_lahir: '',
    tgl_mulai: '',
    alamat: '',
    email: '',
    noTelp: '',
    status_individu: 'Dalam proses',
    seniman_id: ''
  });

  const kategoriOptions = ref([]);
  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');

  const getSeniman = async () => {
  const senimanId = localStorage.getItem('seniman_id');
  if (senimanId) {
    formData.seniman_id = senimanId;
    try {
      const response = await axios.get(`/seniman/${senimanId}`);
      if (response.status === 200 && response.data.status === 'success') {
        const senimanData = response.data.data;
        formData.nama = senimanData.nama_seniman;
        formData.tgl_lahir = senimanData.tgl_lahir;
        formData.alamat = senimanData.alamat_seniman;
        formData.noTelp = senimanData.noTelp_seniman;
        formData.email = senimanData.user.email;
      } else {
        console.error('Failed to fetch seniman data:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching seniman data:', error.message);
    }
  } else {
    console.error('Seniman ID is missing from localStorage.');
  }
};


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

  const getIndividu = async (id) => {
    try {
      const response = await axios.get(`/registerIndividu/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const individuData = response.data.data;
        Object.assign(formData, individuData);
        mode.value = 'edit';
      } else {
        console.error('Failed to fetch individu:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching individu:', error.message);
    }
  };

  onMounted(async () => {
    await getKategoriOptions();
    await getSeniman();

    const { id } = route.params;
    if (id) {
      await getIndividu(id);
    }
  });

  const formatDate = (date) => {
    const [year, month, day] = date.split('-');
    return `${day}/${month}/${year}`;
  };

  const handleSubmit = async () => {
  const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';
  if (mode.value === 'edit') {
    const result = await Swal.fire({
      title: `Apakah Anda yakin ingin ${action} registrasi individu ini?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
      return;
    }
  }

  const confirmationResult = await Swal.fire({
    title: `Konfirmasi Data Registrasi`,
    html: `
      <strong>Nama:</strong> ${formData.nama}<br>
      <strong>Tanggal Lahir:</strong> ${formatDate(formData.tgl_lahir)}<br>
      <strong>Alamat:</strong> ${formData.alamat}<br>
      <strong>No Telp:</strong> ${formData.noTelp}<br>
      <strong>Email:</strong> ${formData.email}<br>
      <strong>Kategori Seni:</strong> ${formData.nama_kategori}<br>
      <strong>Tanggal Mulai:</strong> ${formatDate(formData.tgl_mulai)}<br>
    `,
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'Data Sudah Benar',
    cancelButtonText: 'Belum',
  });

  if (!confirmationResult.isConfirmed) {
    return;
  }

  try {
    const formattedData = {
      ...formData,
      tgl_lahir: formatDate(formData.tgl_lahir),
      tgl_mulai: formatDate(formData.tgl_mulai)
    };
    console.log('Formatted Data:', formattedData);
    let response;
    if (mode.value === 'add') {
      response = await axios.post('/registerIndividu', formattedData);
    } else if (mode.value === 'edit' && formData.id) {
      response = await axios.put(`/registerIndividu/${formData.id}`, formattedData);
    } else {
      console.error('Invalid mode or missing formData.id for edit.');
      return;
    }

    if (response.status === 200 && response.data.status === 'success') {
      router.push({ name: 'Registrasi' });
      closeForm();
    } else {
      console.error(mode.value === 'add' ? 'Failed to add individu:' : 'Failed to edit individu:', response.data.message);
    }
  } catch (error) {
    console.error('Error saving data:', error.message);
    if (error.response) {
      console.error('Server response:', error.response.data);
    }
  }
};


  const closeForm = () => {
    formData.nama_kategori = [];
    formData.seniman_id = '';
    formData.nama = '';
    formData.tgl_lahir = '';
    formData.tgl_mulai = '';
    formData.alamat = '';
    formData.email = '';
    formData.noTelp = '';
    formData.status_individu = 'Dalam proses';
    mode.value = 'add';
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
    height: 90vw;
    max-width: 600px;
    max-height: 700px;
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
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
    }

    .custom-multiselect {
      width: 40vw;
    }

    input[type="text"],
    input[type="date"],
    input[type="email"],
    input[type="number"],
    select {
      width: 40vw;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;

      button {
          background-color: #f7941e;
          color: #fff;
          border: none;
          padding: 0.5rem 1rem;
          border-radius: 4px;
          cursor: pointer;
          margin-left: 0.5rem;
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
          background-color: #da190b;
      }
    }
  }
  </style>
