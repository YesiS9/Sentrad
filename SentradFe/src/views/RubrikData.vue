<template>
    <div class="rubrik-page">
        <Sidebar />
        <main class="data-rubrik">
            <div class="rubrik-management-container">
                <header class="header">
                    <h2>Rubrik Management</h2>
                </header>
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Daftar Rubrik Saya</h3>
                        <router-link :to="{ name: 'FormRubrik' }" class="button">Tambah</router-link>
                    </div>
                    <table class="rubrik-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Rubrik</th>
                                <th>Bobot</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(rubrik, index) in rubriks" :key="rubrik.id">
                                <td>{{ (currentPageRubrikSaya - 1) * perPageRubrikSaya + index + 1 }}</td>
                                <td>{{ rubrik.nama_rubrik }}</td>
                                <td>{{ rubrik.bobot }}</td>
                                <td>
                                    <router-link :to="{ name: 'FormRubrikEdit', params: { id: rubrik.id } }" class="edit-btn material-icons">
                                        settings
                                    </router-link>
                                    <button @click="deleteRubrik(rubrik.id)" class="delete-btn">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="rubriks.length === 0">
                                <td colspan="5" class="no-data">Data rubrik kosong</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination" v-if="rubriks.length > 0">
                        <button @click="prevPageRubrikSaya" :disabled="currentPageRubrikSaya === 1">Previous</button>
                        <span>{{ currentPageRubrikSaya }} / {{ totalPagesRubrikSaya }}</span>
                        <button @click="nextPageRubrikSaya" :disabled="currentPageRubrikSaya === totalPagesRubrikSaya">Next</button>
                    </div>
                </div>
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Daftar Rubrik Lainnya</h3>
                    </div>
                    <table class="rubrik-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Rubrik</th>
                                <th>Bobot</th>
                                <th>Penilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(rubrikPenilai, index) in rubrikPenilai" :key="rubrikPenilai.id">
                                <td>{{ (currentPageRubrikLainnya - 1) * perPageRubrikLainnya + index + 1 }}</td>
                                <td>{{ rubrikPenilai.nama_rubrik }}</td>
                                <td>{{ rubrikPenilai.bobot }}</td>
                                <td>{{ rubrikPenilai.nama_penilai || 'N/A' }}</td>
                            </tr>
                            <tr v-if="rubrikPenilai.length === 0">
                                <td colspan="4" class="no-data">Data rubrik penilai kosong</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination" v-if="rubrikPenilai.length > 0">
                        <button @click="prevPageRubrikLainnya" :disabled="currentPageRubrikLainnya === 1">Previous</button>
                        <span>{{ currentPageRubrikLainnya }} / {{ totalPagesRubrikLainnya }}</span>
                        <button @click="nextPageRubrikLainnya" :disabled="currentPageRubrikLainnya === totalPagesRubrikLainnya">Next</button>
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

const rubriks = ref([]);
const rubrikPenilai = ref([]);

const currentPageRubrikSaya = ref(1);
const totalPagesRubrikSaya = ref(1);
const perPageRubrikSaya = 10;

const currentPageRubrikLainnya = ref(1);
const totalPagesRubrikLainnya = ref(1);
const perPageRubrikLainnya = 10;

const router = useRouter();

const fetchRubriks = async () => {
    try {
        const response = await axios.get('/rubrikPenilai', {
            params: {
                per_page: perPageRubrikSaya,
                page: currentPageRubrikSaya.value,
            },
        });
        if (response.status === 200 && response.data.status === 'success') {
            rubriks.value = response.data.data;
            currentPageRubrikSaya.value = response.data.current_page;
            totalPagesRubrikSaya.value = response.data.last_page;
        } else {
            console.error('Failed to fetch rubriks:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching rubriks:', error.message);
    }
};

const fetchRubrikPenilai = async () => {
    try {
        const response = await axios.get('/rubrik', {
            params: {
                per_page: perPageRubrikLainnya,
                page: currentPageRubrikLainnya.value,
            },
        });
        if (response.status === 200 && response.data.status === 'success') {
            rubrikPenilai.value = response.data.data;
            currentPageRubrikLainnya.value = response.data.current_page;
            totalPagesRubrikLainnya.value = response.data.last_page;
        } else {
            console.error('Failed to fetch rubrikPenilai:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching rubrikPenilai:', error.message);
    }
};

const deleteRubrik = async (id) => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus rubrik ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await axios.delete(`/rubrik/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Rubrik berhasil dihapus', '', 'success');
            fetchRubriks();
        } else {
            Swal.fire('Gagal menghapus rubrik', response.data.message, 'error');
            console.error('Gagal menghapus rubrik:', response.data.message);
        }
    } catch (error) {
        Swal.fire('Error menghapus rubrik', error.message, 'error');
        console.error('Error menghapus rubrik:', error.message);
    }
};

const prevPageRubrikSaya = () => {
    if (currentPageRubrikSaya.value > 1) {
        currentPageRubrikSaya.value--;
        fetchRubriks();
    }
};

const nextPageRubrikSaya = () => {
    if (currentPageRubrikSaya.value < totalPagesRubrikSaya.value) {
        currentPageRubrikSaya.value++;
        fetchRubriks();
    }
};

const prevPageRubrikLainnya = () => {
    if (currentPageRubrikLainnya.value > 1) {
        currentPageRubrikLainnya.value--;
        fetchRubrikPenilai();
    }
};

const nextPageRubrikLainnya = () => {
    if (currentPageRubrikLainnya.value < totalPagesRubrikLainnya.value) {
        currentPageRubrikLainnya.value++;
        fetchRubrikPenilai();
    }
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        alert('Please login first.');
        router.push('/login');
        return;
    }
    fetchRubriks();
    fetchRubrikPenilai();
});
</script>
<style lang="scss" scoped>
.rubrik-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
    .data-rubrik {
        background-color: #f5d99d;

        .rubrik-management-container {
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

        .rubrik-table {
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
