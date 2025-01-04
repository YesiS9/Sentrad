<template>
  <div class="container-fluid page">
    <div class="row">
      <div class="col-lg-3 col-md-4 d-none d-md-block">
        <Sidebar />
      </div>
      <div class="col-lg-9 col-md-8">
        <main class="info-forum mt-3" v-bind="$attrs">
          <div class="card forum-detail mb-3" v-if="forum">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h2 class="h5 mb-0">Detail Forum</h2>
              <button @click="goBack" class="btn btn-outline-secondary btn-sm">
                <i class="material-icons">arrow_back_ios</i> Kembali
              </button>
            </div>
            <div class="card-body">
              <p><strong>Judul Forum:</strong> {{ forum.judul_forum }}</p>
              <p><strong>Seniman:</strong> {{ forum.nama_seniman }}</p>
              <p><strong>Kategori:</strong> {{ forum.nama_kategori }}</p>
              <p><strong>Jumlah Anggota:</strong> {{ forum.anggota_forums_count }}</p>
            </div>
          </div>

          <div class="card komen-forum">
            <div class="card-header">
              <h3 class="h6 mb-0">Komentar Forum</h3>
            </div>
            <div class="card-body">
              <div v-if="komenForum.length">
                <div v-for="(komen, index) in komenForum" :key="komen.id" class="card mb-3">
                  <div class="card-body">
                    <div class="d-flex justify-content-between">
                      <div>
                        <p class="fw-bold mb-1">{{ komen.nama_seniman }}</p>
                        <template v-if="editIndex === index">
                          <input v-model="editedComment" class="form-control" />
                        </template>
                        <template v-else>
                          <p class="mb-0">{{ komen.isi_komenForum }}</p>
                        </template>
                      </div>
                      <div>
                        <button
                          v-if="editIndex !== index"
                          @click="toggleMenu(index)"
                          class="btn btn-sm btn-outline-secondary"
                        >
                          <i class="material-icons">more_vert</i>
                        </button>
                        <div v-if="showMenu === index" class="dropdown-menu dropdown-menu-end show">
                          <a class="dropdown-item" @click="replyComment(komen)">Reply</a>
                          <a class="dropdown-item" @click="startEditComment(index, komen)">Edit</a>
                          <a class="dropdown-item text-danger" @click="deleteComment(komen)">Hapus</a>
                        </div>
                      </div>
                    </div>

                    <div v-if="editIndex === index" class="mt-2">
                      <button @click="saveEditComment(komen)" class="btn btn-primary btn-sm me-2">Simpan</button>
                      <button @click="cancelEditComment" class="btn btn-outline-secondary btn-sm">Batal</button>
                    </div>
                  </div>
                </div>
              </div>
              <p v-else class="text-muted">Tidak ada komentar pada forum ini.</p>

              <div class="input-group mt-3">
                <input
                  v-model="newComment"
                  type="text"
                  class="form-control"
                  placeholder="Tambahkan komentar..."
                  @keyup.enter="addComment"
                />
                <button @click="addComment" class="btn btn-primary">
                  <i class="material-icons">send</i>
                </button>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
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
    //saas
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

<style scoped>
.page {
  background-color: #f2d395;
  color: #333;
  min-height: 100vh;
}

/* Info Forum */
.info-forum {
  padding: 1rem;
}

/* Kartu */
.card {
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Detail Forum */
.forum-detail .card-header {
  background-color: #ffe4b2;
  font-weight: bold;
  color: #333;
}

/* Komentar Forum */
.komen-forum .card-header {
  background-color: #f9f9f9;
  border-bottom: 1px solid #ddd;
}

.komen-forum .dropdown-menu {
  padding: 0;
}

.komen-forum .dropdown-item {
  font-size: 0.875rem;
}

.komen-forum .dropdown-item:hover {
  background-color: #f2d395;
}

.komen-forum .input-group input {
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.komen-forum .input-group button {
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

/* Tombol */
.btn {
  font-size: 0.875rem;
}

.btn-primary {
  background-color: #f2d395;
  border: none;
}

.btn-primary:hover {
  background-color: #f1c27d;
}

.btn-outline-secondary {
  color: #333;
  border: 1px solid #ddd;
}

.btn-outline-secondary:hover {
  background-color: #f9f9f9;
}

/* Responsivitas */
@media (max-width: 768px) {
  .info-forum {
    padding: 0.5rem;
  }

  .komen-forum .dropdown-item {
    font-size: 0.75rem;
  }

  .btn {
    font-size: 0.75rem;
  }
}
</style>