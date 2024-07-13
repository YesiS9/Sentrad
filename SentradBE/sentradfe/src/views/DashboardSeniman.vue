<template>
    <Sidebar />
    <main class="dashboard-page">
        <h1>Dashboard</h1>
    </main>
</template>

<script>
  import Sidebar from '../components/SidebarSeniman.vue';

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
