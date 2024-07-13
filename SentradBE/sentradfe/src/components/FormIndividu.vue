<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h3>{{ mode === 'add' ? 'Tambah Individu' : 'Edit Individu' }}</h3>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="nama_seniman">Seniman</label>
                        <select id="nama_seniman" v-model="formData.nama_seniman" required>
                            <option value="">Pilih Seniman</option>
                            <option v-for="seniman in senimans" :key="seniman.id" :value="seniman.nama_seniman">{{ seniman.nama_seniman }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" v-model="formData.nama" placeholder="Nama" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                        <input type="date" id="tgl_lahir" v-model="formData.tgl_lahir" placeholder="Tanggal Lahir" required>
                    </div>

                    <div class="form-group">
                        <label for="tgl_mulai">Tanggal Mulai</label>
                        <input type="date" id="tgl_mulai" v-model="formData.tgl_mulai" placeholder="Tanggal Mulai" required>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" id="alamat" v-model="formData.alamat" placeholder="Alamat" required>
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
    nama_seniman: '',
    nama: '',
    tgl_lahir: '',
    tgl_mulai: '',
    alamat: '',
    status: 1 // Set default status to 1
});

const senimans = ref([]);

const route = useRoute();
const router = useRouter();
const mode = ref('add');

const getSeniman = async () => {
    try {
        const response = await axios.get('/seniman');
        senimans.value = response.data;
    } catch (error) {
        console.error('Error fetching senimans:', error.message);
    }
};

const getIndividu = async (id) => {
    try {
        const response = await axios.get(`/register-individu/${id}`);
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
    await getSeniman(); // Fetch roles when component mounts

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
    try {
        const formattedData = {
            ...formData,
            tgl_lahir: formatDate(formData.tgl_lahir),
            tgl_mulai: formatDate(formData.tgl_mulai)
        };
        console.log('Formatted Data:', formattedData);
        let response;
        if (mode.value === 'add') {
            response = await axios.post('/register-individu', formattedData);
        } else if (mode.value === 'edit' && formData.id) {
            response = await axios.put(`/register-individu/${formData.id}`, formattedData);
        } else {
            console.error('Invalid mode or missing formData.id for edit.');
            return;
        }

        if (response.status === 200 && response.data.status === 'success') {
            router.push({ name: 'DataIndividu' });
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
    formData.nama_seniman = '';
    formData.nama = '';
    formData.tgl_lahir = '';
    formData.tgl_mulai = '';
    formData.alamat = '';
    formData.status = 1;
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
        width: 40vw;
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
