<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Kelompok' : 'Edit Kelompok' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-row">
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
                <label for="nama_kelompok">Nama Kelompok</label>
                <input type="text" id="nama_kelompok" v-model="formData.nama_kelompok" placeholder="Nama Kelompok" required />
              </div>
            </div>

            <!-- Second Row: Tanggal and Alamat -->
            <div class="form-row">
              <div class="form-group">
                <label for="tgl_terbentuk">Tanggal Terbentuk</label>
                <input type="date" id="tgl_terbentuk" v-model="formData.tgl_terbentuk" placeholder="Tanggal Terbentuk" required />
              </div>
              <div class="form-group">
                <label for="alamat_kelompok">Alamat Kelompok</label>
                <input type="text" id="alamat_kelompok" v-model="formData.alamat_kelompok" placeholder="Alamat Kelompok" required />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="deskripsi_kelompok">Deskripsi Kelompok</label>
                <textarea id="deskripsi_kelompok" v-model="formData.deskripsi_kelompok" placeholder="Deskripsi Kelompok" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <label for="noTelp_kelompok">No. Telp Kelompok</label>
                <input type="text" id="noTelp_kelompok" v-model="formData.noTelp_kelompok" placeholder="No. Telp Kelompok" required />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email_kelompok">Email Kelompok</label>
                <input type="email" id="email_kelompok" v-model="formData.email_kelompok" placeholder="Email Kelompok" required />
              </div>
              <div class="form-group">
                <label for="jumlah_anggota">Jumlah Anggota</label>
                <input type="number" id="jumlah_anggota" v-model="formData.jumlah_anggota" placeholder="Jumlah Anggota" required />
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
import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';

const formData = reactive({
  nama_kelompok: '',
  tgl_terbentuk: '',
  alamat_kelompok: '',
  deskripsi_kelompok: '',
  noTelp_kelompok: '',
  email_kelompok: '',
  jumlah_anggota: '',
  status_kelompok: 'Dalam proses',
  seniman_id: ''
});

const kategoriOptions = ref([]);
const route = useRoute();
const router = useRouter();
const mode = ref('add');

const getSeniman = () => {
  const senimanId = localStorage.getItem('seniman_id');
  if (senimanId) {
    formData.seniman_id = senimanId;
    console.log('seniman: ', senimanId);
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

const getKelompok = async (id) => {
  try {
    const response = await axios.get(`/registerKelompok/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      const kelompokData = response.data.data;
      console.log('DataKelompok: ', response.data);
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
  getSeniman();
  await getKategoriOptions();
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
  const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

  const result = await Swal.fire({
    title: `Apakah Anda yakin ingin ${action} registrasi kelompok ini?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya',
    cancelButtonText: 'Tidak',
  });

  if (!result.isConfirmed) {
    return;
  }

  try {
    const formattedData = {
      ...formData,
      tgl_terbentuk: formatDate(formData.tgl_terbentuk),
    };

    let response;
    if (mode.value === 'add') {
      response = await axios.post('/registerKelompok', formattedData);
    } else if (mode.value === 'edit' && formData.id) {
      response = await axios.put(`/registerKelompok/${formData.id}`, formattedData);
    } else {
      console.error('Invalid mode or missing formData.id for edit.');
      return;
    }

    if (response.status === 200 && response.data.status === 'success') {
      const kelompokData = response.data.data;

      if (kelompokData && kelompokData.id) {
        localStorage.setItem('kelompok_id', kelompokData.id);
      }
      if (kelompokData && kelompokData.jumlah_anggota) {
        localStorage.setItem('jumlah_anggota', kelompokData.jumlah_anggota);
      }

      router.push({ name: 'FormAnggota', params: { kelompok_id: kelompokData.id } });
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
  formData.status_kelompok = 'Dalam proses';
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
    max-width: 600px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  h3 {
    margin-bottom: 1rem;
  }

  .form-row {
    display: flex;
    justify-content: space-between;
    width: 100%;
    gap: 2rem;
    margin-bottom: 1rem;

    .form-group {
      flex: 1;
    }
  }


  label {
    display: block;
    margin-bottom: 0.5rem;
  }

  .custom-multiselect,
  input[type="text"],
  input[type="date"],
  input[type="email"],
  input[type="number"],
  textarea {
    width: 200px;
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
    background-color: #45a049;
  }

  button[type='submit']:hover {
    background-color: #45a049;
  }

  button[type='button'] {
    background-color: #f44336;
  }

  button[type='button']:hover {
    background-color: #da190b;
  }
</style>
