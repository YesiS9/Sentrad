<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
            <h3>Input Anggota Kelompok</h3>
            <p>Jumlah anggota yang perlu diinput: {{ jumlahAnggota }}</p>
            <p>Perhatian: Pendaftar tidak akan secara otomatis terdaftar sebagai anggota kelompok. Semua anggota, termasuk pendaftar, harus dimasukkan secara manual pada form ini.</p>
          <form @submit.prevent="handleSubmit">
            <div class="anggota-group">
              <h4>Anggota {{ currentAnggotaIndex + 1 }}</h4>
              <div class="form-group">
                <label for="nama_anggota">Nama Anggota</label>
                <input
                  type="text"
                  :id="'nama_anggota_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].nama_anggota"
                  placeholder="Nama Anggota"
                  required
                />
              </div>

              <div class="form-group">
                <label for="tgl_lahir">Tanggal Lahir</label>
                <input
                  type="date"
                  :id="'tgl_lahir_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].tgl_lahir"
                  placeholder="Tanggal Lahir"
                  required
                />
              </div>

              <div class="form-group">
                <label for="tgl_gabung">Tanggal Gabung</label>
                <input
                  type="date"
                  :id="'tgl_gabung_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].tgl_gabung"
                  placeholder="Tanggal Gabung"
                  required
                />
              </div>

              <div class="form-group">
                <label for="alamat_anggota">Alamat</label>
                <input
                  type="text"
                  :id="'alamat_anggota_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].alamat_anggota"
                  placeholder="Alamat"
                  required
                />
              </div>

              <div class="form-group">
                <label for="noTelp_anggota">No. Telp</label>
                <input
                  type="text"
                  :id="'noTelp_anggota_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].noTelp_anggota"
                  placeholder="No. Telp"
                  required
                />
              </div>

              <div class="form-group">
                <label for="tingkat_skill">Tingkat Skill</label>
                <select
                    :id="'tingkat_skill_' + currentAnggotaIndex"
                    v-model="anggotaData[currentAnggotaIndex].tingkat_skill"
                    required
                >
                    <option disabled value="">Pilih Tingkat Skill</option>
                    <option value="Pemula">Pemula</option>
                    <option value="Menengah">Menengah</option>
                    <option value="Lanjutan">Lanjutan</option>
                </select>
            </div>


              <div class="form-group">
                <label for="peran_anggota">Peran Anggota</label>
                <input
                  type="text"
                  :id="'peran_anggota_' + currentAnggotaIndex"
                  v-model="anggotaData[currentAnggotaIndex].peran_anggota"
                  placeholder="Peran Anggota"
                  required
                />
              </div>
              <div class="form-group">
                <label for="status_anggota">Status Anggota</label>
                <select
                    :id="'status_anggota' + currentAnggotaIndex"
                    v-model="anggotaData[currentAnggotaIndex].status_anggota"
                    required
                >
                    <option disabled value="">Pilih Status Anggota</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            </div>

            <div class="form-actions">
              <button v-if="currentAnggotaIndex < jumlahAnggota - 1" type="button" @click="nextAnggota">Selanjutnya</button>
              <button v-else type="submit">Tambah</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </template>

  <script setup>
  import { ref, reactive } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from '../services/api.js';
  import Swal from 'sweetalert2';

  const router = useRouter();
  const jumlahAnggota = ref(parseInt(localStorage.getItem('jumlah_anggota')) || 1);

  const currentAnggotaIndex = ref(0);

  const anggotaData = reactive(
    Array.from({ length: jumlahAnggota.value }, () => ({
      nama_anggota: '',
      tgl_lahir: '',
      tgl_gabung: '',
      alamat_anggota: '',
      noTelp_anggota: '',
      tingkat_skill: '',
      peran_anggota: '',
      status_anggota: '',
    }))
  );

const formatDate = (date) => {
  const [year, month, day] = date.split('-');
  return `${day}/${month}/${year}`;
};

  const nextAnggota = () => {
    if (currentAnggotaIndex.value < jumlahAnggota.value - 1) {
      currentAnggotaIndex.value++;
    }
  };

  const handleSubmit = async () => {
  const result = await Swal.fire({
    title: `Apakah Anda yakin ingin menyimpan data anggota?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya',
    cancelButtonText: 'Tidak',
  });

  if (!result.isConfirmed) {
    return;
  }

  const formattedAnggotaData = anggotaData.map((anggota) => ({
    ...anggota,
    tgl_lahir: formatDate(anggota.tgl_lahir),
    tgl_gabung: formatDate(anggota.tgl_gabung),
  }));

  console.log('Data to be submitted:', {
    kelompok_id: localStorage.getItem('kelompok_id'),
    anggota: formattedAnggotaData,
  });

  try {
    const response = await axios.post('/anggota', {
      kelompok_id: localStorage.getItem('kelompok_id'),
      anggota: formattedAnggotaData,
    });

    if (response.status === 200 && response.data.status === 'success') {
      Swal.fire('Sukses', 'Data anggota berhasil disimpan!', 'success');
      router.push({ name: 'FormPortofolioKelompok' });
    } else {
      console.error('Failed to save anggota:', response.data.message);
    }
  } catch (error) {
    console.error('Error saving anggota:', error.message);
  }
};
  </script>

<style lang="scss" scoped>
  main {
    background-color: #f7941e;
  }

  .auth-container {
    display: flex;
    flex: 1;
    justify-content: center;
    align-items: center;
    height: 120vh;
    background-color: #f7941e;
  }

  .auth-form {
    background-color: #fff;
    width: 90vw;
    height: 90vw;
    max-width: 600px;
    max-height: 800px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    h3 {
      margin-bottom: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
      text-align: left;
      width: 100%;
    }

    .custom-multiselect {
      width: 100%;
    }

    input[type="text"], input[type="date"], select{
      width: 100%;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .form-actions {
      margin-top: 1rem;
      text-align: right;
      width: 100%;
    }

    button {
      background-color: #f7941e;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 0.5rem;
    }

    button[type="submit"] {
      background-color: #45a049;
    }

    button[type="submit"]:hover {
      background-color: #45a049;
    }

    button[type="button"] {
      background-color: #f44336;
    }

    button[type="button"]:hover {
      background-color: #da190b;
    }
  }
</style>
