<template>
    <Sidebar />
    <main class="data-portofolio">
      <div class="user-management-container">
        <header class="header">
          <h2>Welcome, {{ userName }}</h2>
        </header>
        <div class="table-wrapper">
          <div class="table-header">
            <h3>Portofolio</h3>
          </div>
          <table class="user-table">
            <thead>
              <tr>
                <th>NO</th>
                <th>Judul Portofolio</th>
                <th>Jumlah Karya</th>
                <th>Tambah Karya</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(portofolio, index) in portofolios" :key="portofolio.id">
                <td>{{ index + 1 }}</td>
                <td>
                  <router-link :to="{ name: 'InfoPortofolio', params: { id: portofolio.id } }" class="portfolio-link">
                    {{ portofolio.judul_portofolio }}
                  </router-link>
                </td>
                <td>{{ portofolio.jumlah_karya }}</td>
                <td>
                  <router-link :to="{ name: 'FormKarya', params: { portofolioId: portofolio.id } }" class="add-karya-btn">
                    <span class="material-icons">add_circle</span>
                  </router-link>
                </td>
              </tr>
              <tr v-if="portofolios.length === 0">
                <td colspan="4" class="no-data">Portofolio kosong</td>
              </tr>
            </tbody>
          </table>
          <div class="pagination" v-if="totalPages > 1">
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
  import axios from '../services/api.js';
  import Sidebar from '../components/SidebarSeniman.vue';

  const userName = ref(localStorage.getItem('username') || '');
  const portofolios = ref([]);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const perPage = 10;
  const registrasiIndividu = ref('');
  const registrasiKelompok = ref('');

  const loadPortofolios = async () => {
      try {
          const response = await axios.get('/api/portofolios/filter', {
              params: {
                  per_page: perPage,
                  page: currentPage.value,
                  registrasi_individu: registrasiIndividu.value,
                  registrasi_kelompok: registrasiKelompok.value
              }
          });
          if (response.status === 200 && response.data.status === 'success') {
              portofolios.value = response.data.data.data; // Assuming data contains data, current_page, last_page
              currentPage.value = response.data.data.current_page;
              totalPages.value = response.data.data.last_page;
          } else {
              console.error('Failed to load portofolio data:', response.data.message);
          }
      } catch (error) {
          console.error('Error:', error.message);
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

  onMounted(() => {
      if (!localStorage.getItem('token')) {
          alert('Please login first.');
          router.push('/login');
          return;
      }
      loadPortofolios();
  });
  </script>

  <style lang="scss" scoped>
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

          .add-karya-btn {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              background-color: #f7941e;
              color: #fff;
              border: none;
              padding: 0.3rem;
              border-radius: 4px;
              cursor: pointer;
              height: 2rem;
              width: 2rem;

              .material-icons {
                  font-size: 1.5rem;
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
