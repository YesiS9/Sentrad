<template>
    <main class="home-page">
      <div class="homepage-container">
        <img :src="logoURL" alt="Sentrad Logo" class="logo" />
        <h1>Sentrad</h1>
        <div class="gallery">
          <img
            v-for="(image, index) in images"
            :key="index"
            :src="image.src"
            :alt="image.alt"
            class="gallery-image"
          />
        </div>
        <div class="button-container">
          <button @click="navigateTo('/login')" class="btn">Login</button>
          <button @click="navigateTo('/register')" class="btn">Register</button>
        </div>
      </div>
    </main>
  </template>

  <script>
  import logo from '../assets/Sentradlogo.png';
  import pahatImage from '../assets/pahat.jpg';
  import rupaImage from '../assets/rupa.jpg';
  import tariImage from '../assets/Tari.jpg';

  export default {
    name: 'Home',
    data() {
      return {
        logoURL: logo,
        images: [
          { src: pahatImage, alt: 'Traditional Art 1' },
          { src: rupaImage, alt: 'Traditional Art 2' },
          { src: tariImage, alt: 'Traditional Art 3' },
        ],
      };
    },
    methods: {
      navigateTo(route) {
        this.$router.push(route); // Menggunakan route sebagai string, misal '/login'
      },
    },
  };
  </script>

  <style lang="scss" scoped>
  .home-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f7941e;
  }

  .homepage-container {
    text-align: center;
    background-color: white;
    padding: 2em;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .logo {
    width: 200px;
    height: auto;
    margin-bottom: 20px;
  }

  .gallery {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .gallery-image {
    width: 200px;
    height: 200px;
    margin: 5px;
    object-fit: cover;
    border-radius: 5px;
    border: 1px solid #df853b;
  }

  .button-container {
    display: flex;
    justify-content: center;
    gap: 1em;
  }

  .btn {
    padding: 0.8em 1.2em;
    background-color: #f5a840;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }

  .btn:hover {
    background-color: #df853b;
  }
  </style>
