<template>
    <div class="page">
        <Sidebar />
        <main class="main-content">
        <div class="add-location-card">
            <li>
            <button @click="goBack" class="btn-back">Kembali</button>
            </li>
            <h3>Tambah Lokasi</h3>
            <input
            type="text"
            v-model="locationData.name"
            placeholder="Masukkan nama lokasi"
            class="location-input"
            />
            <textarea
            v-model="locationData.description"
            placeholder="Masukkan deskripsi lokasi"
            class="location-input"
            ></textarea>
            <button @click="confirmLocation" class="save-location-btn" :disabled="!locationConfirmed">
            Simpan Lokasi
            </button>
            <button class="geo-btn" @click="useGeolocation">Gunakan Lokasi Saat Ini</button>
        </div>

        <div class="location-card">
            <h3>Pilih Lokasi</h3>
            <div id="map" ref="map" class="map-view"></div>
        </div>
        </main>
    </div>
</template>

<script>
  import axios from "../services/api.js";
  import Sidebar from "../components/SidebarSeniman.vue";
  import L from "leaflet";
  import Swal from "sweetalert2";

  export default {
    name: "MapPage",
    components: { Sidebar },
    data() {
      return {
        map: null,
        locationData: {
          name: "",
          description: "",
          latitude: null,
          longitude: null,
          seniman_id: null, // Tambahkan atribut seniman_id
        },
        locationConfirmed: false,
      };
    },
    mounted() {
      this.initializeMap();
      const storedSenimanId = localStorage.getItem("seniman_id");
      if (storedSenimanId) {
        this.locationData.seniman_id = storedSenimanId;
      } else {
        Swal.fire("Error", "Seniman ID tidak ditemukan di localStorage.", "error");
      }
    },
    methods: {
      initializeMap() {
        this.map = L.map(this.$refs.map).setView([-7.7956, 110.3695], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          maxZoom: 19,
          attribution: "© OpenStreetMap contributors",
        }).addTo(this.map);

        this.map.on("click", (e) => {
          this.locationData.latitude = e.latlng.lat;
          this.locationData.longitude = e.latlng.lng;
          this.locationConfirmed = true;

          this.clearMarkers();

          L.marker([e.latlng.lat, e.latlng.lng])
            .addTo(this.map)
            .bindPopup("Lokasi baru ditetapkan")
            .openPopup();
        });
      },
      clearMarkers() {
        this.map.eachLayer((layer) => {
          if (layer instanceof L.Marker) {
            this.map.removeLayer(layer);
          }
        });
      },
      async useGeolocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition((position) => {
            this.locationData.latitude = position.coords.latitude;
            this.locationData.longitude = position.coords.longitude;
            this.locationConfirmed = true;

            this.clearMarkers();

            L.marker([position.coords.latitude, position.coords.longitude])
              .addTo(this.map)
              .bindPopup("Lokasi saat ini")
              .openPopup();
          });
        } else {
          Swal.fire("Error", "Geolocation tidak didukung oleh browser Anda.", "error");
        }
      },
      async confirmLocation() {
        if (!this.locationData.name || !this.locationData.description) {
          Swal.fire("Error", "Nama dan deskripsi lokasi wajib diisi.", "error");
          return;
        }

        try {
          const response = await axios.post("/map", {
            name: this.locationData.name,
            description: this.locationData.description,
            latitude: this.locationData.latitude,
            longitude: this.locationData.longitude,
            seniman_id: this.locationData.seniman_id,
          });

          Swal.fire("Sukses", "Lokasi berhasil disimpan.", "success");
          console.log("Saved location:", response.data);
          this.resetLocationForm();
        } catch (error) {
          Swal.fire("Error", "Gagal menyimpan lokasi.", "error");
          console.error("Error saving location:", error);
        }
      },
      resetLocationForm() {
        this.locationData = {
          name: "",
          description: "",
          latitude: null,
          longitude: null,
          seniman_id: localStorage.getItem("seniman_id"), // Tetap ambil dari localStorage
        };
        this.locationConfirmed = false;
        this.clearMarkers();
      },
      goBack() {
        this.$router.go(-1);
      },
    },
  };
  </script>

<style lang="scss"  scoped>
.page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
  .location-container {
    display: flex;
  }
  .btn-back {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    cursor: pointer;
  }
  .main-content {
    width: 80%;
    padding: 1rem;
  }
  .add-location-card,
  .location-card {
    margin-bottom: 1.5rem;
  }
  .map-view {
    width: 100%;
    height: 400px;
    border-radius: 8px;
  }
  .location-input {
    width: 100%;
    margin-bottom: 1rem;
    padding: 0.5rem;
  }
  .save-location-btn,
  .geo-btn {
    margin-top: 1rem;
    display: block;
  }
  </style>
