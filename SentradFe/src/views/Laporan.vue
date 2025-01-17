<template>
    <div class="laporan-page">
        <Sidebar />
        <main class="data-penilai">
            <div class="user-management-container">
                <header class="header">
                    <h2>Welcome, admin</h2>
                </header>
                <div class="menu-wrapper">
                    <h3>Laporan Data</h3>

                    <div class="button-group">
                        <button @click="previewUserData" class="button">
                            <span class="material-icons">visibility</span> Preview Data User
                        </button>
                        <button @click="downloadUserData" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                    <div class="button-group">
                        <button @click="previewRegistrasiData" class="button">
                            <span class="material-icons">visibility</span> Preview Data Registrasi
                        </button>
                        <button @click="downloadRegistrasiData" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                    <div class="button-group">
                        <button @click="previewPenilaianData" class="button">
                            <span class="material-icons">visibility</span> Preview Data Penilaian Karya
                        </button>
                        <button @click="downloadPenilaianData" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                    <div class="button-group">
                        <button @click="previewKategoriSeni" class="button">
                            <span class="material-icons">visibility</span> Preview Data Kategori Seni
                        </button>
                        <button @click="downloadKategoriSeni" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                    <div class="button-group">
                        <button @click="previewSenimanData" class="button">
                            <span class="material-icons">visibility</span> Preview Data Seniman
                        </button>
                        <button @click="downloadSenimanData" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                    <div class="button-group">
                        <button @click="previewPenilaiData" class="button">
                            <span class="material-icons">visibility</span> Preview Data Penilai
                        </button>
                        <button @click="downloadPenilaiData" class="button-download">
                            <span class="material-icons">save_alt</span> Download
                        </button>
                    </div>

                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../services/api.js';
import Sidebar from '../components/SidebarAdmin.vue';

const previewUserData = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=user', { responseType: 'blob' });
        openFileInNewTab(response.data, 'data_user.pdf');
    } catch (error) {
        console.error('Error previewing User Data:', error);
    }
};

const downloadUserData = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=user', { responseType: 'blob' });
        downloadFile(response.data, 'data_user.pdf');
    } catch (error) {
        console.error('Error downloading User Data:', error);
    }
};

const previewRegistrasiData = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=registrasi', { responseType: 'blob' });
        openFileInNewTab(response.data, 'data_registrasi.pdf');
    } catch (error) {
        console.error('Error previewing Registrasi Data:', error);
    }
};

const downloadRegistrasiData = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=registrasi', { responseType: 'blob' });
        downloadFile(response.data, 'data_registrasi.pdf');
    } catch (error) {
        console.error('Error downloading Registrasi Data:', error);
    }
};

const previewKategoriSeni = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=kategori', { responseType: 'blob' });
        openFileInNewTab(response.data, 'kategori_seni_data.pdf');
    } catch (error) {
        console.error('Error previewing Kategori Seni Data:', error);
    }
};

const downloadKategoriSeni = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=kategori', { responseType: 'blob' });
        downloadFile(response.data, 'kategori_seni_data.pdf');
    } catch (error) {
        console.error('Error downloading Kategori Seni Data:', error);
    }
};

const previewSenimanData = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=seniman', { responseType: 'blob' });
        openFileInNewTab(response.data, 'seniman_data.pdf');
    } catch (error) {
        console.error('Error previewing Seniman Data:', error);
    }
};

const downloadSenimanData = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=seniman', { responseType: 'blob' });
        downloadFile(response.data, 'seniman_data.pdf');
    } catch (error) {
        console.error('Error downloading Seniman Data:', error);
    }
};

const previewPenilaianData = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=penilaian', { responseType: 'blob' });
        openFileInNewTab(response.data, 'penilaian_data.pdf');
    } catch (error) {
        console.error('Error previewing Penilaian Karya Data:', error);
    }
};

const downloadPenilaianData = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=penilaian', { responseType: 'blob' });
        downloadFile(response.data, 'penilaian_data.pdf');
    } catch (error) {
        console.error('Error downloading Penilaian Karya Data:', error);
    }
};

const previewPenilaiData = async () => {
    try {
        const response = await axios.get('/laporan/preview-pdf?type=penilai', { responseType: 'blob' });
        openFileInNewTab(response.data, 'penilai_data.pdf');
    } catch (error) {
        console.error('Error previewing Penilai Data:', error);
    }
};

const downloadPenilaiData = async () => {
    try {
        const response = await axios.get('/laporan/download-pdf?type=penilai', { responseType: 'blob' });
        downloadFile(response.data, 'penilai_data.pdf');
    } catch (error) {
        console.error('Error downloading Penilai Data:', error);
    }
};



const openFileInNewTab = (data, filename) => {
    const url = window.URL.createObjectURL(new Blob([data], { type: 'application/pdf' }));
    const newTab = window.open(url, '_blank');
    newTab.focus();
};

const downloadFile = (data, filename) => {
    const url = window.URL.createObjectURL(new Blob([data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        alert('Please login first.');
        return;
    }
});
</script>

<style lang="scss" scoped>
.laporan-page {
  display: flex;
  flex: 1;
  background-color: #f2d395;
  color: #333;
}
.data-penilai {
    background-color: #f5d99d;

    .user-management-container {
        background-color: #f5d99d;
        padding: 2rem;
    }

    .header {
        margin-bottom: 1rem;

        h2 {
            color: #000;
        }
    }

    .menu-wrapper {
        background-color: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        gap: 1rem;

        h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .button-group {
            display: flex;
            align-items: center;
            gap: 5rem;

            .button {
                background-color: #fff;
                color: #000;
                border: 1px solid #000;
                padding: 0.75rem 1.5rem;
                border-radius: 4px;
                cursor: pointer;
                display: flex;
                align-items: center;
                width: 900px;

                &:hover {
                    background-color: #f0f0f0;
                }

                .material-icons {
                    margin-right: 0.5rem;
                    color: #000;
                }
            }

            .button-download {
                background-color: #f7941e;
                color: #ffffff;
                border: 1px solid #fff;
                padding: 0.75rem 1rem;
                border-radius: 4px;
                cursor: pointer;
                display: flex;
                align-items: center;
                width: 150px;

                &:hover {
                    background-color: #f7941e;
                }

                .material-icons {
                    margin-right: 0.5rem;
                    color: #ffffff;
                }
            }
        }
    }
}
</style>
