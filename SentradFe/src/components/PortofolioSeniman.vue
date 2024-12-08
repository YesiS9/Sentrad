<template>
    <div class="page">
        <Sidebar />
        <main class="data-portofolio">
            <div class="user-management-container">
                <div v-if="registrasiIndividu" class="registration-info">
                    <h3>Detail Registrasi Individu</h3>
                    <p><strong>Nama Seniman:</strong> {{ registrasiIndividu.nama }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ registrasiIndividu.tgl_lahir }}</p>
                    <p><strong>Tanggal Mulai Berkarya:</strong> {{ registrasiIndividu.tgl_mulai }}</p>
                    <p><strong>Alamat:</strong> {{ registrasiIndividu.alamat }}</p>
                    <p><strong>Kategori Seni:</strong> {{ registrasiIndividu.kategori_seni.nama_kategori }}</p>
                </div>

                <!-- Portfolio Data Display -->
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Portofolio Individu</h3>
                        <button @click="goBack" class="back-button">Back</button>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Seniman</th>
                                <th>Judul Portofolio</th>
                                <th>Jumlah Karya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(portofolio, index) in portofolios" :key="portofolio.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ portofolio.seniman.nama_seniman }}</td>
                                <td>
                                    <router-link :to="{ name: 'InfoPortofolioPenilai', params: { id: portofolio.id }, query: { individuId } }" class="portfolio-link">
                                        {{ portofolio.judul_portofolio }}
                                    </router-link>
                                </td>
                                <td>{{ portofolio.jumlah_karya }}</td>
                            </tr>
                            <tr v-if="portofolios.length === 0">
                                <td colspan="4" class="no-data">Portofolio kosong</td>
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
import Sidebar from '../components/SidebarPenilai.vue';

const props = defineProps({
    individuId: String,
});

const portofolios = ref([]);
const registrasiIndividu = ref(null);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;
const router = useRouter();

onMounted(() => {
    if (props.individuId) {
        loadRegistrasiIndividu();
        loadPortofolios('/registrasi-portofolio/individu', { individu_id: props.individuId });
    }
});

const loadRegistrasiIndividu = async () => {
    try {
        const response = await axios.get(`/registerIndividu/${props.individuId}`);
        if (response.data.status === 'success') {
            registrasiIndividu.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching individual registration data:', error.message);
    }
};

const loadPortofolios = async (endpoint, params) => {
    try {
        const response = await axios.get(endpoint, { params: { ...params, per_page: perPage, page: currentPage.value } });
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

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        loadPortofolios('/registrasi-portofolio/individu', { individu_id: props.individuId });
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        loadPortofolios('/registrasi-portofolio/individu', { individu_id: props.individuId });
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
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;

        h3 {
            margin: 0;
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
