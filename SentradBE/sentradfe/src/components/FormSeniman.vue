<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Profile Seniman' : 'Edit Profile Seniman' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div v-if="mode === 'edit'" class="form-group">
              <label for="username">Username</label>
              <input
                type="text"
                id="username"
                v-model="formData.username"
                readonly
                placeholder="Username"
              />
            </div>

            <div v-if="mode === 'add'" class="form-group">
              <label for="username">Username</label>
              <Multiselect
                v-model="formData.username"
                :options="users"
                :searchable="true"
                :close-on-select="true"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="Pilih atau cari username"
                label="username"
                track-by="username"
                class="custom-multiselect"
                required
              ></Multiselect>
            </div>

            <div class="form-group">
              <label for="nama_seniman">Nama Lengkap Seniman</label>
              <input type="text" id="nama_seniman" v-model="formData.nama_seniman" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
              <label for="tgl_lahir">Tanggal Lahir</label>
              <input type="date" id="tgl_lahir" v-model="formData.tgl_lahir" placeholder="Tanggal Lahir" required>
            </div>
            <div class="form-group">
              <label for="deskripsi_seniman">Deskripsi Seniman</label>
              <textarea id="deskripsi_seniman" v-model="formData.deskripsi_seniman" placeholder="Deskripsi Seniman" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="alamat_seniman">Alamat Seniman</label>
              <input type="text" id="alamat_seniman" v-model="formData.alamat_seniman" placeholder="Alamat Seniman" required>
            </div>
            <div class="form-group">
              <label for="noTelp_seniman">No. Telp</label>
              <input type="text" id="noTelp_seniman" v-model="formData.noTelp_seniman" placeholder="No. Telp" required>
            </div>
            <div class="form-group">
              <label for="lama_pengalaman">Lama Pengalaman Berkarya (Tahun)</label>
              <input type="number" id="lama_pengalaman" v-model="formData.lama_pengalaman" placeholder="Lama Pengalaman" required>
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
import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';
import Swal from 'sweetalert2';
import { useToast } from 'vue-toastification';

const formData = reactive({
  username: '',
  nama_seniman: '',
  tgl_lahir: '',
  deskripsi_seniman: '',
  alamat_seniman: '',
  noTelp_seniman: '',
  lama_pengalaman: '',
  status_seniman: '1',
});

const users = ref([]);
const route = useRoute();
const router = useRouter();
const mode = ref('add');
const toast = useToast();

const getUser = async () => {
  try {
    const response = await axios.get('/userbyseniman');
    if (response.data.status === 'success' && Array.isArray(response.data.data)) {
      users.value = response.data.data;
    } else {
      console.error('Unexpected response data format:', response.data);
    }
  } catch (error) {
    console.error('Error fetching user list:', error.message);
  }
};

const getSeniman = async (id) => {
  try {
    const response = await axios.get(`/seniman/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      const senimanData = response.data.data;
      Object.assign(formData, senimanData);
      mode.value = 'edit';
    } else {
      console.error('Failed to fetch seniman:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching seniman:', error.message);
  }
};

onMounted(async () => {
  await getUser();
  const { id } = route.params;
  if (id) {
    await getSeniman(id);
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
            title: `Apakah Anda yakin ingin ${action} seniman ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        });

        if (!result.isConfirmed) {
            return;
        }
    }

  try {
    const formattedData = { ...formData, tgl_lahir: formatDate(formData.tgl_lahir) };
    let response;
    if (mode.value === 'add') {
      response = await axios.post('/seniman/storeByAdmin', formattedData);
    } else if (mode.value === 'edit' && formData.id) {
      response = await axios.put(`/seniman/${formData.id}`, formattedData);
    } else {
      console.error('Invalid mode or missing formData.id for edit.');
      return;
    }

    if (response.status === 200 && response.data.status === 'success') {
      toast.success(`Berhasil ${mode.value === 'add' ? 'menambahkan' : 'mengedit'} seniman!`);
      router.push({ name: 'DataSeniman' });
      closeForm();
    } else {
      toast.error(response.data.message || `Gagal ${mode.value === 'add' ? 'menambahkan' : 'mengedit'} seniman!`);
    }
  } catch (error) {
    console.error('Error saving data:', error.message);
    if (error.response) {
      console.error('Server response:', error.response.data);
      toast.error(error.response.data.message || 'Terjadi kesalahan saat menyimpan data!');
    } else {
      toast.error('Terjadi kesalahan saat menyimpan data!');
    }
  }
};

const closeForm = () => {
  formData.username = '';
  formData.nama_seniman = '';
  formData.tgl_lahir = '';
  formData.deskripsi_seniman = '';
  formData.alamat_seniman = '';
  formData.noTelp_seniman = '';
  formData.lama_pengalaman = '';
  formData.status_seniman = '1';
  mode.value = 'add';
  router.push({ name: 'DataSeniman' });
};
</script>

<style lang="scss" scoped>
main{
      background-color: #f7941e;
  }
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f7941e;
}

.auth-form {
    background-color: #fff;
  width: 90vw;
  height: 90vw;
  max-width: 650px;
  max-height: 850px;
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

    input[type="text"],input[type="date"], input[type="number"],textarea{
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
