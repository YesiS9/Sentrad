<template>
    <div class="data-page">
        <Sidebar />
        <main class="data-registrasi">
            <div class="user-management-container">
                <header class="header">
                <h2>Welcome, admin</h2>
                </header>
                <div class="table-wrapper">
                <div class="table-header">
                    <h3>Registrasi Individu</h3>
                    <router-link :to="{ name: 'FormIndividu' }" class="button">Tambah</router-link>
                </div>
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Lahir</th>
                        <th>Tanggal Mulai</th>
                        <th>Alamat</th>
                        <th>No. Telp</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(individu, index) in registrasi_individus" :key="individu.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ individu.nama }}</td>
                        <td><a :href="'mailto:' + individu.email">{{ individu.email }}</a></td>
                        <td>{{ individu.tgl_lahir }}</td>
                        <td>{{ individu.tgl_mulai }}</td>
                        <td>{{ individu.alamat }}</td>
                        <td>{{ individu.noTelp }}</td>
                        <td>{{ individu.status_individu }}</td>
                        <td>
                            <router-link :to="{ name: 'FormIndividuEdit', params: { id: individu.id } }" class="edit-btn material-icons">
                                settings
                            </router-link>
                            <button @click="deleteIndividu(individu.id)" class="delete-btn">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="pagination">
                    <button @click="prevIndividuPage" :disabled="currentIndividuPage === 1">Previous</button>
                    <span>{{ currentIndividuPage }} / {{ totalIndividuPages }}</span>
                    <button @click="nextIndividuPage" :disabled="currentIndividuPage === totalIndividuPages">Next</button>
                </div>
                </div>
            </div>

            <!-- Container for Kelompok Registrations -->
            <div class="user-management-container">
                <div class="table-wrapper">
                <div class="table-header">
                    <h3>Registrasi Kelompok</h3>
                    <router-link :to="{ name: 'FormKelompok' }" class="button">Tambah</router-link>
                </div>
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>NO</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Terbentuk</th>
                        <th>No.Telp</th>
                        <th>Alamat</th>
                        <th>Jumlah Anggota</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(kelompok, index) in registrasi_kelompoks" :key="kelompok.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ kelompok.nama_kelompok }}</td>
                        <td><a :href="'mailto:' + kelompok.email_kelompok">{{ kelompok.email_kelompok }}</a></td>
                        <td>{{ kelompok.tgl_terbentuk }}</td>
                        <td>{{ kelompok.noTelp_kelompok }}</td>
                        <td>{{ kelompok.alamat_kelompok }}</td>
                        <td>{{ kelompok.jumlah_anggota }}</td>
                        <td>{{ kelompok.status_kelompok }}</td>
                        <td>
                            <router-link :to="{ name: 'FormKelompokEdit', params: { id: kelompok.id } }" class="edit-btn material-icons">
                                settings
                            </router-link>
                            <button @click="deleteKelompok(kelompok.id)" class="delete-btn">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
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
    import axios from '../services/api.js';
    import Sidebar from '../components/SidebarAdmin.vue';
    import Swal from 'sweetalert2';

    const registrasi_individus = ref([]);
    const registrasi_kelompoks = ref([]);
    const currentIndividuPage = ref(1);
    const totalIndividuPages = ref(1);
    const currentKelompokPage = ref(1);
    const totalKelompokPages = ref(1);
    const perPage = 10;

    const loadIndividus = async () => {
        try {
        const response = await axios.get('/registerIndividu',{
            params: {
                per_page: perPage,
                page: currentIndividuPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            registrasi_individus.value = response.data.data;
            currentIndividuPage.value = response.data.current_page;
            totalIndividuPages.value = response.data.last_page;
        } else {
            console.error('Failed to load individus data.');
        }
        } catch (error) {
        console.error('Error:', error.message);
        }
    };

    const loadKelompoks = async () => {
        try {
        const response = await axios.get('/registerKelompok',{
            params: {
                per_page: perPage,
                page: currentKelompokPage.value
            }
        });
        if (response.status === 200 && response.data.status === 'success') {
            registrasi_kelompoks.value = response.data.data;
            currentKelompokPage.value = response.data.current_page;
            totalKelompokPages.value = response.data.last_page;
        } else {
            console.error('Failed to load kelompoks data.');
        }
        } catch (error) {
        console.error('Error:', error.message);
        }
    };

    const deleteIndividu = async (id) => {
        const result = await Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus registrasi individu ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        });

        if (!result.isConfirmed) {
            return;
        }
        try {
            const response = await axios.delete(`/registerIndividu/${id}`);
            if (response.status === 200 && response.data.status === 'success') {
                Swal.fire('Regitrasi individu berhasil dihapus', '', 'success');
                loadKelompoks();
            } else {
                Swal.fire('Gagal menghapus regitrasi individu', response.data.message, 'error');
                console.error('Gagal menghapus regitrasi individu:', response.data.message);
            }
        } catch (error) {
            Swal.fire('Error menghapus regitrasi individu', error.message, 'error');
            console.error('Error menghapus regitrasi individu:', error.message);
        }
    };


    const deleteKelompok = async (id) => {
        const result = await Swal.fire({
            title: 'Apakah Anda yakin ingin menghapus regitrasi kelompok ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        });

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await axios.delete(`/registerKelompok/${id}`);
            if (response.status === 200 && response.data.status === 'success') {
                Swal.fire('Regitrasi kelompok berhasil dihapus', '', 'success');
                loadKelompoks();
            } else {
                Swal.fire('Gagal menghapus regitrasi kelompok', response.data.message, 'error');
                console.error('Gagal menghapus regitrasi kelompok:', response.data.message);
            }
        } catch (error) {
            Swal.fire('Error menghapus regitrasi kelompok', error.message, 'error');
            console.error('Error menghapus regitrasi kelompok:', error.message);
        }
    };


    const prevIndividuPage = () => {
        if (currentIndividuPage.value > 1) {
        currentIndividuPage.value--;

        }
    };

    const nextIndividuPage = () => {
        if (currentIndividuPage.value < totalIndividuPages.value) {
        currentIndividuPage.value++;
        }
    };

    const prevKelompokPage = () => {
        if (currentKelompokPage.value > 1) {
        currentKelompokPage.value--;
        }
    };

    const nextKelompokPage = () => {
        if (currentKelompokPage.value < totalKelompokPages.value) {
        currentKelompokPage.value++;
        }
    };


    onMounted(() => {
        loadIndividus();
        loadKelompoks();
    });
</script>

<style lang="scss" scoped>
.data-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
    .data-registrasi {
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
