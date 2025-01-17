<template>
    <div class="data-page">
        <Sidebar />
        <main class="data-seni">
        <div class="user-management-container">
            <header class="header">
            <h2>Welcome, admin</h2>
            </header>
            <div class="table-wrapper">
            <div class="table-header">
                <h3>Seni Management</h3>
                <router-link :to="{ name: 'FormSeni' }" class="button">Tambah</router-link>
            </div>
            <table class="user-table">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>Nama Seni</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(seni, index) in senis" :key="seni.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ seni.nama_seni }}</td>
                    <td>{{ seni.status_seni }}</td>
                    <td>
                    <router-link :to="{ name: 'FormSeniEdit', params: { id: seni.id } }" class="edit-btn material-icons">
                        settings
                    </router-link>
                    <button @click="deleteSeni(seni.id)" class="delete-btn">
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

  const senis = ref([]);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const perPage = 10;

  const router = useRouter();

  const getSeni = async () => {
    try {
      const response = await axios.get('/seni', {
        params: {
          per_page: perPage,
          page: currentPage.value,
        },
      });
      if (response.status === 200 && response.data.status === 'success') {
        senis.value = response.data.data;
        currentPage.value = response.data.current_page;
        totalPages.value = response.data.last_page;
      } else {
        console.error('Failed to fetch senis:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching senis:', error.message);
    }
  };

  const deleteSeni = async (id) => {
    const result = await Swal.fire({
      title: 'Apakah Anda yakin ingin menghapus seni ini?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya',
      cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
      return;
    }
    try {
      const response = await axios.delete(`/seni/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        Swal.fire('Seni berhasil dihapus', '', 'success');
        getSeni();
      } else {
        Swal.fire('Gagal menghapus seni', response.data.message, 'error');
        console.error('Failed to delete seni:', response.data.message);
      }
    } catch (error) {
      Swal.fire('Error menghapus seni', error.message, 'error');
      console.error('Error deleting seni:', error.message);
    }
  };

  const prevPage = () => {
    if (currentPage.value > 1) {
      currentPage.value--;
      getSeni();
    }
  };

  const nextPage = () => {
    if (currentPage.value < totalPages.value) {
      currentPage.value++;
      getSeni();
    }
  };

  onMounted(() => {
    if (!localStorage.getItem('token')) {
      alert('Please login first.');
      return;
    }
    getSeni();
  });
  </script>

  <style lang="scss" scoped>
  .data-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .data-seni {
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
