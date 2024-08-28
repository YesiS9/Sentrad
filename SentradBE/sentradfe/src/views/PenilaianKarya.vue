<template>
    <Sidebar />
    <main class="data-penilaian-karya">
        <div class="penilaian-management-container">
            <header class="header">
                <h2>Penilaian Karya Management</h2>
            </header>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Daftar Penilaian Karya</h3>
                </div>
                <table class="penilaian-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Tanggal Penilaian</th>
                            <th>Nama Seniman/Kelompok</th>
                            <th>Total Nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(penilaian, index) in penilaians" :key="penilaian.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ formatDate(penilaian.tgl_penilaian) }}</td>
                            <td>{{ penilaian.nama_seniman || penilaian.nama_kelompok }}</td>
                            <td>{{ penilaian.total_nilai }}</td>
                            <td>
                                <router-link :to="{ name: 'editPenilaian', params: { id: penilaian.id } }" class="edit-btn material-icons">
                                    settings
                                </router-link>
                                <button @click="deletePenilaian(penilaian.id)" class="delete-btn">
                                    <span class="material-icons">delete</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="penilaians.length === 0">
                            <td colspan="5" class="no-data">Data Penilaian Kosong</td>
                        </tr>
                    </tbody>
                </table>
                <div class="pagination" v-if="penilaians.length > 0">
                    <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
                    <span>{{ currentPage }} / {{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Daftar Registrasi Individu</h3>
                </div>
                <!-- Registrasi Individu Table -->
                <table class="registrasi-individu-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama Seniman</th>
                            <th>Nama Karya</th>
                            <th>Info Karya</th> <!-- New Column -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(individu, index) in registrasiIndividu" :key="individu.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ individu.seniman.nama_seniman }}</td>
                            <td>{{ individu.nama_karya }}</td>
                            <td>
                                <router-link :to="{ name: 'PortofolioSeniman', params: { id: individu.portofolio_id } }" class="button">Lihat Karya</router-link>
                            </td>
                            <td>
                                <router-link :to="{ name: '' }" class="button">Tambah</router-link>
                            </td>
                        </tr>
                        <tr v-if="registrasiIndividu.length === 0">
                            <td colspan="5" class="no-data">Data Registrasi Individu Kosong</td>
                        </tr>
                    </tbody>
                </table>

                <div class="pagination" v-if="registrasiIndividu.length > 0">
                    <button @click="prevIndividuPage" :disabled="individuCurrentPage === 1">Previous</button>
                    <span>{{ individuCurrentPage }} / {{ individuTotalPages }}</span>
                    <button @click="nextIndividuPage" :disabled="individuCurrentPage === individuTotalPages">Next</button>
                </div>
            </div>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Daftar Registrasi Kelompok</h3>
                </div>
                <!-- Registrasi Kelompok Table -->
                <table class="registrasi-kelompok-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama Seniman</th>
                            <th>Nama Kelompok</th>
                            <th>Info Karya</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(kelompok, index) in registrasiKelompok" :key="kelompok.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ kelompok.seniman.nama_seniman }}</td>
                            <td>{{ kelompok.nama_kelompok }}</td>
                            <td>
                                <router-link :to="{ name: 'PortofolioSeniman', params: { id: kelompok.portofolio_id } }" class="button">Lihat Karya</router-link>
                            </td>
                            <td>
                                <router-link :to="{ name: '' }" class="button">Tambah</router-link>
                            </td>
                        </tr>
                        <tr v-if="registrasiKelompok.length === 0">
                            <td colspan="5" class="no-data">Data Registrasi Kelompok Kosong</td>
                        </tr>
                    </tbody>
                </table>

                <div class="pagination" v-if="registrasiKelompok.length > 0">
                    <button @click="prevKelompokPage" :disabled="kelompokCurrentPage === 1">Previous</button>
                    <span>{{ kelompokCurrentPage }} / {{ kelompokTotalPages }}</span>
                    <button @click="nextKelompokPage" :disabled="kelompokCurrentPage === kelompokTotalPages">Next</button>
                </div>
            </div>
        </div>
    </main>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarPenilai.vue';
import Swal from 'sweetalert2';


const penilaians = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;


const registrasiIndividu = ref([]);
const individuCurrentPage = ref(1);
const individuTotalPages = ref(1);

const registrasiKelompok = ref([]);
const kelompokCurrentPage = ref(1);
const kelompokTotalPages = ref(1);

const router = useRouter();

const fetchPenilaians = async () => {
    try {
        const response = await axios.get('/penilaian', {
            params: {
                per_page: perPage,
                page: currentPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            penilaians.value = response.data.data;
            currentPage.value = response.data.current_page;
            totalPages.value = response.data.last_page;
        } else {
            console.error('Failed to fetch penilaian karya:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching penilaian karya:', error.message);
    }
};


const fetchRegistrasiIndividu = async () => {
    try {
        const response = await axios.get('/registerIndividuPenilai', {
            params: {
                per_page: perPage,
                page: individuCurrentPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            registrasiIndividu.value = response.data.data;
            individuCurrentPage.value = response.data.current_page;
            individuTotalPages.value = response.data.last_page;
        } else {
            console.error('Failed to fetch registrasi individu:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching registrasi individu:', error.message);
    }
};


const fetchRegistrasiKelompok = async () => {
    try {
        const response = await axios.get('/registerKelompokPenilai', {
            params: {
                per_page: perPage,
                page: kelompokCurrentPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            registrasiKelompok.value = response.data.data;
            kelompokCurrentPage.value = response.data.current_page;
            kelompokTotalPages.value = response.data.last_page;
        } else {
            console.error('Failed to fetch registrasi kelompok:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching registrasi kelompok:', error.message);
    }
};


const deletePenilaian = async (id) => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus penilaian ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await axios.delete(`/penilaian-karya/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Penilaian berhasil dihapus', '', 'success');
            fetchPenilaians();
        } else {
            Swal.fire('Gagal menghapus penilaian', response.data.message, 'error');
            console.error('Gagal menghapus penilaian:', response.data.message);
        }
    } catch (error) {
        Swal.fire('Error menghapus penilaian', error.message, 'error');
        console.error('Error menghapus penilaian:', error.message);
    }
};


const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        fetchPenilaians();
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        fetchPenilaians();
    }
};

const prevIndividuPage = () => {
    if (individuCurrentPage.value > 1) {
        individuCurrentPage.value--;
        fetchRegistrasiIndividu();
    }
};

const nextIndividuPage = () => {
    if (individuCurrentPage.value < individuTotalPages.value) {
        individuCurrentPage.value++;
        fetchRegistrasiIndividu();
    }
};

const prevKelompokPage = () => {
    if (kelompokCurrentPage.value > 1) {
        kelompokCurrentPage.value--;
        fetchRegistrasiKelompok();
    }
};

const nextKelompokPage = () => {
    if (kelompokCurrentPage.value < kelompokTotalPages.value) {
        kelompokCurrentPage.value++;
        fetchRegistrasiKelompok();
    }
};


onMounted(() => {
    fetchPenilaians();
    fetchRegistrasiIndividu();
    fetchRegistrasiKelompok();
});
</script>

<style lang="scss" scoped>
    .data-penilaian-karya {
        background-color: #f5d99d;

        .penilaian-management-container {
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
            margin-bottom: 2rem; /* Space between tables */
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

        .penilaian-table, .registrasi-individu-table, .registrasi-kelompok-table {
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

            .edit-btn,
            .delete-btn {
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
