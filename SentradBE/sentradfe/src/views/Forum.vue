<template>
    <Sidebar />
    <main class="forum-page">
        <div class="content">
            <div class="search-bar">
                <input type="text" v-model="searchQuery" placeholder="Search forums..." />
            </div>
        <div class="section">
          <h2>Forum Saya</h2>
          <div class="forum-list">
            <div class="forum-card add-forum" @click="goToAddForum">
              <div class="forum-info">
                <span class="add-icon">+</span>
              </div>
            </div>
            <div class="forum-card" v-for="forum in myForums" :key="forum.id">
              <div class="forum-info">
                <h3>{{ forum.name }}</h3>
                <p>Jumlah anggota: {{ forum.membersCount }}</p>
              </div>
              <div class="forum-actions">
                <button @click="editForum(forum.id)">Edit</button>
                <button @click="deleteForum(forum.id)">Hapus</button>
              </div>
            </div>
          </div>
        </div>
        <div class="section">
          <h2>Forum Yang Diikuti</h2>
          <div class="forum-list">
            <div class="forum-card" v-for="forum in followedForums" :key="forum.id">
              <div class="forum-info">
                <h3>{{ forum.name }}</h3>
                <p>Oleh: {{ forum.creator }}</p>
                <p>Jumlah anggota: {{ forum.membersCount }}</p>
              </div>
              <div class="forum-actions">
                <button @click="leaveForum(forum.id)">Keluar</button>
              </div>
            </div>
          </div>
        </div>
        <div class="section">
          <h2>Forum Lainnya</h2>
          <div class="forum-list">
            <div class="forum-card" v-for="forum in otherForums" :key="forum.id">
              <div class="forum-info">
                <h3>{{ forum.name }}</h3>
                <p>Oleh: {{ forum.creator }}</p>
                <p>Jumlah anggota: {{ forum.membersCount }}</p>
              </div>
              <div class="forum-actions">
                <button @click="joinForum(forum.id)">Gabung</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </template>

  <script>
  import Sidebar from '../components/SidebarSeniman.vue';

  export default {
    name: "ForumPage",
    components: {
      Sidebar
    },
    data() {
      return {
        myForums: [
          // Array of forums created by the logged-in user
        ],
        followedForums: [
          // Array of forums followed by the logged-in user
        ],
        otherForums: [
          // Array of other forums available to join
        ]
      };
    },
    methods: {
      goToAddForum() {
        this.$router.push('/form-penilai');
      },
      editForum(forumId) {
        // Handle edit forum
      },
      deleteForum(forumId) {
        // Handle delete forum
      },
      leaveForum(forumId) {
        // Handle leave forum
      },
      joinForum(forumId) {
        // Handle join forum
      }
    }
  };
  </script>

  <style lang="scss" scoped>
  .forum-page {
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
}
</style>
