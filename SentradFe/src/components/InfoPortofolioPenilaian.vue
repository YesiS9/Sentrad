<template>
    <div class="page">
        <Sidebar />
        <main class="info-portofolio">
        <div class="card portofolio-detail" v-if="portofolio">
            <div class="card-header">
            <h2>Detail Portofolio</h2>
            <button @click="goBack" class="back-button">
                <i class="material-icons">arrow_back_ios</i>
            </button>
            </div>
            <div class="portofolio-info">
            <p v-if="portofolio.nama_kelompok"><strong>Nama Kelompok:</strong> {{ portofolio.nama_kelompok }}</p>
            <p><strong>Nama Seniman:</strong> {{ portofolio.nama_seniman }}</p>
            <p><strong>Judul Portofolio:</strong> {{ portofolio.judul_portofolio }}</p>
            <p><strong>Tanggal Dibuat:</strong> {{ formatDate(portofolio.tgl_dibuat) }}</p>
            <p><strong>Deskripsi Portofolio:</strong> {{ portofolio.deskripsi_portofolio }}</p>
            <p><strong>Jumlah Karya:</strong> {{ portofolio.jumlah_karya }}</p>
            </div>
        </div>

        <div class="card karya-portofolio">
            <h3>Karya dalam Portofolio</h3>
            <div class="karya-grid" v-if="karyaList.length">
            <div v-for="karya in karyaList" :key="karya.id" class="karya-card" @click="goToKarya(karya.id)">
                <div class="karya-thumbnail">
                <img v-if="karya.media_type === 'image'" :src="karya.media_url" alt="Karya Image" />
                <video v-if="karya.media_type === 'video'" controls>
                    <source :src="karya.media_url" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                </div>
                <div class="karya-info">
                <h4>{{ karya.judul_karya }}</h4>
                </div>
            </div>
            </div>
            <p v-else>Tidak ada karya dalam portofolio ini.</p>
        </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import { useRouter } from 'vue-router';
import Sidebar from '../components/SidebarPenilai.vue';

const props = defineProps({
  id: String,
  individuId: String,
  kelompokId: String,
});

const router = useRouter();

const portofolio = ref(null);
const karyaList = ref([]);

const getPortofolioDetail = async (id) => {
  try {
    const response = await axios.get(`/portofolio/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      portofolio.value = {
        ...response.data.data.portofolio,
        nama_seniman: response.data.data.nama_seniman,
        nama_kelompok: response.data.data.nama_kelompok,
      };
      getKaryaList(id);
    } else {
      console.error('Failed to fetch portofolio detail:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching portofolio detail:', error.message);
  }
};


const getKaryaList = async (portofolioId) => {
  try {
    const response = await axios.get(`/karyas/${portofolioId}`);
    if (response.status === 200 && response.data.status === 'success') {
      karyaList.value = response.data.data.map(karya => {
        const mediaUrl = karya.media_url || '';
        const mediaType = mediaUrl.endsWith('.mp4') ? 'video' : 'image';
        return { ...karya, media_type: mediaType };
      });
    } else {
      console.error('Failed to fetch karya list:', response.data.message);
    }
  } catch (error) {
    console.error('Error fetching karya list:', error.message);
  }
};

const goToKarya = (id) => {
  router.push({ name: 'InfoKarya', params: { id } });
};


const formatDate = (date) => {
  if (!date) return 'Tanggal tidak tersedia';
  const [year, month, day] = date.split('-');
  return `${day}/${month}/${year}`;
};

const goBack = () => {
  if (props.individuId) {
    router.push({ name: 'PortofolioIndividu', params: { individuId: props.individuId } });
  } else if (props.kelompokId) {
    router.push({ name: 'PortofolioKelompok', params: { kelompokId: props.kelompokId } });
  } else {
    console.error('Unable to determine the correct route.');
  }
};

onMounted(() => {
  if (props.id) {
    getPortofolioDetail(props.id);
  }
});
</script>

<style lang="scss" scoped>
.page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .info-portofolio {
    padding: 2rem;
    background-color: #f5d99d;
  }

  .card {
    padding: 1.5rem;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
  }

  .back-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.5rem;
    color: #333;
  }

  h2, h3 {
    color: #333;
    margin: 0;
  }

  .portofolio-info p {
    margin: 0.5rem 0;
    color: #333;
  }

  .karya-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
  }

  .karya-card {
    background-color: #fafafa;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease-in-out;
  }

  .karya-card:hover {
    transform: scale(1.05);
  }

  .karya-thumbnail img, .karya-thumbnail video {
    width: 100%;
    height: auto;
    display: block;
  }

  .karya-info {
    padding: 1rem;
    text-align: center;
  }

  .karya-info h4 {
    margin: 0;
    color: #333;
  }

  .karya-link {
    text-decoration: none;
    color: #007bff;
  }

  .karya-link:hover {
    color: #0056b3;
  }

  .portofolio-info p strong {
    color: #222;
  }
  </style>
