<template>
    <Sidebar />
    <main class="data-registrasi">
        <div class="user-management-container">
            <header class="header">
                <h2>Welcome, {{ userName }}</h2>
            </header>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Registrasi Individu</h3>
                    <router-link :to="{ name: 'IndividuAdd' }" class="button">Tambah</router-link>
                </div>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama</th>
                            <th>Tanggal Registrasi</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="registrasi_individus.length === 0">
                            <td colspan="5">No data available</td>
                        </tr>
                        <tr v-else v-for="(individu, index) in registrasi_individus" :key="individu.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ individu.nama }}</td>
                            <td>{{ formatDate(individu.created_at) }}</td>
                            <td>
                                <span v-if="individu.status_individu === 1" class="status-active">Aktif</span>
                                <span v-else class="status-inactive">Nonaktif</span>
                            </td>
                            <td>
                                <router-link :to="{ name: 'IndividuEdit', params: { id: individu.id } }" class="edit-btn material-icons">
                                    settings
                                </router-link>
                                <button @click="deleteIndividu(individu.id)" class="delete-btn">
                                    <span class="material-icons">delete</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="pagination">
                    <button @click="prevIndividuPage" :disabled="currentIndividuPage === 1">Previous</button>
                    <span>{{ currentIndividuPage }} / {{ totalIndividuPages }}</span>
                    <button @click="nextIndividuPage" :disabled="currentIndividuPage === totalIndividuPages">Next</button>
                </div>
            </div>
        </div>

        <div class="user-management-container">
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Registrasi Kelompok</h3>
                    <router-link :to="{ name: 'KelompokAdd' }" class="button">Tambah</router-link>
                </div>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama Kelompok</th>
                            <th>Tanggal Registrasi</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="registrasi_kelompoks.length === 0">
                            <td colspan="5">No data available</td>
                        </tr>
                        <tr v-else v-for="(kelompok, index) in registrasi_kelompoks" :key="kelompok.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ kelompok.nama_kelompok }}</td>
                            <td>{{ formatDate(kelompok.created_at) }}</td>
                            <td>
                                <span v-if="kelompok.status_kelompok === 1" class="status-active">Aktif</span>
                                <span v-else class="status-inactive">Nonaktif</span>
                            </td>
                            <td>
                                <router-link :to="{ name: 'KelompokEdit', params: { id: kelompok.id } }" class="edit-btn material-icons">
                                    settings
                                </router-link>
                                <button @click="deleteKelompok(kelompok.id)" class="delete-btn">
                                    <span class="material-icons">delete</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="pagination">
                    <button @click="prevKelompokPage" :disabled="currentKelompokPage === 1">Previous</button>
                    <span>{{ currentKelompokPage }} / {{ totalKelompokPages }}</span>
                    <button @click="nextKelompokPage" :disabled="currentKelompokPage === totalKelompokPages">Next</button>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarSeniman.vue';
import Swal from 'sweetalert2';
import { format } from 'date-fns';


const userName = ref(localStorage.getItem('username') || '');
const registrasi_individus = ref([]);
const registrasi_kelompoks = ref([]);
const currentIndividuPage = ref(1);
const totalIndividuPages = ref(1);
const currentKelompokPage = ref(1);
const totalKelompokPages = ref(1);
const perPage = 10;

const formatDate = (dateString) => {
    return format(new Date(dateString), 'dd/MM/yyyy');
};

// Function to load individu data
const loadIndividus = async () => {
    try {
        const response = await axios.get('/registerIndividuUser', {
            params: {
                per_page: perPage,
                page: currentIndividuPage.value
            }
        });
        console.log('Individu response:', response); // Log respons server untuk debugging

        // Periksa apakah response.data ada dan sesuai
        if (response.status === 200 && response.data && response.data.status === 'success') {
            registrasi_individus.value = response.data.data || []; // Tangani jika data tidak ada
            currentIndividuPage.value = response.data.current_page || 1; // Tangani jika current_page tidak ada
            totalIndividuPages.value = response.data.last_page || 1; // Tangani jika last_page tidak ada
        } else {
            console.error('Failed to load individus data.');
        }
    } catch (error) {
        console.error('Error:', error.message);
        Swal.fire('Error', 'Failed to load individus data.', 'error');
    }
};

