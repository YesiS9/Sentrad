<template>
    <div class="penilai-page">
        <Sidebar />
        <main class="dashboard-page">
            <section class="penilai-dashboard">
                <h1>Profil Penilai</h1>
                <div class="profile-section">
                    <div class="profile-card">
                        <div class="profile-details" v-if="penilai">
                            <img :src="penilai.avatar" alt="Avatar" class="avatar" />
                            <div class="info">
                                <h2>{{ penilai.user ? penilai.user.username : 'Loading...' }}</h2>
                                <p><strong>Nama Lengkap:</strong> {{ penilai.nama_penilai }}</p>
                                <p v-if="penilai.user"><strong>Email:</strong> {{ penilai.user.email }}</p>
                                <p><strong>Telepon:</strong> {{ penilai.noTelp_penilai }}</p>
                                <p><strong>Bidang Ahli:</strong> {{ penilai.bidang_ahli }}</p>
                                <p><strong>Lembaga:</strong> {{ penilai.lembaga }}</p>
                                <p><strong>Status:</strong> {{ penilai.status_penilai ? 'Aktif' : 'Tidak Aktif' }}</p>
                            </div>
                        </div>
                        <p v-else>Loading...</p>
                    </div>

                    <div class="history-card">
                        <h3>History</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(penilaian, index) in historyData" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ penilaian.nama }}</td>
                                    <td>{{ penilaian.total_nilai }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="historyData.length === 0">Tidak ada data penilaian tersedia.</p>
                    </div>
                </div>

                <div class="table-section">
                    <div class="table-card">
                        <h3>Daftar Seniman Individu yang Harus Dinilai</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Status Penilaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(seniman, index) in daftarSenimanIndividu" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ seniman.nama }}</td>
                                    <td>{{ seniman.status_individu }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="daftarSenimanIndividu.length === 0">Tidak ada seniman individu yang harus dinilai.</p>
                    </div>

                    <div class="table-card">
                        <h3>Daftar Seniman Kelompok yang Harus Dinilai</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelompok</th>
                                    <th>Status Penilaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(kelompok, index) in daftarSenimanKelompok" :key="index">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ kelompok.nama_kelompok }}</td>
                                    <td>{{ kelompok.status_kelompok }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="daftarSenimanKelompok.length === 0">Tidak ada seniman kelompok yang harus dinilai.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>


<script>
import Sidebar from '../components/SidebarPenilai.vue';
import axios from '../services/api.js';

export default {
    components: {
        Sidebar,
    },
    data() {
        return {
            penilai: null,
            daftarSenimanIndividu: [],
            daftarSenimanKelompok: [],
            historyData: [],
        };
    },
    mounted() {
        const penilaiId = localStorage.getItem('penilai_id');

        if (penilaiId) {
            this.fetchPenilaiData(penilaiId);
            this.fetchPenilaians(penilaiId);
            this.fetchLatestIndividu(penilaiId);
            this.fetchLatestKelompok(penilaiId);
        } else {
            console.error('Penilai ID not found in localStorage');
        }
    },
    methods: {
        async fetchPenilaiData(penilaiId) {
            try {
                const response = await axios.get(`/penilai/${penilaiId}`);
                this.penilai = response.data.data;

                if (this.penilai && this.penilai.user_id) {
                    const userResponse = await axios.get(`/user/${this.penilai.user_id}`);
                    this.penilai.user = userResponse.data.data;
                }

                if (this.penilai.user && this.penilai.user.foto) {
                    this.penilai.avatar = `http://localhost:8000/storage/${this.penilai.user.foto}`;
                } else {
                    this.penilai.avatar = 'default-avatar.jpg';
                }
            } catch (error) {
                console.error('Error fetching Penilai or User data:', error);
                alert('Failed to fetch data. Please try again later.');
            }
        },
        async fetchPenilaians(penilaiId) {
            try {
                const response = await axios.get(`/penilaianKarya`, {
                    params: { penilai_id: penilaiId }
                });

                if (response.status === 200 && response.data.status === 'success') {
                    const penilaianData = response.data.data || [];

                    this.historyData = penilaianData.map((penilaian) => {
                        const nama =
                            penilaian.registrasi_individu?.nama ||
                            penilaian.registrasi_kelompok?.nama_kelompok ||
                            "Tidak Diketahui";

                        return {
                            ...penilaian,
                            nama,
                            total_nilai: penilaian.total_nilai || 0,
                        };
                    });
                } else {
                    console.error('Failed to fetch penilaian karya:', response.data.message);
                }
            } catch (error) {
                console.error('Error fetching penilaian karya:', error);
            }
        },

        async fetchLatestIndividu(penilaiId) {
            try {
                const response = await axios.get(`/registerIndividuPenilai/${penilaiId}`, {
                    params: {
                        limit: 5
                    }
                });

                if (response.status === 200 && response.data.status === 'success') {
                    this.daftarSenimanIndividu = response.data.data || [];
                } else {
                    console.error('Failed to fetch latest registrasi individu:', response.data.message);
                }
            } catch (error) {
                console.error('Error fetching latest registrasi individu:', error);
            }
        },
        async fetchLatestKelompok(penilaiId) {
            try {
                const response = await axios.get(`/registerKelompokPenilai/${penilaiId}`, {
                    params: {
                        limit: 5
                    }
                });

                if (response.status === 200 && response.data.status === 'success') {
                    this.daftarSenimanKelompok = response.data.data || [];
                } else {
                    console.error('Failed to fetch latest registrasi kelompok:', response.data.message);
                }
            } catch (error) {
                console.error('Error fetching latest registrasi kelompok:', error);
            }
        },
    },
};
</script>

<style scoped lang="scss">
.penilai-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.dashboard-page {
    display: flex;
    flex-direction: column;
    background-color: #f5d99d;
    padding: 2rem;
}

.penilai-dashboard {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 100%;
}

.profile-section {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 2rem;
}

.profile-card {
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    width: 48%;
    transition: box-shadow 0.3s;
    &:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
}

.history-card {
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #ffffff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    width: 48%;
    max-height: 450px;
    overflow-y: auto;
}

.profile-details {
    display: flex;
    align-items: center;
}

.avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin-right: 1rem;
}

.info {
    h2 {
        margin: 0;
        font-size: 1.5rem;
    }
    p {
        margin: 0.5rem 0;
    }
}

.table-section {
    display: flex;
    gap: 2rem;
    width: 100%;
    justify-content: flex-start;
}

.table-card {
    width: 100%;
    max-width: 300px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1rem;
}

.table-card h3 {
    font-size: 1.1rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 1rem;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 0.5rem;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

th {
    background-color: #ccc;
}

tr:hover {
    background-color: #f9f9f9;
}
</style>
