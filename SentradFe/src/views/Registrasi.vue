<template>
    <div class="registrasi-page">
        <Sidebar />
        <main class="data-registrasi">
            <div class="user-management-container">
                <header class="header">
                    <h2>Welcome, {{ userName }}</h2>
                </header>
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Registrasi Individu</h3>
                        <button @click="checkPortfolioAndAddIndividu" class="button">Tambah</button>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama</th>
                                <th>Tanggal Registrasi</th>
                                <th>Status</th>
                                <th>Hasil Penilaian</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="registrasi_individus.length === 0">
                                <td colspan="6">No data available</td>
                            </tr>
                            <tr v-else v-for="(individu, index) in registrasi_individus" :key="individu.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ individu.nama }}</td>
                                <td>{{ formatDate(individu.created_at) }}</td>
                                <td>{{ individu.status_individu }}</td>
                                <td>
                                    <span v-if="individu.status_individu === 'Penilaian Selesai'">
                                        <router-link :to="{ name: 'HasilPenilaian', params: { id: individu.id } }">
                                            Lihat Penilaian
                                        </router-link>
                                    </span>
                                    <span v-else>
                                        Belum ada hasil
                                    </span>
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
                                <th>Hasil Penilaian</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="registrasi_kelompoks.length === 0">
                                <td colspan="6">No data available</td>
                            </tr>
                            <tr v-else v-for="(kelompok, index) in registrasi_kelompoks" :key="kelompok.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ kelompok.nama_kelompok }}</td>
                                <td>{{ formatDate(kelompok.created_at) }}</td>
                                <td>{{ kelompok.status_kelompok }}</td>
                                <td>
                                    <span v-if="kelompok.status_kelompok === 'Penilaian Selesai'">
                                        <router-link :to="{ name: 'HasilPenilaian', params: { id: kelompok.id } }">
                                            Lihat Penilaian
                                        </router-link>
                                    </span>
                                    <span v-else>
                                        Belum ada hasil
                                    </span>
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarSeniman.vue';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();

const userName = ref(localStorage.getItem('username') || '');
const registrasi_individus = ref([]);
const registrasi_kelompoks = ref([]);
const currentIndividuPage = ref(1);
const totalIndividuPages = ref(1);
const currentKelompokPage = ref(1);
const totalKelompokPages = ref(1);
const perPage = 10;

const formatDate = (dateString) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', options);
};

const checkPortfolioAndAddIndividu = async () => {
    try {
        const response = await axios.get('/portofolio');
        const individuResponse = await axios.get('/registerIndividuUser');

        const portofolioData = response.data?.data?.individual_portfolios || {};
        const individuData = individuResponse.data?.data?.items || [];

        // Periksa apakah sudah ada registrasi individu
        if (individuData.length > 0) {
            Swal.fire('Error', 'Anda sudah memiliki registrasi individu. Tidak dapat menambah lagi.', 'error');
            return;
        }

        // Periksa apakah portofolio individu kosong
        if (portofolioData.total > 0) {
            router.push({ name: 'IndividuAdd' });
        } else {
            Swal.fire('Error', 'Portofolio individu tidak ditemukan. Silakan buat portofolio terlebih dahulu.', 'error');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal memeriksa data portofolio', 'error');
    }
};



const loadIndividus = async () => {
    try {
        const response = await axios.get('/registerIndividuUser', {
            params: {
                per_page: perPage,
                page: currentIndividuPage.value
            }
        });

        if (response.status === 200 && response.data && response.data.status === 'success') {
            registrasi_individus.value = response.data.data || [];
            currentIndividuPage.value = response.data.current_page || 1;
            totalIndividuPages.value = response.data.last_page || 1;
        } else {
            console.error('Failed to load individus data.');
        }
    } catch (error) {
        console.error('Error:', error.message);
        Swal.fire('Error', 'Failed to load individus data.', 'error');
    }
};

const loadKelompoks = async () => {
    try {
        const response = await axios.get('/registerKelompokUser', {
            params: {
                per_page: perPage,
                page: currentKelompokPage.value
            }
        });

        if (response.status === 200 && response.data && response.data.status === 'success') {
            registrasi_kelompoks.value = response.data.data || [];
            currentKelompokPage.value = response.data.current_page || 1;
            totalKelompokPages.value = response.data.last_page || 1;
        } else {
            console.error('Failed to load kelompoks data.');
        }
    } catch (error) {
        console.error('Error:', error.message);
        Swal.fire('Error', 'Failed to load kelompoks data.', 'error');
    }
};

const deleteIndividu = async (id) => {
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
            loadIndividus();
        } else {
            Swal.fire('Gagal menghapus registrasi individu', response.data.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error menghapus registrasi individu', error.message, 'error');
    }
};


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
            loadKelompoks();
        } else {
            Swal.fire('Gagal menghapus registrasi kelompok', response.data.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error menghapus registrasi kelompok', error.message, 'error');
    }
};


const nextIndividuPage = () => {
    if (currentIndividuPage.value < totalIndividuPages.value) {
        currentIndividuPage.value++;
        loadIndividus();
    }
};

const prevIndividuPage = () => {
    if (currentIndividuPage.value > 1) {
        currentIndividuPage.value--;
        loadIndividus();
    }
};

const nextKelompokPage = () => {
    if (currentKelompokPage.value < totalKelompokPages.value) {
        currentKelompokPage.value++;
        loadKelompoks();
    }
};

const prevKelompokPage = () => {
    if (currentKelompokPage.value > 1) {
        currentKelompokPage.value--;
        loadKelompoks();
    }
};

onMounted(() => {
    loadIndividus();
    loadKelompoks();
});
</script>



<style lang="scss" scoped>
.registrasi-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
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
        justify-content: center;
        align-items: center;
        margin-top: 2rem;

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
