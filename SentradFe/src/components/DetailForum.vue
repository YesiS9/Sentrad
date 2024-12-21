<template>
    <div class="page">
        <Sidebar />
        <main class="info-forum" v-bind="$attrs">
        <div class="card forum-detail" v-if="forum">
            <div class="card-header">
            <h2>Detail Forum</h2>
            <button @click="goBack" class="back-button">
                <i class="material-icons">arrow_back_ios</i>
            </button>
            </div>
            <div class="forum-info">
            <p><strong>Judul Forum:</strong> {{ forum.judul_forum }}</p>
            <p><strong>Seniman:</strong> {{ forum.nama_seniman }}</p>
            <p><strong>Kategori:</strong> {{ forum.nama_kategori }}</p>
            <p><strong>Jumlah Anggota:</strong> {{ forum.anggota_forums_count }}</p>
            </div>
        </div>
        <div class="card komen-forum">
        <h3>Komentar Forum</h3>
        <div class="komen-list" v-if="komenForum.length">
            <div v-for="(komen, index) in komenForum" :key="komen.id" class="komen-card">
            <div class="komen-content">
                <div class="text-content">
                <p class="nama-seniman">{{ komen.nama_seniman }}:</p>
                <template v-if="editIndex === index">
                    <input v-model="editedComment" class="edit-input" />
                </template>
                <template v-else>
                    <p class="isi-komen">{{ komen.isi_komenForum }}</p>
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
        <p v-else>Tidak ada komentar pada forum ini.</p>

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
  const forum = ref(null);
  const komenForum = ref([]);
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

  const getForumDetail = async (id) => {
    try {
      const response = await axios.get(`/forum/${id}`);
      if (response.status === 200 && response.data.status === 'success') {
        forum.value = response.data.data;
        await getSenimanName(forum.value.seniman_id);
        await getKategoriName(forum.value.kategori_id);
        await getKomenList(id);
      } else {
        console.error('Failed to fetch forum detail:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching forum detail:', error.message);
    }
  };

  const getSenimanName = async (senimanId) => {
    try {
      const response = await axios.get(`/seniman/${senimanId}`);
      if (response.status === 200 && response.data.status === 'success') {
        forum.value.nama_seniman = response.data.data.nama_seniman;
      } else {
        console.error('Failed to fetch seniman name:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching seniman name:', error.message);
    }
  };

  const getKategoriName = async (kategoriId) => {
    try {
      const response = await axios.get(`/kategoriSeni/${kategoriId}`);
      if (response.status === 200 && response.data.status === 'success') {
        forum.value.nama_kategori = response.data.data.nama_kategori;
      } else {
        console.error('Failed to fetch kategori name:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching kategori name:', error.message);
    }
  };
  const replyComment = (komen) => {
  alert(`Reply to: ${komen.isi_komenForum}`);
};
  const startEditComment = (index, komen) => {
  editIndex.value = index;
  editedComment.value = komen.isi_komenForum;
  showMenu.value = null;
};

const saveEditComment = async (komen) => {
  try {
    const response = await axios.put(`/komenForum/${komen.id}`, {
      isi_komenForum: editedComment.value,
    });
    if (response.status === 200 && response.data.status === 'success') {
      komenForum.value[editIndex.value].isi_komenForum = editedComment.value;
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
    cancelButtonText: 'Batal',
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`/komenForum/${komen.id}`);
      if (response.status === 200 && response.data.status === 'success') {
        komenForum.value = komenForum.value.filter((k) => k.id !== komen.id);
        Swal.fire('Terhapus!', 'Komentar telah dihapus.', 'success');
      }
    } catch (error) {
      console.error('Error deleting comment:', error.message);
    }
  }
};

  const getKomenList = async (forumId) => {
    try {
      const response = await axios.get(`/komenForum?forum_id=${forumId}`);
      if (response.status === 200 && response.data.status === 'success') {
        komenForum.value = await Promise.all(response.data.data.map(async (komen) => {
          const senimanResponse = await axios.get(`/seniman/${komen.seniman_id}`);
          if (senimanResponse.status === 200 && senimanResponse.data.status === 'success') {
            komen.nama_seniman = senimanResponse.data.data.nama_seniman;
          } else {
            komen.nama_seniman = 'Seniman tidak ditemukan';
          }
          return komen;
        }));
      } else {
        console.error('Failed to fetch komen list:', response.data.message);
      }
    } catch (error) {
      console.error('Error fetching komen list:', error.message);
    }
  };

  const addComment = async () => {
    if (!newComment.value) return;
    const seniman_Id = localStorage.getItem('seniman_id');
    try {
      const response = await axios.post(`/komenForum`, {
        forum_id: forum.value.id,
        isi_komenForum: newComment.value,
        seniman_id: seniman_Id,
      });
      if (response.status === 200) {
        const senimanResponse = await axios.get(`/seniman/${seniman_Id}`);
        komenForum.value.push({
          ...response.data,
          nama_seniman: senimanResponse.data.data.nama_seniman,
        });
        newComment.value = '';
      } else {
        console.error('Failed to add comment:', response.data.message);
      }
    } catch (error) {
      console.error('Error adding comment:', error.message);
    }
  };

  const goBack = () => {
    router.push('/forum');
  };

  onMounted(() => {
    if (id) {
      getForumDetail(id);
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
  .info-forum {
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
  position: relative; 
}

.komen-card p {
  margin: 0;
}

.komen-card strong {
  color: #007bff;
}

.text-content {
  margin-right: 40px; 
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
