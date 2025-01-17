<template>
    <div class="portofolio-page">
        <Sidebar />
        <main class="data-portofolio">
            <div class="user-management-container">
                <header class="header">
                    <h2>Welcome, {{ userName }}</h2>
                </header>
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Portofolio Individu</h3>
                        <router-link :to="{ name: 'FormPortofolio' }" class="button">Tambah Portofolio Individu</router-link>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Judul Portofolio</th>
                                <th>Jumlah Karya</th>
                                <th>Tambah Karya</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(portofolio, index) in portofoliosIndividu" :key="portofolio.id">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <router-link :to="{ name: 'InfoPortofolioSeniman', params: { id: portofolio.id } }" class="portfolio-link">
                                        {{ portofolio.judul_portofolio }}
                                    </router-link>
                                </td>
                                <td>{{ portofolio.jumlah_karya }}</td>
                                <td>
                                    <router-link :to="{ name: 'FormKarya', params: { portofolioId: portofolio.id } }" class="add-karya-btn">
                                        <span class="material-icons">add_circle</span>
                                    </router-link>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'FormPortofolioEdit', params: { id: portofolio.id } }" class="edit-btn material-icons">
                                        settings
                                    </router-link>
                                    <button @click="deletePortofolio(portofolio.id, 'individu')" class="delete-btn">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="portofoliosIndividu.length === 0">
                                <td colspan="5" class="no-data">Portofolio Individu kosong</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Table for Group Portfolios -->
                    <div class="table-header">
                        <h3>Portofolio Kelompok</h3>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Judul Portofolio</th>
                                <th>Jumlah Karya</th>
                                <th>Tambah Karya</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(portofolio, index) in portofoliosKelompok" :key="portofolio.id">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <router-link :to="{ name: 'InfoPortofolioSeniman', params: { id: portofolio.id } }" class="portfolio-link">
                                        {{ portofolio.judul_portofolio }}
                                    </router-link>
                                </td>
                                <td>{{ portofolio.jumlah_karya }}</td>
                                <td>
                                    <router-link :to="{ name: 'FormKarya', params: { portofolioId: portofolio.id } }" class="add-karya-btn">
                                        <span class="material-icons">add_circle</span>
                                    </router-link>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'FormPortofolioEdit', params: { id: portofolio.id } }" class="edit-btn material-icons">
                                        settings
                                    </router-link>
                                    <button @click="deletePortofolio(portofolio.id, 'kelompok')" class="delete-btn">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="portofoliosKelompok.length === 0">
                                <td colspan="5" class="no-data">Portofolio Kelompok kosong</td>
                            </tr>
                        </tbody>
                    </table>
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

const userName = ref(localStorage.getItem('username') || '');
const portofoliosIndividu = ref([]);
const portofoliosKelompok = ref([]);

const loadPortofolios = async () => {
    try {
        const response = await axios.get('/portofolio');
        if (response.status === 200 && response.data.status === 'success') {
            const { individual_portfolios, group_portfolios } = response.data.data;
            portofoliosIndividu.value = individual_portfolios.items;
            portofoliosKelompok.value = group_portfolios.items;
        } else {
            console.error('Failed to load portfolios:', response.data.message);
        }
    } catch (error) {
        console.error('Error:', error.message);
    }
};

const deletePortofolio = async (id, type) => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus portofolio ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) return;

    try {
        const response = await axios.delete(`/portofolio/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Portofolio berhasil dihapus', '', 'success');
            if (type === 'individu') {
                portofoliosIndividu.value = portofoliosIndividu.value.filter(port => port.id !== id);
            } else {
                portofoliosKelompok.value = portofoliosKelompok.value.filter(port => port.id !== id);
            }
        } else {
            Swal.fire('Gagal menghapus portofolio', response.data.message, 'error');
        }
    } catch (error) {
        Swal.fire('Error menghapus portofolio', error.message, 'error');
    }
};

onMounted(() => {
    loadPortofolios();
});
</script>


<style lang="scss" scoped>
.portofolio-page {
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
.data-portofolio {
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

        .edit-btn, .delete-btn, .add-karya-btn, .info-btn {
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
        .info-btn{
            background-color: #437ce6;

            &:hover {
            background-color: #437ce6;
            }

            .material-icons {
            color: #fff;
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

        .add-karya-btn {
            background-color: #45a049;

            &:hover {
            background-color: #45a049;
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
