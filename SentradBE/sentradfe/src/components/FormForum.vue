<template>
    <div class="form-karya">
      <h2>{{ isEdit ? 'Edit Karya' : 'Tambah Karya' }}</h2>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="judul_portofolio">Judul Portofolio</label>
          <Multiselect
            v-model="formData.judul_portofolio"
            :options="portofolioOptions"
            :searchable="true"
            :close-on-select="true"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Pilih atau cari judul portofolio"
            label="judul_portofolio"
            track-by="judul_portofolio"
          ></Multiselect>
        </div>
        <div class="form-group">
          <label for="nama_seni">Nama Seni</label>
          <Multiselect
            v-model="formData.nama_seni"
            :options="seniOptions"
            :searchable="true"
            :close-on-select="true"
            :clear-on-select="false"
            :preserve-search="true"
            placeholder="Pilih atau cari nama seni"
            label="nama_seni"
            track-by="nama_seni"
          ></Multiselect>
        </div>
        <div class="form-group">
          <label for="judul_karya">Judul Karya</label>
          <input type="text" v-model="formData.judul_karya" placeholder="Judul Karya" required>
        </div>
        <div class="form-group">
          <label for="deskripsi_karya">Deskripsi Karya</label>
          <textarea v-model="formData.deskripsi_karya" placeholder="Deskripsi Karya" required></textarea>
        </div>
        <div class="form-group">
          <label for="tgl_pembuatan">Tanggal Pembuatan</label>
          <input type="date" v-model="formData.tgl_pembuatan" required>
        </div>
        <div class="form-group">
          <label for="media_karya">Media Karya</label>
          <input type="file" @change="handleFileChange" required>
        </div>
        <div class="form-group">
          <label for="bentuk_karya">Bentuk Karya</label>
          <input type="text" v-model="formData.bentuk_karya" placeholder="Bentuk Karya" required>
        </div>
        <div class="form-group">
          <label for="status_karya">Status Karya</label>
          <select v-model="formData.status_karya" required>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit">{{ isEdit ? 'Simpan' : 'Tambah' }}</button>
          <button type="button" @click="closeForm">Batal</button>
        </div>
      </form>
    </div>
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
    judul_portofolio: '',
    nama_seni: '',
    judul_karya: '',
    deskripsi_karya: '',
    tgl_pembuatan: '',
    media_karya: null,
    bentuk_karya: '',
    status_karya: 1,
  });
  const portofolioOptions = ref([]);
  const seniOptions = ref([]);
  const route = useRoute();
  const router = useRouter();
  const isEdit = ref(false);
  const toast = useToast();

  const getPortofolioOptions = async () => {
    try {
      const response = await axios.get('/portofolio');
      if (response.status === 200 && response.data.status === 'success') {
        portofolioOptions.value = response.data.data.map(portofolio => portofolio.judul_portofolio);
      } else {
        console.error('Failed to fetch portofolio options:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching portofolio options:', error.message);
    }
  };

  const getSeniOptions = async () => {
    try {
      const response = await axios.get('/nama-seni');
      if (response.status === 200 && response.data.status === 'success') {
        seniOptions.value = response.data.data.map(seni => seni.nama_seni);
      } else {
        console.error('Failed to fetch seni options:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching seni options:', error.message);
    }
  };

  const getKarya = async (id) => {
    try {
      const response = await axios.get(`/karya/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        Object.assign(formData, response.data.data);
        isEdit.value = true;
      } else {
        console.error('Failed to fetch karya:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching karya:', error.message);
    }
  };

  onMounted(async () => {
    await getPortofolioOptions();
    await getSeniOptions();
    const { id } = route.params;
    if (id) {
      getKarya(id);
    }
  });

  const handleFileChange = (event) => {
    formData.media_karya = event.target.files[0];
  };

  const handleSubmit = async () => {
    const action = isEdit.value ? 'mengedit' : 'menambahkan';

    const result = await Swal.fire({
      title: `Apakah Anda yakin ingin ${action} karya ini?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
      return;
    }

    try {
      const formDataObj = new FormData();
      Object.keys(formData).forEach(key => {
        formDataObj.append(key, formData[key]);
      });

      let response;
      if (!isEdit.value) {
        response = await axios.post('/karya', formDataObj);
      } else {
        response = await axios.post(`/karya/${formData.id}`, formDataObj);
      }

      if (response.status === 200 && response.data.status === 'success') {
        toast.success(`Berhasil ${isEdit.value ? 'mengedit' : 'menambahkan'} karya!`);
        router.push({ name: 'DataKarya' });
        closeForm();
      } else {
        toast.error(response.data.message || `Gagal ${isEdit.value ? 'mengedit' : 'menambahkan'} karya!`);
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
    Object.keys(formData).forEach(key => {
      formData[key] = '';
    });
    formData.status_karya = 1;
    isEdit.value = false;
    router.push({ name: 'DataKarya' });
  };
  </script>

  <style lang="scss" scoped>
  .form-karya {
    background-color: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 2rem auto;
    text-align: center;

    h2 {
      margin-bottom: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
      text-align: left;

      label {
        display: block;
        margin-bottom: 0.5rem;
      }

      input,
      textarea,
      select {
        width: 100%;
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

      button {
        background-color: #4caf50;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 0.5rem;
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
