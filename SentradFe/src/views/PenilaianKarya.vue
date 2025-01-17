<template>
    <div class="penilaian-page">
        <Sidebar />
        <main class="data-penilaian-karya">
            <div class="penilaian-management-container">
                <header class="header">
                    <h2>Penilaian Registrasi Management</h2>
                </header>

                <div class="table-wrapper">
                    <h3>Daftar Penilaian Registrasi</h3>
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
                                <td>{{ penilaian.registrasi_individu?.nama || penilaian.registrasi_kelompok?.nama_kelompok || 'Tidak Diketahui' }}</td>
                                <td>{{ penilaian.total_nilai }}</td>
                                <td>
                                    <router-link :to="{ name: 'DetailPenilaian', params: { id: penilaian.id } }" class="edit-btn material-icons">
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
                    <div class="pagination">
                        <button @click="prevPage" :disabled="currentPenilaianPage === 1">Previous</button>
                        <span>{{ currentPenilaianPage }} / {{ totalPenilaianPages }}</span>
                        <button @click="nextPage" :disabled="currentPenilaianPage === totalPenilaianPages">Next</button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <h3>Daftar Registrasi Individu</h3>
                    <table class="registrasi-individu-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Seniman</th>
                                <th>Nama Karya</th>
                                <th>Status Penilaian</th>
                                <th>Info Karya</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(individu, index) in registrasiIndividu" :key="individu.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ individu.nama }}</td>
                                <td>{{ individu.email }}</td>
                                <td>{{ individu.status_individu }}</td>
                                <td>
                                    <router-link :to="{ name: 'PortofolioIndividu', params: { individuId: individu.id } }" class="button">Lihat Karya</router-link>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'FormPenilaianIndividu', params: { individuId: individu.id } }" class="button">Berikan Penilaian</router-link>
                                </td>
                            </tr>
                            <tr v-if="registrasiIndividu && registrasiIndividu.length === 0">
                                <td colspan="6" class="no-data">Data Registrasi Individu Kosong</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination">
                        <button @click="prevIndividuPage" :disabled="currentIndividuPage === 1">Previous</button>
                        <span>{{ currentIndividuPage }} / {{ totalIndividuPages }}</span>
                        <button @click="nextIndividuPage" :disabled="currentIndividuPage === totalIndividuPages">Next</button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <h3>Daftar Registrasi Kelompok</h3>
                    <table class="registrasi-kelompok-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Seniman</th>
                                <th>Nama Kelompok</th>
                                <th>Status Penilaian</th>
                                <th>Info Karya</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(kelompok, index) in registrasiKelompok" :key="kelompok.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ kelompok.seniman?.nama_seniman || 'Tidak Diketahui' }}</td>
                                <td>{{ kelompok.nama_kelompok }}</td>
                                <td>{{ kelompok.status_kelompok }}</td>
                                <td>
                                    <router-link :to="{ name: 'PortofolioKelompok', params: { kelompokId: kelompok.id } }" class="button">Lihat Karya</router-link>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'FormPenilaianKelompok', params: { kelompokId: kelompok.id } }" class="button">Berikan Penilaian</router-link>
                                </td>
                            </tr>
                            <tr v-if="registrasiKelompok && registrasiKelompok.length === 0">
                                <td colspan="6" class="no-data">Data Registrasi Kelompok Kosong</td>
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
import { useRouter } from 'vue-router';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarPenilai.vue';
import Swal from 'sweetalert2';

const penilaians = ref([]);
const currentPenilaianPage = ref(1);
const totalPenilaianPages = ref(1);
const perPage = 10;

const registrasiIndividu = ref([]);
const currentIndividuPage = ref(1);
const totalIndividuPages = ref(1);

const registrasiKelompok = ref([]);
const currentKelompokPage = ref(1);
const totalKelompokPages = ref(1);

const router = useRouter();

const formatDate = (dateString) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', options);
};

const fetchPenilaians = async () => {
    try {
        const penilaiId = localStorage.getItem('penilai_id');
        const response = await axios.get(`/penilaianKarya?penilai_id=${penilaiId}`, {
            params: { per_page: perPage, page: currentPenilaianPage.value }
        });

        if (response.status === 200 && response.data.status === 'success') {
            penilaians.value = response.data.data || [];
            currentPenilaianPage.value = response.data.current_page;
            totalPenilaianPages.value = response.data.last_page;
        }
    } catch (error) {
        console.error('Error fetching penilaian karya:', error);
    }
};

const fetchRegistrasiIndividu = async () => {
    try {
        const penilaiId = localStorage.getItem('penilai_id');
        if (!penilaiId) return;

        const response = await axios.get(`/registerIndividuPenilai/${penilaiId}`, {
            params: { per_page: perPage, page: currentIndividuPage.value }
        });

        if (response.status === 200 && response.data.status === 'success') {
            registrasiIndividu.value = response.data.data || [];
            currentIndividuPage.value = response.data.current_page;
            totalIndividuPages.value = response.data.last_page;
        }
    } catch (error) {
        console.error('Error fetching registrasi individu:', error);
    }
};

const fetchRegistrasiKelompok = async () => {
    try {
        const penilaiId = localStorage.getItem('penilai_id');
        if (!penilaiId) return;

        const response = await axios.get(`/registerKelompokPenilai/${penilaiId}`, {
            params: { per_page: perPage, page: currentKelompokPage.value }
        });

        if (response.status === 200 && response.data.status === 'success') {
            registrasiKelompok.value = response.data.data || [];
            currentKelompokPage.value = response.data.current_page;
            totalKelompokPages.value = response.data.last_page;
        }
    } catch (error) {
        console.error('Error fetching registrasi kelompok:', error);
    }
};

const deletePenilaian = async (penilaianId) => {
    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This action will permanently delete the penilaian.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it'
        });

        if (result.isConfirmed) {
            const response = await axios.delete(`/penilaianKarya/${penilaianId}`);
            if (response.status === 200) {
                Swal.fire('Deleted!', 'The penilaian has been deleted.', 'success');
                fetchPenilaians();
            }
        }
    } catch (error) {
        console.error('Error deleting penilaian:', error);
        Swal.fire('Error!', 'Failed to delete penilaian.', 'error');
    }
};

const nextPage = () => {
    if (currentPenilaianPage.value < totalPenilaianPages.value) {
        currentPenilaianPage.value++;
        fetchPenilaians();
    }
};
const prevPage = () => {
    if (currentPenilaianPage.value > 1) {
        currentPenilaianPage.value--;
        fetchPenilaians();
    }
};

const nextIndividuPage = () => {
    if (currentIndividuPage.value < totalIndividuPages.value) {
        currentIndividuPage.value++;
        fetchRegistrasiIndividu();
    }
};
const prevIndividuPage = () => {
    if (currentIndividuPage.value > 1) {
        currentIndividuPage.value--;
        fetchRegistrasiIndividu();
    }
};

const nextKelompokPage = () => {
    if (currentKelompokPage.value < totalKelompokPages.value) {
        currentKelompokPage.value++;
        fetchRegistrasiKelompok();
    }
};
const prevKelompokPage = () => {
    if (currentKelompokPage.value > 1) {
        currentKelompokPage.value--;
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
.penilaian-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
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
                background-color: #f7941e;

                &:hover {
                    background-color: #f7941e;
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
