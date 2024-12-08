<template>
    <div class="proyek-page">
        <Sidebar />
        <main class="proyek-data">
        <div class="content">
            <div class="search-bar">
            <input type="text" v-model="searchQuery" placeholder="Search proyek..." />
            </div>

            <div class="section">
            <h2>Event Saya</h2>
            <div class="proyek-list">
                <div class="proyek-card add-proyek" @click="goToAddProyek">
                <div class="proyek-info">
                    <span class="add-icon">+</span>
                </div>
                </div>
                <div
                class="proyek-card"
                v-for="proyek in filteredMyProyeks"
                :key="proyek.id"
                @click="goToProyekDetail(proyek.id)"
                >
                <div class="proyek-info">
                    <h3>{{ proyek.judul_proyek }}</h3>
                    <p>Waktu Mulai: {{ formatDate(proyek.waktu_mulai) }}</p>
                    <p>Waktu Selesai: {{ formatDate(proyek.waktu_selesai) }}</p>
                    <p>Likes: <span class="likes-count">{{ proyek.jumlah_like }}</span></p>
                </div>
                <div class="proyek-actions">
                    <button class="edit-btn" @click.stop="editProyek(proyek.id)">Edit</button>
                    <button class="delete-btn" @click.stop="deleteProyek(proyek.id)">Hapus</button>
                </div>
                <div class="social-actions">
                    <button @click.stop="likeProyek(proyek.id)">
                    <span class="heart-icon">❤️</span> Like ({{ proyek.jumlah_like }})
                    </button>
                    <button @click.stop="shareProyek(proyek.id)">
                    <span class="share-icon">🔗</span> Share
                    </button>
                </div>
                </div>
            </div>
            </div>
            <div class="section">
            <h2>Event Lainnya</h2>
            <div class="proyek-list">
                <div
                class="proyek-card"
                v-for="proyek in filteredProyeks"
                :key="proyek.id"
                @click="goToProyekDetail(proyek.id)"
                >
                <div class="proyek-info">
                    <h3>{{ proyek.judul_proyek }}</h3>
                    <p>Waktu Mulai: {{ formatDate(proyek.waktu_mulai) }}</p>
                    <p>Waktu Selesai: {{ formatDate(proyek.waktu_selesai) }}</p>
                    <p>Likes: <span class="likes-count">{{ proyek.jumlah_like }}</span></p>
                </div>
                <div class="social-actions">
                    <button @click.stop="toggleLikeProyek(proyek)">
                    <span class="heart-icon" :class="{ liked: proyek.liked }">❤️</span> Like ({{ proyek.jumlah_like }})
                    </button>
                    <button @click.stop="shareProyek(proyek.id)">
                    <span class="share-icon">🔗</span> Share
                    </button>
                </div>
                </div>
            </div>
            </div>
        </div>
        </main>
    </div>
  </template>

  <script>
  import { ref, onMounted, computed } from "vue";
  import { useRouter } from "vue-router";
  import axios from "../services/api.js";
  import Sidebar from "../components/SidebarSeniman.vue";
  import Swal from "sweetalert2";

  export default {
    name: "ProyekPage",
    components: {
      Sidebar,
    },
    setup() {
      const searchQuery = ref("");
      const myProyeks = ref([]);
      const proyek = ref([]);
      const router = useRouter();

      const formatDate = (dateString) => {
        const options = { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta', hour12: false };
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', options).replace(',', '').replace( ',  ');
      };

      const fetchMyProyeks = async () => {
            try {
                const token = localStorage.getItem("authToken");
                const senimanId = localStorage.getItem("seniman_id");
                const response = await axios.get(
                `/index-proyek?seniman_id=${senimanId}`,
                {
                    headers: { Authorization: `Bearer ${token}` },
                }
                );
                if (response.data && response.data.data) {
                myProyeks.value = response.data.data.map((p) => ({
                    ...p,
                    liked: p.is_liked || false,
                }));
                } else {
                myProyeks.value = [];
                }
            } catch (error) {
                console.error("Failed to fetch your projects:", error);
                myProyeks.value = [];
            }
        };

        const fetchProyeks = async () => {
        try {
            const token = localStorage.getItem("authToken");
            const response = await axios.get("/proyek", {
            headers: { Authorization: `Bearer ${token}` },
            });
            if (response.data && response.data.data) {
            proyek.value = response.data.data.map((p) => ({
                ...p,
                liked: p.is_liked,
            }));
            } else {
            proyek.value = []; // Initialize to empty array if no data is returned
            }
        } catch (error) {
            console.error("Failed to fetch projects:", error);
            proyek.value = []; // Ensure proyek is an array even on error
        }
        };


      onMounted(() => {
        fetchMyProyeks();
        fetchProyeks();
      });

      const goToAddProyek = () => {
        router.push("/form-proyek");
      };

      const goToProyekDetail = (id) => {
        router.push(`/detail-proyek/${id}`);
      };

      const editProyek = (id) => {
        router.push({ name: 'FormProyekEdit', params: { id } });
      };

      const deleteProyek = async (proyekId) => {
        Swal.fire({
          title: "Apakah Anda yakin ingin menghapus portofolio ini?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
        }).then(async (result) => {
          if (result.isConfirmed) {
            try {
              const token = localStorage.getItem("authToken");
              await axios.delete(`/proyek/${proyekId}`, {
                headers: { Authorization: `Bearer ${token}` },
              });
              Swal.fire("Deleted!", "The project has been deleted.", "success");
              fetchMyProyeks();
            } catch (error) {
              console.error("Failed to delete project:", error);
              Swal.fire(
                "Error",
                "There was a problem deleting the project. Please try again later.",
                "error"
              );
            }
          }
        });
      };

      const toggleLikeProyek = async (proyek) => {
        try {
          const token = localStorage.getItem("authToken");
          if (proyek.liked) {
            await axios.post(`/proyek/${proyek.id}/unlike`, {}, {
              headers: { Authorization: `Bearer ${token}` },
            });
            proyek.jumlah_like--;
          } else {
            await axios.post(`/proyek/${proyek.id}/like`, {}, {
              headers: { Authorization: `Bearer ${token}` },
            });
            proyek.jumlah_like++;
          }
          proyek.liked = !proyek.liked;
        } catch (error) {
          console.error("Failed to toggle like:", error);
          Swal.fire("Error", "Failed to toggle like. Please try again later.", "error");
        }
      };

      const shareProyek = (proyekId) => {
        // share proyek
      };

      const filteredMyProyeks = computed(() =>
        myProyeks.value.filter((proyek) =>
          proyek.judul_proyek.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      );

      const filteredProyeks = computed(() =>
        proyek.value.filter((proyek) =>
          proyek.judul_proyek.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
      );

      return {
        searchQuery,
        filteredMyProyeks,
        filteredProyeks,
        myProyeks,
        proyek,
        goToAddProyek,
        goToProyekDetail,
        editProyek,
        deleteProyek,
        toggleLikeProyek,
        shareProyek,
        formatDate,
      };
    },
  };
  </script>

  <style lang="scss" scoped>
  .proyek-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .proyek-data {
    display: flex;
    background-color: #f5d99d;
  }
  .content {
    flex: 1;
    padding: 20px;
  }
  .search-bar {
    position: absolute;
    right: 20px;
    top: 20px;
    width: 300px;
  }
  .search-bar input {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 20px;
    border: 1px solid #ddd;
  }
  .section {
    margin-bottom: 20px;
  }
  .proyek-list {
    display: flex;
    flex-wrap: wrap;
  }
  .proyek-card {
    border: 1px solid #ddd;
    padding: 10px;
    margin: 10px;
    width: 200px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background-color: #ffffff;
    cursor: pointer;
  }
  .add-proyek {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    font-size: 2em;
    font-weight: bold;
    cursor: pointer;
  }
  .add-icon {
    font-size: 3em;
    color: #888;
  }
  .proyek-info {
    margin-bottom: 10px;
  }
  .proyek-actions {
  display: flex;
  justify-content: space-between;

  .edit-btn {
    background-color: #f7941e;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;

    &:hover {
      background-color: darkorange;
    }
  }

  .delete-btn {
    background-color: #f44336;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;

    &:hover {
      background-color: darkred;
    }
  }
}

  .social-actions {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }
  button {
    padding: 5px 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  .heart-icon {
    margin-right: 5px;
  }
  .share-icon {
    margin-right: 5px;
  }
  .likes-count {
    font-weight: bold;
  }
  input {
    width: 100%;
    padding: 5px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  </style>
