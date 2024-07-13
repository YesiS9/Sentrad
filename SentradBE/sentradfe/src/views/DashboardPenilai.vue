<template>
    <Sidebar />
    <main class="dashboard-page">
        <h1>Dashboard, penilai</h1>
    </main>
</template>

<script>
  import Sidebar from '../components/SidebarPenilai.vue';

  export default {
    components: {
      Sidebar
    }
  }
</script>

<style scoped lang="scss">
  .dashboard-container {
    display: flex;
  }

  .dashboard-page {
    flex: 1;
    background-color: white;
    padding: 2rem; /* Add padding for better layout */
  }
</style>
