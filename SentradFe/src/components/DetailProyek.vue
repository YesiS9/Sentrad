<template>
    <div class="page">
        <Sidebar />
        <main class="info-proyek" v-bind="$attrs">
            <div class="card proyek-detail" v-if="proyek">
            <div class="card-header">
                <h2>Detail Proyek</h2>
                <button @click="goBack" class="back-button">
                <i class="material-icons">arrow_back_ios</i>
                </button>
            </div>
            <div class="proyek-info">
                <p><strong>Nama Seniman:</strong> {{ proyek.nama_seniman }}</p>
                <p><strong>Nama Kategori:</strong> {{ proyek.nama_kategori }}</p>
                <p><strong>Judul Proyek:</strong> {{ proyek.judul_proyek }}</p>
                <p><strong>Deskripsi Proyek:</strong> {{ proyek.deskripsi_proyek }}</p>
                <p><strong>Waktu Mulai:</strong> {{ formatDateTime(proyek.waktu_mulai) }} WIB</p>
                <p><strong>Waktu Selesai:</strong> {{ formatDateTime(proyek.waktu_selesai) }} WIB</p>
                <p><strong>Lokasi Proyek:</strong> {{ proyek.lokasi_proyek }}</p>
                <p><strong>Tautan Proyek:</strong> <a :href="proyek.tautan_proyek" target="_blank">{{ proyek.tautan_proyek }}</a></p>
                <p><strong>Jumlah Like:</strong> {{ proyek.jumlah_like }}</p>
            </div>
            </div>

            <div class="card komen-proyek">
            <h3>Komentar Proyek</h3>
            <div class="komen-list" v-if="komenProyek.length">
                <div v-for="(komen, index) in komenProyek" :key="komen.id" class="komen-card">
                <div class="komen-content">
                    <div class="text-content">
                    <p class="nama-seniman">{{ komen.nama_seniman }}:</p>
                    <template v-if="editIndex === index">
                        <input v-model="editedComment" class="edit-input" />
                    </template>
                    <template v-else>
                        <p class="isi-komen">{{ komen.isi_komenProyek }}</p>
                    </template>
                    </div>
                    <div class="menu-actions">
                    <button v-if="editIndex !== index" @click="toggleMenu(index)" class="menu-button">
                        <i class="material-icons">more_vert</i>
                    </button>
                    <div v-if="showMenu === index" class="popup-menu">
                        <ul>
                        <li @click="replyComment(komen)">Reply</li>
                        <li @click="startEditComment(index, komen)">Edit</li>
                        <li @click="deleteComment(komen)">Hapus</li>
                        </ul>
                    </div>
                    <div v-if="editIndex === index" class="edit-actions">
                        <button @click="saveEditComment(komen)">Simpan</button>
                        <button @click="cancelEditComment">Batal</button>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <p v-else>Tidak ada komentar pada proyek ini.</p>

            <div class="add-comment">
                <input v-model="newComment" type="text" placeholder="Tambahkan komentar..." @keyup.enter="addComment" />
                <button @click="addComment" class="send-button">
                <i class="material-icons">send</i>
                </button>
            </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '../components/SidebarSeniman.vue';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const id = route.params.id;
const showMenu = ref(null);
const proyek = ref(null);
const komenProyek = ref([]);
const newComment = ref('');
const editIndex = ref(null);
const editedComment = ref('');

const toggleMenu = (index) => {
  if (showMenu.value === index) {
    showMenu.value = null;
  } else {
    showMenu.value = index;
  }
};

const getProyekDetail = async (id) => {
  try {
    const response = await axios.get(`/proyek/${id}`);
    if (response.status === 200 && response.data.status === 'success') {
      proyek.value = response.data.data;
      await Promise.all([getSenimanName(proyek.value.seniman_id), getKategoriName(proyek.value.kategori_id)]);
      getKomenList(id);
    }
  } catch (error) {
    console.error('Error fetching proyek detail:', error.message);
  }
};

const getSenimanName = async (senimanId) => {
  try {
    const response = await axios.get(`/seniman/${senimanId}`);
    if (response.status === 200 && response.data.status === 'success') {
      proyek.value.nama_seniman = response.data.data.nama_seniman;
    }
  } catch (error) {
    console.error('Error fetching seniman name:', error.message);
  }
};

const getKategoriName = async (kategoriId) => {
  try {
    const response = await axios.get(`/kategoriSeni/${kategoriId}`);
    if (response.status === 200 && response.data.status === 'success') {
      proyek.value.nama_kategori = response.data.data.nama_kategori;
    }
  } catch (error) {
    console.error('Error fetching kategori name:', error.message);
  }
};

