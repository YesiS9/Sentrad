<template>
    <div class="page">
        <Sidebar />
        <main class="data-portofolio">
            <div class="user-management-container">
                <div v-if="registrasiKelompok" class="registration-info">
                    <h3>Data Registrasi Kelompok</h3>
                    <p><strong>Nama Kelompok:</strong> {{ registrasiKelompok.nama_kelompok }}</p>
                    <p><strong>Tanggal Terbentuk:</strong> {{ registrasiKelompok.tgl_terbentuk }}</p>
                    <p><strong>Alamat Kelompok:</strong> {{ registrasiKelompok.alamat_kelompok }}</p>
                    <p><strong>Jumlah Anggota:</strong> {{ registrasiKelompok.jumlah_anggota }}</p>
                    <p><strong>Kategori Seni:</strong> {{ registrasiKelompok.kategori_seni.nama_kategori }}</p>
                </div>
                <div v-if="anggotaKelompok.length > 0" class="table-wrapper">
                    <div class="table-header">
                        <h3>Data Anggota Kelompok</h3>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Anggota</th>
                                <th>Tanggal Lahir</th>
                                <th>Tanggal Bergabung</th>
                                <th>Tingkat Skill</th>
                                <th>Peran Anggota</th>
                                <th>Status Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(anggota, index) in anggotaKelompok" :key="anggota.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ anggota.nama_anggota }}</td>
                                <td>{{ anggota.tgl_lahir }}</td>
                                <td>{{ anggota.tgl_gabung }}</td>
                                <td>{{ anggota.tingkat_skill }}</td>
                                <td>{{ anggota.peran_anggota }}</td>
                                <td>{{ anggota.status_anggota }}</td>
                            </tr>
                            <tr v-if="anggotaKelompok.length === 0">
                                <td colspan="7" class="no-data">Tidak ada anggota yang terdaftar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Portofolio Kelompok</h3>
                        <button @click="goBack" class="back-button">Back</button>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Judul Portofolio</th>
                                <th>Jumlah Karya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(portofolio, index) in portofolios" :key="portofolio.id">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <router-link :to="{ name: 'InfoPortoKelompok', params: { id: portofolio.id }, query: { kelompokId: props.kelompokId } }" class="portfolio-link">
                                        {{ portofolio.judul_portofolio }}
                                    </router-link>
                                </td>
                                <td>{{ portofolio.jumlah_karya }}</td>
                            </tr>
                            <tr v-if="portofolios.length === 0">
                                <td colspan="3" class="no-data">Portofolio kosong</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination" v-if="portofolios.length > 0">
                        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
                        <span>{{ currentPage }} / {{ totalPages }}</span>
                        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../services/api.js';
import Sidebar from './SidebarPenilai.vue';

const props = defineProps({
  kelompokId: String,
});

const portofolios = ref([]);
const registrasiKelompok = ref(null);
const anggotaKelompok = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;
const router = useRouter();

onMounted(() => {
    console.log('Kelompok ID:', props.kelompokId);
    if (props.kelompokId) {
        loadRegistrasiKelompok();
        loadPortofolios();
        loadAnggotaKelompok();
    } else {
        router.push({ name: 'PenilaianKarya' });
        console.error('No kelompokId provided');
    }
});

const loadRegistrasiKelompok = async () => {
    try {
        const response = await axios.get(`/registerKelompok/${props.kelompokId}`); // Use props.kelompokId here
        if (response.data.status === 'success') {
            registrasiKelompok.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching group registration data:', error.message);
    }
};

const loadPortofolios = async () => {
    try {
        const response = await axios.get('/registrasi-portofolio/kelompok', { params: { kelompok_id: props.kelompokId, per_page: perPage, page: currentPage.value } }); // Use props.kelompokId here
        if (response.status === 200 && response.data.status === 'success') {
            portofolios.value = response.data.data;
            currentPage.value = response.data.current_page;
            totalPages.value = response.data.last_page;
        } else {
            console.error('Failed to load portfolio data:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching portfolio data:', error.message);
    }
};

const loadAnggotaKelompok = async () => {
    try {
        const response = await axios.get(`/anggota/kelompok/${props.kelompokId}`); // Use props.kelompokId here
        if (response.status === 200 && response.data.status === 'success') {
            anggotaKelompok.value = response.data.data;
        } else {
            console.error('Failed to load anggota kelompok:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching anggota kelompok data:', error.message);
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        loadPortofolios();
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        loadPortofolios();
    }
};

const goBack = () => {
    router.push({ name: 'PenilaianKarya' });
};
</script>

<style lang="scss" scoped>
.page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.no-data {
    text-align: center;
    font-style: italic;
    color: #888;
}

.back-button {
    background-color: #f7941e;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    display: inline-block;

    &:hover {
        background-color: #e6830d;
    }
}

.registration-info {
    margin-bottom: 1rem;
}

.data-portofolio {
    background-color: #f5d99d;

    .user-management-container {
        background-color: #f5d99d;
        padding: 2rem;
    }

    .table-wrapper {
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;  // Add margin between cards
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;

        h3 {
            margin: 0;
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
