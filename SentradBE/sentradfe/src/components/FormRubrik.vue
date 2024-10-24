<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h3>{{ mode === 'add' ? 'Tambah Rubrik' : 'Edit Rubrik' }}</h3>
                <form @submit.prevent="handleSubmit">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama_rubrik">Nama Rubrik</label>
                            <input type="text" id="nama_rubrik" v-model="formData.nama_rubrik" placeholder="Nama Rubrik" required>
                        </div>
                        <div class="form-group">
                            <label for="bobot">Bobot</label>
                            <input type="number" id="bobot" v-model="formData.bobot" placeholder="Bobot" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="deskripsi_rubrik">Deskripsi Rubrik</label>
                            <textarea id="deskripsi_rubrik" v-model="formData.deskripsi_rubrik" placeholder="Deskripsi Rubrik" required></textarea>
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
    nama_rubrik: '',
    deskripsi_rubrik: '',
    bobot: '',
    penilai_id: ''
});

const route = useRoute();
const router = useRouter();
const mode = ref('add');
const toast = useToast();


const getRubrik = async (id) => {
    try {
        const response = await axios.get(`/rubrik/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Object.assign(formData, response.data.data);
            mode.value = 'edit';
        } else {
            console.error('Failed to fetch rubrik:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching rubrik:', error.message);
    }
};

onMounted(async () => {
    formData.penilai_id = localStorage.getItem('penilai_id');

    const { id } = route.params;
    if (id) {
        getRubrik(id);
    }
});

const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    const result = await Swal.fire({
        title: `Apakah Anda yakin ingin ${action} rubrik ini?`,
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
            response = await axios.post('/rubrik', formData);
        } else if (mode.value === 'edit' && formData.id) {
            response = await axios.put(`/rubrik/${formData.id}`, formData);
        } else {
            console.error('Invalid mode or missing formData.id for edit.');
            return;
        }

        if (response.status === 200 && response.data.status === 'success') {
            toast.success(`Berhasil ${mode.value === 'add' ? 'menambahkan' : 'mengedit'} rubrik!`);
            router.push({ name: 'Rubrik' });
            closeForm();
        } else {
            console.error(`Gagal ${action} rubrik:`, response.data.message);
        }
    } catch (error) {
        console.error(`Error saat ${action} rubrik:`, error.message);
    }
};

const closeForm = () => {
    formData.nama_rubrik = '',
    formData.deskripsi_rubrik = '',
    formData.bobot = '',
    mode.value = 'add';
    router.push({ name: 'Rubrik' });
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
        flex-wrap: wrap;
        justify-content: space-between;
        width: 100%;
    }

    .form-group {
        margin-bottom: 1rem;
        text-align: left;
        width: calc(100% - 0.5rem);
    }

    input[type="text"],
    input[type="number"],
    textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    textarea {
        height: 100px;
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
</style>