const replyComment = (komen) => {
  alert(`Reply to: ${komen.isi_komenProyek}`);
};

const startEditComment = (index, komen) => {
  editIndex.value = index;
  editedComment.value = komen.isi_komenProyek;
  showMenu.value = null;
};

const saveEditComment = async (komen) => {
  try {
    const response = await axios.put(`/komenProyek/${komen.id}`, {
      isi_komenProyek: editedComment.value,
    });
    if (response.status === 200 && response.data.status === 'success') {
      komenProyek.value[editIndex.value].isi_komenProyek = editedComment.value;
      editIndex.value = null;
    }
  } catch (error) {
    console.error('Error saving comment:', error.message);
  }
};

const cancelEditComment = () => {
  editIndex.value = null;
  editedComment.value = '';
};

const deleteComment = async (komen) => {
  const result = await Swal.fire({
    title: 'Yakin ingin menghapus komentar ini?',
    text: 'Komentar ini akan dihapus secara permanen!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`/komenProyek/${komen.id}`);
      if (response.status === 200 && response.data.status === 'success') {
        komenProyek.value = komenProyek.value.filter((k) => k.id !== komen.id);
        Swal.fire('Terhapus!', 'Komentar telah dihapus.', 'success');
      }
    } catch (error) {
      console.error('Error deleting comment:', error.message);
    }
  }
};

const getKomenList = async (proyekId) => {
  try {
    const response = await axios.get(`/komenProyek?proyek_id=${proyekId}`);
    if (response.status === 200 && response.data.status === 'success') {
      komenProyek.value = await Promise.all(
        response.data.data.map(async (komen) => {
          const senimanResponse = await axios.get(`/seniman/${komen.seniman_id}`);
          komen.nama_seniman = senimanResponse.status === 200 && senimanResponse.data.status === 'success'
            ? senimanResponse.data.data.nama_seniman
            : 'Seniman tidak ditemukan';
          return komen;
        })
      );
    }
  } catch (error) {
    console.error('Error fetching komen list:', error.message);
  }
};

const addComment = async () => {
  if (!newComment.value) return;
  const seniman_Id = localStorage.getItem('seniman_id');
  try {
    const response = await axios.post('/komenProyek', {
      proyek_id: proyek.value.id,
      isi_komenProyek: newComment.value,
      seniman_id: seniman_Id,
    });
    if (response.status === 200 && response.data.status === 'success') {
      komenProyek.value.push({
        ...response.data.data,
        nama_seniman: (await axios.get(`/seniman/${seniman_Id}`)).data.data.nama_seniman,
      });
      newComment.value = '';
    }
  } catch (error) {
    console.error('Error adding comment:', error.message);
  }
};

const formatDateTime = (dateTimeString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
  return new Date(dateTimeString).toLocaleString('id-ID', options);
};

const goBack = () => {
  router.back();
};

onMounted(() => {
  getProyekDetail(id);
});
</script>

<style lang="scss" scoped>
.page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .info-proyek {
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
  border-bottom: 1px solid #ffffff;
  padding-bottom: 0.5rem;
}

.komen-list {
  margin-top: 1rem;
}

.komen-card {
  background-color: #fafafa;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  position: relative; /* To make the actions menu position absolute */
}

.komen-card p {
  margin: 0;
}

.komen-card strong {
  color: #007bff;
}

.text-content {
  margin-right: 40px; /* Allow space for the menu icon */
}

.menu-actions {
  position: absolute;
  right: 10px;
  top: 10px;
}

.menu-button {
  background: none;
  border: none;
  cursor: pointer;
  color: #555;
}

.popup-menu {
  background-color: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  position: absolute;
  right: 0;
  top: 30px;
  width: 100px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 999;
}

.popup-menu ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.popup-menu ul li {
  padding: 10px;
  cursor: pointer;
  border-bottom: 1px solid #eee;

}

.popup-menu ul li:hover {
  background-color: #f0f0f0;
}

.edit-actions {
  display: flex;
  gap: 10px; /* Add space between buttons */
  margin-top: 10px;
}

.edit-actions button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.edit-actions button:first-child {
  background-color: #4caf50; /* Save button */
  color: white;
}

.edit-actions button:last-child {
  background-color: #f44336; /* Cancel button */
  color: white;
}

.add-comment {
  display: flex;
  margin-top: 1rem;
}

.add-comment input {
  flex: 1;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-right: 0.5rem;
}

.send-button {
  background-color: #4caf50;
  color: white;
  padding: 0.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

  </style>
