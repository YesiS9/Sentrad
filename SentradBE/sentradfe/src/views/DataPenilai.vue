<template>
    <Sidebar />
    <main class="data-penilai">
        <div class="user-management-container">
            <header class="header">
            <h2>Welcome, admin</h2>
            </header>
            <div class="table-wrapper">
            <div class="table-header">
                <h3>Penilai Management</h3>
                <router-link :to="{ name: 'FormPenilai' }" class="button">Tambah</router-link>
            </div>
            <table class="user-table">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>Nama Penilai</th>
                    <th>No. Telp</th>
                    <th>Bidang Ahli</th>
                    <th>Lembaga</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(penilai, index) in penilais" :key="penilai.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ penilai.nama_penilai }}</td>
                    <td>{{ penilai.noTelp_penilai }}</td>
                    <td>{{ penilai.bidang_ahli }}</td>
                    <td>{{ penilai.lembaga }}</td>
                    <td>
                        <span v-if="penilai.status_penilai === 1" class="status-active">Aktif</span>
                        <span v-else-if="penilai.status_penilai === 0" class="status-inactive">Nonaktif</span>
                    </td>
                    <td>
                    <router-link :to="{ name: 'FormPenilaiEdit', params: { id: penilai.id } }" class="edit-btn material-icons">
                        settings
                    </router-link>
                    <button @click="deletePenilai(penilai.id)" class="delete-btn">
                        <span class="material-icons">delete</span>
                    </button>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="pagination">
                <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
                <span>{{ currentPage }} / {{ totalPages }}</span>
                <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
            </div>
            </div>
        </div>
    </main>
  </template>

<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import axios from '../services/api.js';
    import Sidebar from '../components/SidebarAdmin.vue';

    const penilais = ref([]);
    const currentPage = ref(1);
    const totalPages = ref(1);

    const router = useRouter();

    const getPenilais = async () => {
        try {
        const response = await axios.get('/penilai');
        if (response.status === 200 && response.data.status === 'success') {
            penilais.value = response.data.data;
        } else {
            console.error('Failed to fetch penilais:', response.data.message);
        }
        } catch (error) {
            console.error('Error fetching penilais:', error.message);
        }
    };

    const deletePenilai = async (id) => {
        try {
        const response = await axios.delete(`/penilai/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            alert('Penilai deleted successfully');
            getPenilais(); // Refresh penilais list after deletion
        } else {
            console.error('Failed to delete penilai:', response.data.message);
        }
        } catch (error) {
        console.error('Error deleting penilai:', error.message);
        }
    };

    const prevPage = () => {
        if (currentPage.value > 1) {
        currentPage.value--;
        getPenilais(); // Fetch previous page data
        }
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
        currentPage.value++;
        getPenilais(); // Fetch next page data
        }
    };

    onMounted(() => {
        if (!localStorage.getItem('token')) {
        alert('Please login first.');
        return;
        }
        getPenilais();
    });
</script>

<style lang="scss" scoped>
    .data-penilai {
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
        }

        span {
            margin: 0 0.5rem;
        }
        }
    }
</style>
