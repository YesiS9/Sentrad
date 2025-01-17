<template>
    <div class="data-page">
        <Sidebar />
        <main class="data-seniman">
            <div class="user-management-container">
                <header class="header">
                    <h2>Welcome, admin</h2>
                </header>
                <div class="table-wrapper">
                    <div class="table-header">
                        <h3>Seniman Management</h3>
                        <router-link :to="{ name: 'FormSeniman' }" class="button">Tambah</router-link>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>Nama Seniman</th>
                                <th>Tanggal Lahir</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(seniman, index) in senimans" :key="seniman.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ seniman.nama_seniman }}</td>
                                <td>{{ seniman.tgl_lahir }}</td>
                                <td>
                                    <span v-if="seniman.status_seniman === 1" class="status-active">Aktif</span>
                                    <span v-else-if="seniman.status_seniman === 0" class="status-inactive">Nonaktif</span>
                                </td>
                                <td>
                                    <router-link :to="{ name: 'FormSenimanEdit', params: { id: seniman.id } }" class="edit-btn material-icons">
                                        settings
                                    </router-link>
                                    <button @click="deleteSeniman(seniman.id)" class="delete-btn">
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarAdmin.vue';
import Swal from 'sweetalert2';

const senimans = ref([]);
const currentPage = ref(1);
const totalPages = ref(1);
const perPage = 10;

const router = useRouter();

const getSenimans = async () => {
    try {
        const response = await axios.get('/seniman', {
            params: {
                per_page: perPage,
                page: currentPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            senimans.value = response.data.data;
            currentPage.value = response.data.current_page;
            totalPages.value = response.data.last_page;
        } else {
            console.error('Failed to fetch senimans:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching senimans:', error.message);
    }
};

const deleteSeniman = async (id) => {
    const result = await Swal.fire({
        title: 'Apakah Anda yakin ingin menghapus seniman ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }
    try {
        const response = await axios.delete(`/seniman/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            Swal.fire('Seniman berhasil dihapus', '', 'success');
            getSenimans();
        } else {
            Swal.fire('Gagal menghapus seniman', response.data.message, 'error');
            console.error('Failed to delete seniman:', response.data.message);
        }
    } catch (error) {
        Swal.fire('Error menghapus seniman', error.message, 'error');
        console.error('Error deleting seniman:', error.message);
    }
};


const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
        getSenimans(); // Fetch previous page data
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        getSenimans(); // Fetch next page data
    }
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        alert('Please login first.');
        return;
    }
    getSenimans();
});
</script>

<style lang="scss" scoped>
.data-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.data-seniman {
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
