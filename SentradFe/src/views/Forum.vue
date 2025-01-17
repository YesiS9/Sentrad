<template>
    <div class="forum-page">
        <Sidebar />
        <main class="forum-data">
            <div class="content">
                <div class="search-bar">
                    <input type="text" v-model="searchQuery" placeholder="Cari forum..." />
                </div>

                <div class="section">
                    <h2>Forum Saya</h2>
                    <div class="forum-list">
                        <div class="forum-card add-forum" @click="goToAddForum">
                            <div class="forum-info">
                                <span class="add-icon">+</span>
                            </div>
                        </div>
                        <div class="forum-card" v-for="forum in filteredMyForums" :key="forum.id">
                            <div class="forum-info" @click="goToForumDetail(forum.id)">
                                <h3>{{ forum.judul_forum }}</h3>
                                <p>Jumlah anggota: {{ forum.anggota_forums_count }}</p>
                            </div>
                            <div class="forum-actions">
                                <button class="edit-btn" @click.stop="editForum(forum.id)">Edit</button>
                                <button class="delete-btn" @click.stop="deleteForum(forum.id)">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>Forum Yang Diikuti</h2>
                    <div class="forum-list">
                        <div class="forum-card" v-for="forum in followedForums" :key="forum.id">
                            <div class="forum-info" @click="goToForumDetail(forum.id)">
                                <h3>{{ forum.judul_forum }}</h3>
                                <p>Dibuat oleh: {{ forum.nama_seniman }}</p>
                                <p>Jumlah anggota: {{ forum.anggota_forums_count }}</p>
                            </div>
                            <div class="forum-actions">
                                <button class="leave-btn" @click.stop="leaveForum(forum.id)">Keluar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h2>Forum Lainnya</h2>
                    <div class="forum-list">
                        <div class="forum-card" v-for="forum in otherForums" :key="forum.id">
                            <div class="forum-info">
                                <h3>{{ forum.judul_forum }}</h3>
                                <p>Dibuat oleh: {{ forum.nama_seniman }}</p>
                                <p>Jumlah anggota: {{ forum.anggota_forums_count }}</p>
                            </div>
                            <div class="forum-actions">
                                <button class="join-btn" @click.stop="joinForum(forum.id)">Gabung</button>
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
    name: "ForumPage",
    components: { Sidebar },
    setup() {
        const searchQuery = ref("");
        const myForums = ref([]);
        const followedForums = ref([]);
        const otherForums = ref([]);
        const router = useRouter();

        const fetchMyForums = async () => {
            const token = localStorage.getItem("authToken");
            const senimanId = localStorage.getItem("seniman_id");
            try {
                const response = await axios.get(`/myForum?seniman_id=${senimanId}`, {
                    headers: { Authorization: `Bearer ${token}` },
                });
                myForums.value = response.data.data;
            } catch (error) {
                console.error("Failed to fetch your forums:", error);
                myForums.value = [];
            }
        };

        const fetchFollowedForums = async () => {
            const token = localStorage.getItem("authToken");
            try {
                const response = await axios.get("/followForum", {
                    headers: { Authorization: `Bearer ${token}` },
                });
                followedForums.value = response.data.data;
                await Promise.all(followedForums.value.map(forum => getSenimanName(forum.seniman_id, forum)));
            } catch (error) {
                console.error("Failed to fetch followed forums:", error);
                followedForums.value = [];
            }
        };

        const fetchOtherForums = async () => {
            const token = localStorage.getItem("authToken");
            try {
                const response = await axios.get("/forum", {
                    headers: { Authorization: `Bearer ${token}` },
                });
                otherForums.value = response.data.data;
                await Promise.all(otherForums.value.map(forum => getSenimanName(forum.seniman_id, forum)));
            } catch (error) {
                console.error("Failed to fetch other forums:", error);
                otherForums.value = [];
            }
        };

        const getSenimanName = async (senimanId, forum) => {
            try {
                const response = await axios.get(`/seniman/${senimanId}`);
                if (response.status === 200 && response.data.status === "success") {
                    forum.nama_seniman = response.data.data.nama_seniman;
                } else {
                    forum.nama_seniman = "Unknown Artist";
                }
            } catch (error) {
                console.error("Error fetching artist name:", error.message);
                forum.nama_seniman = "Unknown Artist";
            }
        };

        onMounted(() => {
            fetchMyForums();
            fetchFollowedForums();
            fetchOtherForums();
        });

        const goToAddForum = () => {
            router.push("/form-forum");
        };

        const goToForumDetail = (id) => {
            router.push(`/detail-forum/${id}`);
        };

        const editForum = (id) => {
            router.push({ name: 'FormForumEdit', params: { id } });
        };

        const deleteForum = async (forumId) => {
            Swal.fire({
                title: "Apakah Anda yakin ingin menghapus forum ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem("authToken");
                    try {
                        await axios.delete(`/forum/${forumId}`, {
                            headers: { Authorization: `Bearer ${token}` },
                        });
                        Swal.fire("Deleted!", "Forum berhasil dihapus.", "success");
                        fetchMyForums();
                    } catch (error) {
                        console.error("Failed to delete forum:", error);
                        Swal.fire("Error", "Terjadi kesalahan. Coba lagi.", "error");
                    }
                }
            });
        };

        const leaveForum = async (forumId) => {
            Swal.fire({
                title: "Apakah Anda yakin ingin keluar dari forum ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const token = localStorage.getItem("authToken");
                    const anggotaId = localStorage.getItem("seniman_id");
                    console.log('senimanId:', anggotaId);
                    try {
                        await axios.delete(`/out-forum/${forumId}/anggota/${anggotaId}`, {
                            headers: { Authorization: `Bearer ${token}` },
                        });
                        Swal.fire("Berhasil!", "Anda telah keluar dari forum.", "success");
                        fetchFollowedForums();
                    } catch (error) {
                        console.error("Failed to leave forum:", error);
                        Swal.fire("Error", "Terjadi kesalahan. Coba lagi.", "error");
                    }
                }
            });
        };

        const joinForum = async (forumId) => {
            const token = localStorage.getItem("authToken");
            const senimanId = localStorage.getItem("seniman_id");
            try {
                const response = await axios.post("/join-forum", {
                    forum_id: forumId, seniman_id: senimanId
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                Swal.fire("Berhasil!", response.data.message, "success");
                fetchFollowedForums();
                fetchOtherForums();
            } catch (error) {
                if (error.response?.status === 409) {
                    Swal.fire("Info", "Anda sudah menjadi anggota forum ini.", "info");
                } else {
                    console.error("Failed to join the forum:", error);
                    Swal.fire("Error", "Gagal bergabung. Coba lagi.", "error");
                }
            }
        };

        const filteredMyForums = computed(() => {
            return myForums.value?.filter(forum =>
                forum.judul_forum.toLowerCase().includes(searchQuery.value.toLowerCase())
            ) || [];
        });

        return {
            searchQuery,
            filteredMyForums,
            followedForums,
            otherForums,
            goToAddForum,
            goToForumDetail,
            editForum,
            deleteForum,
            leaveForum,
            joinForum,
        };
    },
};
</script>

<style lang="scss" scoped>
.forum-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.forum-data {
  display: flex;
  background-color: #f5d99d;
}
.content {
  flex: 1;
  padding: 20px;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
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
.forum-list {
  display: flex;
  flex-wrap: wrap;
}
.forum-card {
  border: 1px solid #ddd;
  padding: 10px;
  margin: 10px;
  width: 200px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  cursor: pointer;
  background: #ffff;
}
.add-forum {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f0f0f0;
  font-size: 2em;
  font-weight: bold;
}
.add-icon {
  font-size: 3em;
  color: #888;
}
.forum-info {
  margin-bottom: 10px;
}
.forum-actions {
  display: flex;
  justify-content: space-between;
}
button {
  padding: 5px 10px;
  cursor: pointer;
  border: none;
  border-radius: 5px;
  font-size: 14px;
}

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

  .join-btn {
    background-color: #21aa21;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;

    &:hover {
      background-color: rgb(28, 206, 72);
    }
  }
  .leave-btn {
    background-color: #f4d136;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;

    &:hover {
      background-color: rgb(139, 102, 0);
    }
  }
</style>
