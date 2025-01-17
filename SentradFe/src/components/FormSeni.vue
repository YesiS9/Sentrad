<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <h3>{{ mode === 'add' ? 'Tambah Seni' : 'Edit Seni' }}</h3>
          <form @submit.prevent="handleSubmit">
            <div class="form-group">
              <label for="nama_kategori">Kategori Seni</label>
              <select id="nama_kategori" v-model="formData.nama_kategori" required oninvalid="this.setCustomValidity('Kategori Seni tidak boleh kosong')" oninput="this.setCustomValidity('')">
                <option value="">Pilih Kategori</option>
                <option v-for="kategori in kategori_senis" :key="kategori.id" :value="kategori.nama_kategori">
                  {{ kategori.nama_kategori }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="nama_seni">Nama Seni</label>
              <input type="text" id="nama_seni" v-model="formData.nama_seni" placeholder="Nama Seni" required oninvalid="this.setCustomValidity('Nama Seni tidak boleh kosong')" oninput="this.setCustomValidity('')">
            </div>
            <div class="form-group">
              <label for="deskripsi_seni">Deskripsi Seni</label>
              <textarea id="deskripsi_seni" v-model="formData.deskripsi_seni" placeholder="Deskripsi Seni" rows="3" required oninvalid="this.setCustomValidity('Deskripsi Seni tidak boleh kosong')" oninput="this.setCustomValidity('')"></textarea>
            </div>
            <div class="form-group">
              <label for="status_seni">Status Seni</label>
              <select id="status_seni" v-model="formData.status_seni" required oninvalid="this.setCustomValidity('Status Seni tidak boleh kosong')" oninput="this.setCustomValidity('')">
                <option value="">Pilih Status Seni</option>
                <option value="Budaya">Budaya</option>
                <option value="Non-budaya">Non-Budaya</option>
                <option value="Modern">Modern</option>
              </select>
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
    nama_kategori: '',
    nama_seni: '',
    deskripsi_seni: '',
    status_seni: ''
  });

  const kategori_senis = ref([]);
  const route = useRoute();
  const router = useRouter();
  const mode = ref('add');
  const toast = useToast();

  const getKategori = async () => {
    try {
      const response = await axios.get('/kategoriSeni');
      kategori_senis.value = response.data.data;
      console.log('Kategori response:', response.data);
    } catch (error) {
      console.error('Error fetching kategori_senis:', error.message);
    }
  };

  const getSeni = async (id) => {
    try {
      const response = await axios.get(`/seni/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        const seniData = response.data.data;
        Object.assign(formData, seniData);
        mode.value = 'edit';
      } else {
        console.error('Failed to fetch seni:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching seni:', error.message);
    }
  };

  onMounted(async () => {
    await getKategori();
    const { id } = route.params;
    if (id) {
      await getSeni(id);
    }
  });



  const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    if (mode.value === 'edit') {
        const result = await Swal.fire({
            title: `Apakah Anda yakin ingin ${action} seni ini?`,
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
      let response;
      if (mode.value === 'add') {
        response = await axios.post('/seni', formData);
      } else if (mode.value === 'edit' && formData.id) {
        response = await axios.put(`/seni/${formData.id}`, formData);
      } else {
        console.error('Invalid mode or missing formData.id for edit.');
        return;
      }

      if (response.status === 200 && response.data.status === 'success') {
            toast.success(`Berhasil ${action} seni!`);
            router.push({ name: 'DataSeni' });
            closeForm();
        } else {
            toast.error(response.data.message || `Gagal ${action} seni!`);
        }
    } catch (error) {
      console.error('Error saving data:', error.message);
      if (error.response) {
        console.error('Server response:', error.response.data);
      }
    }
  };

  const closeForm = () => {
    formData.nama_kategori = '';
    formData.nama_seni = '';
    formData.deskripsi_seni = '';
    formData.status_seni = '';
    mode.value = 'add';
    router.push({ name: 'DataSeni' });
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
  height: 100vh;
  background-color: #f7941e;
}

.auth-form {
  background-color: #fff;
  width: 90vw;
  height: 90vh;
  max-width: 600px;
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

    input[type="text"],select,textarea{
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
