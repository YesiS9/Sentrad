import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import DashboardAdmin from '../views/DashboardAdmin.vue'
import DataUser from '../views/DataUser.vue'
import DataRegistrasi from '../views/DataRegistrasi.vue'
import DataPenilai from '../views/DataPenilai.vue'
import DataSeni from '../views/DataSeni.vue'
import DataKategori from '../views/DataKategori.vue'
import DataTingkatan from '../views/DataTingkatan.vue'
import FormPenilai from '../components/FormPenilai.vue'
import FormSeni from '../components/FormSeni.vue'
import FormTingkatan from '../components/FormTingkatan.vue'
import FormUser from '../components/FormUser.vue'
import FormIndividu from '../components/FormIndividu.vue'
import FormKelompok from '../components/FormKelompok.vue'
import Laporan from '../views/Laporan.vue'
import DashboardSeniman from '../views/DashboardSeniman.vue'
import DashboardPenilai from '../views/DashboardPenilai.vue'
import Registrasi from '../views/Registrasi.vue'
import Seniman from '../views/Seniman.vue'
import FormKategori from '../components/FormKategori.vue'
import Portofolio from '../views/Portofolio.vue'
import FormPortofolio from '../components/FormPortofolio.vue'
import RegistrasiIndividu from '../components/RegistrasiIndividu.vue'
import RegistrasiKelompok from '../components/RegistrasiKelompok.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
        { path: '/dashboardAdmin', component: DashboardAdmin },
        { path: '/dashboardSeniman', component: DashboardSeniman },
        { path: '/dashboardPenilai', component: DashboardPenilai },
        { path: '/seniman', component: Seniman },
        { path: '/dataUser', name: 'DataUser', component: DataUser },
        { path: '/dataRegistrasi', name: 'DataRegistrasi', component: DataRegistrasi },
        { path: '/dataPenilai', name: 'DataPenilai', component: DataPenilai },
        { path: '/dataSeni', name: 'DataSeni', component: DataSeni },
        { path: '/dataKategori', name: 'DataKategori', component: DataKategori },
        { path: '/dataTingkatan', name: 'DataTingkatan', component: DataTingkatan },
        { path: '/laporan', name: 'Laporan', component: Laporan },
        { path: '/portofolio', name: 'Portofolio', component: Portofolio },
        { path: '/form-penilai', name: 'FormPenilai', component: FormPenilai },
        { path: '/form-penilai/edit/:id', name: 'FormPenilaiEdit', component: FormPenilai, props: true },
        { path: '/form-seni', name: 'FormSeni', component: FormSeni, props: true },
        { path: '/form-seni/edit/:id', name: 'FormSeniEdit', component: FormSeni, props: true },
        { path: '/form-tingkatan', name: 'FormTingkatan', component: FormTingkatan, props: true },
        { path: '/form-tingkatan/edit/:id', name: 'FormTingkatanEdit', component: FormTingkatan, props: true },
        { path: '/form-user', name: 'FormUser', component: FormUser, props: true },
        { path: '/form-user/edit/:id', name: 'FormUserEdit', component: FormUser, props: true },
        { path: '/form-individu', name: 'FormIndividu', component: FormIndividu, props: true },
        { path: '/form-individu/edit/:id', name: 'FormIndividuEdit', component: FormIndividu, props: true },
        { path: '/form-kelompok', name: 'FormKelompok', component: FormKelompok, props: true },
        { path: '/form-kelompok/edit/:id', name: 'FormKelompokEdit', component: FormKelompok, props: true },
        { path: '/form-kategori', name: 'FormKategori', component: FormKategori, props: true },
        { path: '/form-kategori/edit/:id', name: 'FormKategoriEdit', component: FormKategori, props: true },
        { path: '/registrasi-user', name: 'Registrasi', component: Registrasi, props: true },
        { path: '/individu-user', name: 'IndividuAdd', component: RegistrasiIndividu, props: true },
        { path: '/individu-user/edit/:id', name: 'IndividuEdit', component: RegistrasiIndividu, props: true },
        { path: '/kelompok-user', name: 'KelompokAdd', component: RegistrasiKelompok, props: true },
        { path: '/kelompok-user/edit/:id', name: 'KelompokEdit', component: RegistrasiKelompok, props: true },
        { path: '/form-portofolio', name: 'FormPortofolio', component: FormPortofolio, props: true },
        { path: '/form-portofolio/edit/:id', name: 'FormPortofolioEdit', component: FormPortofolio, props: true },
        
        { path: '/:pathMatch(.*)*', redirect: '/' }
    ]
})

export default router
