<template>
    <Sidebar />
    <main class="data-tingkatan">
        <div class="user-management-container">
            <header class="header">
                <h2>Welcome, admin</h2>
            </header>
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>Tingkatan Management</h3>
                    <router-link :to="{ name: 'FormTingkatan' }" class="button">Tambah</router-link>
                </div>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>Nama Tingkatan</th>
                            <th>Nilai Max</th>
                            <th>Nilai Min</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(tingkatan, index) in tingkatan_list" :key="tingkatan.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ tingkatan.nama_tingkatan }}</td>
                            <td>{{ tingkatan.nilai_max }}</td>
                            <td>{{ tingkatan.nilai_min }}</td>
                            <td>
                                <router-link :to="{ name: 'FormTingkatanEdit', params: { id: tingkatan.id } }" class="edit-btn material-icons">
                                    settings
                                </router-link>
                                <button @click="deleteTingkatan(tingkatan.id)" class="delete-btn">
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

    const tingkatan_list = ref([]);
    const currentPage = ref(1);
    const totalPages = ref(1);

    const router = useRouter();

    const getTingkatan = async () => {
        try {
            const response = await axios.get('/tingkatan');
            if (response.status === 200 && response.data.status === 'success') {
                tingkatan_list.value = response.data.data;
            } else {
                console.error('Failed to fetch tingkatan:', response.data.message);
            }
        } catch (error) {
            console.error('Error fetching tingkatan:', error.message);
        }
    };

    const deleteTingkatan = async (id) => {
        try {
            const response = await axios.delete(`/tingkatan/${id}`);
            if (response.status === 200 && response.data.status === 'success') {
                alert('Tingkatan deleted successfully');
                getTingkatan();
            } else {
                console.error('Failed to delete Tingkatan:', response.data.message);
            }
        } catch (error) {
            console.error('Error deleting Tingkatan:', error.message);
        }
    };

    const prevPage = () => {
        if (currentPage.value > 1) {
            currentPage.value--;
            getTingkatan(); // Fetch previous page data
        }
    };

    const nextPage = () => {
        if (currentPage.value < totalPages.value) {
            currentPage.value++;
            getTingkatan(); // Fetch next page data
        }
    };

    onMounted(() => {
        if (!localStorage.getItem('token')) {
            alert('Please login first.');
            return;
        }
        getTingkatan();
    });
</script>

<style lang="scss" scoped>
    .data-tingkatan {
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
            }

            span {
                margin: 0 0.5rem;
            }
        }
    }
</style>
