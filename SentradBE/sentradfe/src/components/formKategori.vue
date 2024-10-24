<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h3>{{ mode === 'add' ? 'Tambah Kategori Seni' : 'Edit Kategori Seni' }}</h3>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori Seni</label>
                        <input type="text" id="nama_kategori" v-model="formData.nama_kategori" placeholder="Nama Kategori Seni" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_kategori">Deskripsi Kategori</label>
                        <textarea id="deskripsi_kategori" v-model="formData.deskripsi_kategori" placeholder="Deskripsi Kategori" rows="3" required></textarea>
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
    user_id: '',
    nama_kategori: '',
    deskripsi_kategori: ''
});

const route = useRoute();
const router = useRouter();
const mode = ref('add');
const toast = useToast();

onMounted(() => {
    const userId = localStorage.getItem('user_id');
    if (userId) {
        formData.user_id = userId;
    } else {
        console.error('User ID not found in local storage.');
    }

    const id = route.params.id;
    if (id) {
        getKategori(id);
    }
});

const getKategori = async (id) => {
    try {
        const response = await axios.get(`/kategoriSeni/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Object.assign(formData, response.data.data);
            mode.value = 'edit';
        } else {
            console.error('Failed to fetch kategori:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching kategori:', error.message);
    }
};

const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    if (mode.value === 'edit') {
        const result = await Swal.fire({
            title: `Apakah Anda yakin ingin ${action} kategori seni ini?`,
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

        // Perform the add or edit operation based on the mode
        if (mode.value === 'add') {
            response = await axios.post('/kategoriSeni', formData);
        } else {
            response = await axios.put(`/kategoriSeni/${formData.id}`, formData);
        }

        // Handle the response
        if (response.status === 200 && response.data.status === 'success') {
            toast.success(`Berhasil ${action} kategori seni!`);
            router.push({ name: 'DataKategori' });
            closeForm();
        } else {
            toast.error(response.data.message || `Gagal ${action} kategori seni!`);
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
    formData.user_id = '',
    formData.nama_kategori = '',
    formData.deskripsi_kategori = ''
    mode.value = 'add';
    router.push({ name: 'DataKategori' });
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
        textarea {
            width: 35vw;
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
