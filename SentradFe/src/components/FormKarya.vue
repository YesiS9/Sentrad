<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h3>{{ mode === 'add' ? 'Tambah Karya' : 'Edit Karya' }}</h3>
                <form @submit.prevent="handleSubmit">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="judul_karya">Judul Karya</label>
                            <input type="text" id="judul_karya" v-model="formData.judul_karya" placeholder="Judul Karya" required>
                        </div>
                        <div class="form-group">
                            <label for="bentuk_karya">Bentuk Karya (Misal: dimensi untuk lukisan, atau jenis tari untuk tari)</label>
                            <input type="text" id="bentuk_karya" v-model="formData.bentuk_karya" placeholder="Bentuk Karya" required>
                        </div>
                        <div class="form-group">
                            <label for="tgl_pembuatan">Tanggal Pembuatan</label>
                            <input type="date" id="tgl_pembuatan" v-model="formData.tgl_pembuatan" required>
                        </div>
                        <div class="form-group">
                            <label for="media_karya">Media Karya (Foto atau video)</label>
                            <input type="file" id="media_karya" @change="handleFileChange" accept="image/*,video/*" multiple>
                            <small v-if="errors.media_karya.length" class="error-text">{{ errors.media_karya.join(', ') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_karya">Deskripsi Karya</label>
                            <textarea id="deskripsi_karya" v-model="formData.deskripsi_karya" placeholder="Deskripsi Karya" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="status_karya">Status Karya</label>
                            <select id="status_karya" v-model="formData.status_karya" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
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
    portofolio_id: '',
    judul_karya: '',
    deskripsi_karya: '',
    tgl_pembuatan: '',
    media_karya: [],
    bentuk_karya: '',
    status_karya: '1',
});
const errors = reactive({
    media_karya: [],
});
const route = useRoute();
const router = useRouter();
const mode = ref('add');
const toast = useToast();
const isEdit = ref(false);

const getKarya = async (id) => {
  try {
    const response = await axios.get(`/karya/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      Object.assign(formData, response.data.data);
      console.log('respon:', formData);
      isEdit.value = true;
    } else {
      console.error('Failed to fetch karya:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching karya:', error.message);
  }
};

onMounted(async () => {
  const { id, portofolioId } = route.params;
  formData.portofolio_id = portofolioId;
  if (id) {
    getKarya(id);
  }
});

const handleFileChange = (event) => {
  const files = Array.from(event.target.files);
  formData.media_karya = files;
  console.log('Files selected:', files); // Debugging file input
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
      if (key === 'tgl_pembuatan') {
        const formattedDate = new Date(formData[key]).toLocaleDateString('en-GB');
        formDataObj.append(key, formattedDate);
      } else if (key === 'media_karya' && formData[key].length > 0) {
        formData[key].forEach(file => formDataObj.append('media_karya[]', file)); // Handle multiple files
      } else {
        formDataObj.append(key, formData[key]);
      }
    });

    for (let pair of formDataObj.entries()) {
      console.log(`${pair[0]}: ${pair[1]}`);
    }

    let response;
    if (!isEdit.value) {
      response = await axios.post('/karya', formDataObj, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
    } else {
      response = await axios.post(`/karya/${formData.id}`, formDataObj, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
    }

    if (response.status === 200 && response.data.status === 'success') {
      toast.success(`Berhasil ${isEdit.value ? 'mengedit' : 'menambahkan'} karya!`);
      router.push({ name: 'Portofolio' });
      closeForm();
    } else {
      toast.error(response.data.message || `Gagal ${isEdit.value ? 'mengedit' : 'menambahkan'} karya!`);
    }
  } catch (error) {
    console.error('Error saving data:', error.message);
    if (error.response) {
      console.error('Server response:', error.response.data);
      if (error.response.data.message) {
        for (const [key, value] of Object.entries(error.response.data.message)) {
          errors[key] = value; // Update error messages
        }
      }
      toast.error('Terjadi kesalahan saat menyimpan data!');
    } else {
      toast.error('Terjadi kesalahan saat menyimpan data!');
    }
  }
};

const closeForm = () => {
  Object.keys(formData).forEach(key => {
    formData[key] = '';
  });
  formData.status_karya = '1';
  isEdit.value = false;
  router.push({ name: 'Portofolio' });
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
    height: 100vw;
    max-width: 650px;
    max-height: 600px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
h3 {
        margin-bottom: 1rem;
        }

.form-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        width: 100%;
        }
.form-group {
        margin-bottom: 1rem;
        text-align: left;
        width: calc(50% - 0.5rem);
        }
label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #555;
}
input[type="text"],
input[type="date"],
input[type="file"],
textarea,
select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    color: #333;
}
input[type="file"] {
    padding: 0;
}
textarea {
    height: 100px;
    resize: vertical;
}
.form-actions {
    margin-top: 1rem;
        text-align: right;
        width: 100%;
}
button {
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
    background-color: #d32f2f;
}
</style>
