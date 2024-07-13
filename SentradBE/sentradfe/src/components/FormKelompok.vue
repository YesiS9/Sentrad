<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Kelompok' : 'Edit Kelompok' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-row">
              <div class="form-group">
                <label for="seniman_id">Seniman</label>
                <select id="seniman_id" v-model="formData.seniman_id" required>
                  <option value="">Pilih Seniman</option>
                  <option v-for="seniman in senimans" :key="seniman.id" :value="seniman.id">{{ seniman.nama_seniman }}</option>
                </select>
              </div>

              <div class="form-group">
                <label for="nama_kelompok">Nama Kelompok</label>
                <input type="text" id="nama_kelompok" v-model="formData.nama_kelompok" placeholder="Nama Kelompok" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="tgl_terbentuk">Tanggal Terbentuk</label>
                <input type="date" id="tgl_terbentuk" v-model="formData.tgl_terbentuk" placeholder="Tanggal Terbentuk" required>
              </div>

              <div class="form-group">
                <label for="alamat_kelompok">Alamat Kelompok</label>
                <input type="text" id="alamat_kelompok" v-model="formData.alamat_kelompok" placeholder="Alamat Kelompok" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="deskripsi_kelompok">Deskripsi Kelompok</label>
                <input type="text" id="deskripsi_kelompok" v-model="formData.deskripsi_kelompok" placeholder="Deskripsi Kelompok" required>
              </div>

              <div class="form-group">
                <label for="noTelp_kelompok">No. Telp Kelompok</label>
                <input type="text" id="noTelp_kelompok" v-model="formData.noTelp_kelompok" placeholder="No. Telp Kelompok" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email_kelompok">Email Kelompok</label>
                <input type="email" id="email_kelompok" v-model="formData.email_kelompok" placeholder="Email Kelompok" required>
              </div>

              <div class="form-group">
                <label for="jumlah_anggota">Jumlah Anggota</label>
                <input type="number" id="jumlah_anggota" v-model="formData.jumlah_anggota" placeholder="Jumlah Anggota" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="status_kelompok">Status Kelompok</label>
                <select id="status_kelompok" v-model="formData.status_kelompok" required>
                  <option value="1">Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit">{{ mode === 'add' ? 'Tambah' : 'Simpan Perubahan' }}</button>
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

  const formData = reactive({
    seniman_id: '',
    nama_kelompok: '',
    tgl_terbentuk: '',
    alamat_kelompok: '',
    deskripsi_kelompok: '',
    noTelp_kelompok: '',
    email_kelompok: '',
    jumlah_anggota: '',
    status_kelompok: 1,
  });

  const senimans = ref([]);

  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');

  const getSeniman = async () => {
    try {
      const response = await axios.get('/seniman');
      senimans.value = response.data.data;
    } catch (error) {
      console.error('Error fetching seniman list:', error.message);
    }
  };

  const getKelompok = async (id) => {
    try {
      const response = await axios.get(`/kelompok/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const kelompokData = response.data.data;
        Object.assign(formData, kelompokData);
        mode.value = 'edit';
      } else {
        console.error('Failed to fetch kelompok:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching kelompok:', error.message);
    }
  };

  onMounted(async () => {
    await getSeniman();

    const { id } = route.params;
    if (id) {
      await getKelompok(id);
    }
  });

  const formatDate = (date) => {
    const [year, month, day] = date.split('-');
    return `${day}/${month}/${year}`;
  };

  const handleSubmit = async () => {
    try {
      const formattedData = {
        ...formData,
        tgl_terbentuk: formatDate(formData.tgl_terbentuk),
      };
      let response;
      if (mode.value === 'add') {
        response = await axios.post('/kelompok', formattedData);
      } else if (mode.value === 'edit' && formData.id) {
        response = await axios.put(`/kelompok/${formData.id}`, formattedData);
      } else {
        console.error('Invalid mode or missing formData.id for edit.');
        return;
      }

      if (response.status === 200 && response.data.status === 'success') {
        router.push({ name: 'DataKelompok' });
        closeForm();
      } else {
        console.error(
          mode.value === 'add'
            ? 'Failed to add kelompok:'
            : 'Failed to edit kelompok:',
          response.data.message
        );
      }
    } catch (error) {
      console.error('Error saving data:', error.message);
      if (error.response) {
        console.error('Server response:', error.response.data);
      }
    }
  };

  const closeForm = () => {
    formData.seniman_id = '';
    formData.nama_kelompok = '';
    formData.tgl_terbentuk = '';
    formData.alamat_kelompok = '';
    formData.deskripsi_kelompok = '';
    formData.noTelp_kelompok = '';
    formData.email_kelompok = '';
    formData.jumlah_anggota = '';
    formData.status_kelompok = 1;
    mode.value = 'add';
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
    height: 90vw;
    max-width: 650px;
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

    .form-row {
      display: flex;
      justify-content: space-between;
      width: 100%;

      .form-group {
        margin-bottom: 1rem;
        text-align: left;
        width: 48%;
      }
    }

    input[type='text'],
    input[type='date'],
    input[type='email'],
    input[type='number'],
    select {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .form-actions {
      margin-top: 1rem;
      text-align: right;
      width: 100%;
    }

    button {
      background-color: #f7941e;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 0.5rem;
    }

    button[type='submit'] {
      background-color: #4caf50;
    }

    button[type='submit']:hover {
      background-color: #45a049;
    }

    button[type='button'] {
      background-color: #f44336;
    }

    button[type='button']:hover {
      background-color: #d32f2f;
    }
  }
  </style>
