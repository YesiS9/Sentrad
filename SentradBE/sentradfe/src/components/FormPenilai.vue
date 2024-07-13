<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
            <h3>{{ mode === 'add' ? 'Tambah Penilai' : 'Edit Penilai' }}</h3>
            <form @submit.prevent="handleSubmit">
                <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" v-model="formData.username" placeholder="Username" :readonly="mode === 'edit'" required>
                </div>
                <div class="form-group">
                    <label for="bidang_ahli">Bidang Ahli</label>
                    <input type="text" id="bidang_ahli" v-model="formData.bidang_ahli" placeholder="Bidang Ahli" required>
                </div>
                <div class="form-group">
                    <label for="nama_penilai">Nama Penilai</label>
                    <input type="text" id="nama_penilai" v-model="formData.nama_penilai" placeholder="Nama Penilai" required>
                </div>
                <div class="form-group">
                    <label for="lembaga">Lembaga</label>
                    <input type="text" id="lembaga" v-model="formData.lembaga" placeholder="Lembaga" required>
                </div>
                </div>
                <div class="form-row">
                <div class="form-group">
                    <label for="alamat_penilai">Alamat Penilai</label>
                    <input type="text" id="alamat_penilai" v-model="formData.alamat_penilai" placeholder="Alamat Penilai" required>
                </div>
                <div class="form-group">
                    <label for="tgl_lahir">Tanggal Lahir</label>
                    <input type="date" id="tgl_lahir" v-model="formData.tgl_lahir" placeholder="Tanggal Lahir" required>
                </div>
                <div class="form-group">
                    <label for="noTelp_penilai">No. Telp</label>
                    <input type="text" id="noTelp_penilai" v-model="formData.noTelp_penilai" placeholder="No. Telp" required>
                </div>
                <div class="form-group">
                    <label for="status_penilai">Status Penilai</label>
                    <select id="status_penilai" v-model="formData.status_penilai" required>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
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
    import Swal from 'sweetalert2';

    const formData = reactive({
        username: '',
        nama_penilai: '',
        alamat_penilai: '',
        noTelp_penilai: '',
        bidang_ahli: '',
        lembaga: '',
        tgl_lahir: '',
        status_penilai: ''
    });

    const route = useRoute();
    const router = useRouter();
    const mode = ref('add');

    const getPenilai = async (id) => {
        try {
        console.log('Fetching penilai with id:', id); // Log the id being used
        const response = await axios.get(`/penilai/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            const penilaiData = response.data.data;
            Object.assign(formData, penilaiData);
            console.log('Fetched penilai data:', penilaiData); // Log the fetched data

            const userResponse = await axios.get(`/user/${penilaiData.user_id}`);
            if (userResponse.status === 200 && userResponse.data.status === 'success') {
            formData.username = userResponse.data.data.username;
            console.log('Fetched user data:', userResponse.data.data); // Log the fetched user data
            } else {
            console.error('Failed to fetch user:', userResponse.data.message);
            }

            mode.value = 'edit';
        } else {
            console.error('Failed to fetch penilai:', response.data.message);
        }
        } catch (error) {
        console.error('Error fetching penilai:', error.message);
        }
    };

    onMounted(() => {
        const { id } = route.params;
        if (id) {
        getPenilai(id);
        }
    });

    const formatDate = (date) => {
        const [year, month, day] = date.split('-');
        return `${day}/${month}/${year}`;
    };

    const handleSubmit = async () => {
        const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

        const result = await Swal.fire({
            title: `Apakah Anda yakin ingin ${action} penilai ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        });

        if (!result.isConfirmed) {
            return;
        }
        try {
            const formattedData = { ...formData, tgl_lahir: formatDate(formData.tgl_lahir) };
            console.log('Formatted Data:', formattedData);

            let response;
            if (mode.value === 'add') {
                response = await axios.post('/penilai', formattedData);
            } else if (mode.value === 'edit' && formData.id) {
                response = await axios.put(`/penilai/${formData.id}`, formattedData);
            } else {
                console.error('Invalid mode or missing formData.id for edit.');
            return;
            }

            if (response.status === 200 && response.data.status === 'success') {
                if (mode.value === 'add') {
                    console.log('Adding penilai:', response.data.data);
                } else {
                    console.log('Editing penilai:', response.data.data);
                }
                router.push({ name: 'DataPenilai' });
                closeForm();
            } else {
                console.error(mode.value === 'add' ? 'Failed to add penilai:' : 'Failed to edit penilai:', response.data.message);
            }
        } catch (error) {
            console.error('Error saving data:', error.message);
            if (error.response) {
            console.error('Server response:', error.response.data);
            }
        }
    };


    const closeForm = () => {
        formData.username = '';
        formData.nama_penilai = '';
        formData.alamat_penilai = '';
        formData.noTelp_penilai = '';
        formData.bidang_ahli = '';
        formData.lembaga = '';
        formData.tgl_lahir = '';
        formData.status_penilai = '';
        mode.value = 'add';
        router.push({ name: 'DataPenilai' });
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
        width: calc(50% - 0.5rem);
        }

        input[type="text"],
        input[type="date"],
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

        button[type="submit"] {
        background-color: #4caf50;
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
