<template>
    <Sidebar />
    <main class="dashboard-page">
        <section class="artist-profile">
            <h1>Profil Seniman</h1>
            <div class="profile-card">
                <div class="profile-details" v-if="seniman">
                    <img :src="seniman.avatar" alt="Avatar" class="avatar" />
                    <div class="info">
                        <h2>{{ seniman.user.username }}</h2>
                        <p><strong>Nama Lengkap:</strong> {{ seniman.nama_seniman }}</p>
                        <p><strong>Email:</strong> {{ seniman.user.email }}</p>
                        <p><strong>Telepon:</strong> {{ seniman.noTelp_seniman }}</p>
                        <p><strong>Tanggal Lahir:</strong> {{ seniman.tgl_lahir }}</p>
                        <p><strong>Alamat:</strong> {{ seniman.alamat_seniman }}</p>
                        <p><strong>Lama Pengalaman:</strong> {{ seniman.lama_pengalaman }} tahun</p>
                        <p><strong>Deskripsi:</strong> {{ seniman.deskripsi_seniman }}</p>
                    </div>
                </div>
                <p v-else>Loading...</p>
            </div>
        </section>
    </main>
</template>


<script>
import Sidebar from '../components/SidebarSeniman.vue';
import axios from '../services/api.js';

export default {
    components: {
        Sidebar
    },
    data() {
        return {
            seniman: null,
        };
    },
    mounted() {
        const senimanId = localStorage.getItem("seniman_id");

        if (senimanId) {
            this.fetchArtistData(senimanId);
        } else {
            console.error('Seniman ID not found in localStorage');
        }
    },
    methods: {
        async fetchArtistData(senimanId) {
            try {
                const response = await axios.get(`/seniman/${senimanId}`);
                console.log('API Response:', response.data);
                this.seniman = response.data.data;

                // Construct the full URL for the avatar image
                if (this.seniman.user && this.seniman.user.foto) {
                    this.seniman.avatar = `http://localhost:8000/storage/${this.seniman.user.foto}`;
                } else {
                    this.seniman.avatar = 'default-avatar.jpg'; // Default image
                }

            } catch (error) {
                console.error('Error fetching artist data:', error);
                alert("Failed to fetch artist data. Please try again later.");
            }
        }
    }
};
</script>

<style scoped lang="scss">
.dashboard-page {
    display: flex;
    background-color: #f5d99d;
    padding: 2rem;
}

.artist-profile {
    margin-top: 2rem;
}

.profile-card {
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: box-shadow 0.3s;

    &:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
}

.profile-details {
    display: flex;
    align-items: center;
}

.avatar {
    width: 100px;
    height: 100px;
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
</style>
