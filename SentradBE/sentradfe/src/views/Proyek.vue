<template>
    <Sidebar />
    <main class="proyek-page">
      <div class="content">
        <!-- Search Bar -->
        <div class="search-bar">
          <input type="text" v-model="searchQuery" placeholder="Search proyek..." />
        </div>

        <!-- Proyek Created by the User -->
        <div class="section">
          <h2>Proyek Saya</h2>
          <div class="proyek-list">
            <div class="proyek-card add-proyek" @click="goToAddProyek">
              <div class="proyek-info">
                <span class="add-icon">+</span>
              </div>
            </div>
            <div class="proyek-card" v-for="proyek in myProyeks" :key="proyek.id">
              <div class="proyek-info">
                <h3>{{ proyek.title }}</h3>
                <p>{{ proyek.content }}</p>
              </div>
              <div class="proyek-actions">
                <button @click="editProyek(proyek.id)">Edit</button>
                <button @click="deleteProyek(proyek.id)">Hapus</button>
              </div>
              <!-- Social Media Style Actions -->
              <div class="social-actions">
                <button @click="likeProyek(proyek.id)">Like ({{ proyek.likes }})</button>
                <button @click="shareProyek(proyek.id)">Share</button>
                <div class="comments">
                  <div v-for="comment in proyek.comments" :key="comment.id" class="comment">
                    <strong>{{ comment.user.name }}:</strong> {{ comment.text }}
                  </div>
                  <input v-model="newComment" placeholder="Add a comment..." @keyup.enter="addComment(proyek.id)" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Other Proyeks -->
        <div class="section">
          <h2>Proyek Lainnya</h2>
          <div class="proyek-list">
            <div class="proyek-card" v-for="proyek in otherProyeks" :key="proyek.id">
              <div class="proyek-info">
                <h3>{{ proyek.title }}</h3>
                <p>{{ proyek.content }}</p>
              </div>
              <!-- Social Media Style Actions -->
              <div class="social-actions">
                <button @click="likeProyek(proyek.id)">Like ({{ proyek.likes }})</button>
                <button @click="shareProyek(proyek.id)">Share</button>
                <div class="comments">
                  <div v-for="comment in proyek.comments" :key="comment.id" class="comment">
                    <strong>{{ comment.user.name }}:</strong> {{ comment.text }}
                  </div>
                  <input v-model="newComment" placeholder="Add a comment..." @keyup.enter="addComment(proyek.id)" />
                </div>
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
    name: "ProyekPage",
    components: {
      Sidebar
    },
    data() {
      return {
        searchQuery: '',
        myProyeks: [
          // Array of proyeks created by the logged-in user
        ],
        otherProyeks: [
          // Array of other proyeks available to view
        ],
        newComment: '',
      };
    },
    methods: {
      goToAddProyek() {
        this.$router.push('/form-proyek'); // Redirect to add proyek form
      },
      editProyek(proyekId) {
        // Handle edit proyek
      },
      deleteProyek(proyekId) {
        // Handle delete proyek
      },
      likeProyek(proyekId) {
        // Handle like proyek
      },
      shareProyek(proyekId) {
        // Handle share proyek
      },
      addComment(proyekId) {
        if (this.newComment.trim() === '') return;

        const proyek = this.myProyeks.concat(this.otherProyeks).find(proyek => proyek.id === proyekId);
        proyek.comments.push({
          user: { name: 'Logged-in User' },
          text: this.newComment,
        });
        this.newComment = '';
      }
    }
  };
  </script>

  <style lang="scss" scoped>
  .proyek-page {
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
  }
  .social-actions {
    margin-top: 10px;
  }
  button {
    padding: 5px 10px;
    cursor: pointer;
  }
  .comments {
    margin-top: 10px;
  }
  .comment {
    margin-bottom: 5px;
  }
  input {
    width: 100%;
    padding: 5px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
  </style>