// Function to load kelompok data
const loadKelompoks = async () => {
    try {
        const response = await axios.get('/registerKelompokUser', {
            params: {
                per_page: perPage,
                page: currentKelompokPage.value
            }
        });
        console.log('Kelompok response:', response); // Log respons server untuk debugging

   
        if (response.status === 200 && response.data && response.data.status === 'success') {
            registrasi_kelompoks.value = response.data.data || []; // Tangani jika data tidak ada
            currentKelompokPage.value = response.data.current_page || 1; // Tangani jika current_page tidak ada
            totalKelompokPages.value = response.data.last_page || 1; // Tangani jika last_page tidak ada
        } else {
            console.error('Failed to load kelompoks data.');
        }
    } catch (error) {
        console.error('Error:', error.message);
        Swal.fire('Error', 'Failed to load kelompoks data.', 'error');
    }
};

// Function to delete individu
const deleteIndividu = async (id) => {
    console.log("Deleting individu with id:", id);
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus registrasi individu ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }
    try {
        const response = await axios.delete(`/registerIndividu/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Registrasi individu berhasil dihapus', '', 'success');
            loadIndividus(); // Refresh data setelah hapus
        } else {
            Swal.fire('Gagal menghapus registrasi individu', response.data.message, 'error');
            console.error('Gagal menghapus registrasi individu:', response.data.message);
        }
    } catch (error) {
        Swal.fire('Error menghapus registrasi individu', error.message, 'error');
        console.error('Error menghapus registrasi individu:', error.message);
    }
};

// Function to delete kelompok
const deleteKelompok = async (id) => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus registrasi kelompok ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await axios.delete(`/registerKelompok/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Registrasi kelompok berhasil dihapus', '', 'success');
            loadKelompoks(); // Refresh data setelah hapus
        } else {
            Swal.fire('Gagal menghapus registrasi kelompok', response.data.message, 'error');
            console.error('Gagal menghapus registrasi kelompok:', response.data.message);
        }
    } catch (error) {
        Swal.fire('Error menghapus registrasi kelompok', error.message, 'error');
        console.error('Error menghapus registrasi kelompok:', error.message);
    }
};

// Pagination functions
const nextIndividuPage = () => {
    if (currentIndividuPage.value < totalIndividuPages.value) {
        currentIndividuPage.value += 1;
        loadIndividus();
    }
};

const prevIndividuPage = () => {
    if (currentIndividuPage.value > 1) {
        currentIndividuPage.value -= 1;
        loadIndividus();
    }
};

const nextKelompokPage = () => {
    if (currentKelompokPage.value < totalKelompokPages.value) {
        currentKelompokPage.value += 1;
        loadKelompoks();
    }
};

const prevKelompokPage = () => {
    if (currentKelompokPage.value > 1) {
        currentKelompokPage.value -= 1;
        loadKelompoks();
    }
};

// Load data on component mount
onMounted(() => {
    loadIndividus();
    loadKelompoks();
});
</script>
<style lang="scss" scoped>
    .data-registrasi {
        background-color: #f5d99d;

        .user-management-container {
        background-color: #f5d99d;
        padding: 2rem;
        }

        .header {
        margin-bottom: 1rem;

        h2 {
            color: #000;
        }
        }

        .table-wrapper {
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;

        h3 {
            margin: 0;
        }

        .button {
            background-color: #f7941e;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;

            &:hover {
            background-color: #e6830d;
            }
        }
        }

        .user-table {
        width: 100%;
        border-collapse: collapse;

        th {
                background-color: #f5d99d;
                text-align: center;
                border: 1px solid #ccc;
                padding: 0.5rem;
            }

        td {
            border: 1px solid #ccc;
            padding: 0.5rem;
            text-align: center;
        }

        .edit-btn, .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            color: #fff;
            border: none;
            padding: 0.3rem;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 0.3rem;
            height: 2rem;
            width: 2rem;

            .material-icons {
            font-size: 1.5rem;
            }
        }

        .edit-btn {
            background-color: #4caf50;

            &:hover {
            background-color: #45a049;
            }

            .material-icons {
            color: #fff;
            }
        }

        .delete-btn {
            background-color: #f44336;

            &:hover {
            background-color: #e53935;
            }

            .material-icons {
            color: #fff;
            }
        }
        }

        .pagination {
        display: flex;
        justify-content: center; /* Center align buttons */
        align-items: center;
        margin-top: 2rem; /* Add space above pagination */

        button {
            background-color: #f7941e;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 0.5rem;

            &:hover {
                background-color: #e6830d;
            }
            &:disabled {
                background-color: #ccc;
                cursor: not-allowed;
            }
        }

        span {
            margin: 0 0.5rem;
        }
        }
    }
</style>
