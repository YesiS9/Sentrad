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

        <div class="map-container">
          <h2>Sanggar-Sanggar Seniman</h2>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Cari nama Sanggar..."
            @input="filterArtists"
            class="search-input"
          />
          <div id="map"></div>
        </div>
      </div>
    </main>
  </template>

  <script>
  import L from 'leaflet';
  import axios from '../services/api.js';
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
        map: null,
        artists: [],
        searchQuery: '',
        filteredArtists: [],
      };
    },
    mounted() {
      this.fetchArtists();
      this.initializeMap();
    },
    methods: {
        async fetchArtists() {
            try {
                const response = await axios.get("/map-sanggar");

                if (response.data && Array.isArray(response.data.data)) {
                this.artists = response.data.data;
                this.filteredArtists = this.artists;
                this.updateMarkers();
                } else {
                console.error('Unexpected API response format:', response.data);
                }
            } catch (error) {
                console.error('Error fetching artist data:', error.message);
            }
            },
        updateMarkers() {
        if (!this.map) {
            console.error('Map is not initialized');
            return;
        }

        this.map.eachLayer((layer) => {
            if (layer instanceof L.Marker) {
            this.map.removeLayer(layer);
            }
        });


        this.filteredArtists.forEach((artist) => {
            if (artist.latitude && artist.longitude) {
            const popupContent = `
                <b>${artist.name}</b><br />
                <i>${artist.description || 'Deskripsi tidak tersedia'}</i><br />
                <b>Seniman:</b> ${artist.seniman?.nama_seniman || 'Tidak diketahui'}
            `;
            L.marker([artist.latitude, artist.longitude])
                .addTo(this.map)
                .bindPopup(popupContent);
            } else {
            console.warn('Artist data is missing latitude or longitude:', artist);
            }
        });
        },
      initializeMap() {
        this.map = L.map('map').setView([-7.797068, 110.370529], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
        }).addTo(this.map);
      },
      updateMarkers() {
        this.map.eachLayer((layer) => {
          if (layer instanceof L.Marker) {
            this.map.removeLayer(layer);
          }
        });

        this.filteredArtists.forEach((artist) => {
          const popupContent = `
            <b>${artist.name}</b><br />
            <i>${artist.description}</i><br />
            <b>Seniman:</b> ${artist.seniman.nama_seniman}
          `;
          L.marker([artist.latitude, artist.longitude])
            .addTo(this.map)
            .bindPopup(popupContent);
        });
      },
      filterArtists() {
        this.filteredArtists = this.artists.filter((artist) =>
          artist.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
        this.updateMarkers();
      },
      navigateTo(route) {
        this.$router.push(route);
      },
    },
  };
  </script>

  <style lang="scss" scoped>
  .home-page {
    display: flex;
    justify-content: center;
    height: 100%;
    background-color: #f7941e;
    padding: 20px;
  }

  .homepage-container {
    text-align: center;
    background-color: white;
    padding: 3em;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 1200px;
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
    margin-bottom: 30px;
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
    margin-bottom: 30px;
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

  .map-container {
    margin-top: 40px;
    position: relative;
  }

  h2 {
    text-align: left;
    margin-bottom: 10px;
    font-size: 1.5em;
    color: #333;
  }

  #map {
    height: 400px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  .search-input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  </style>
