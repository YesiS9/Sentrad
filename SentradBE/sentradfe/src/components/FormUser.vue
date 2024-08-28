<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <h3>{{ mode === 'add' ? 'Tambah User' : 'Edit User' }}</h3>
                <form @submit.prevent="handleSubmit">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" v-model="formData.username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" v-model="formData.email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" v-model="formData.password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role" v-model="formData.nama_role" required>
                            <option value="">Pilih Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.nama_role">{{ role.nama_role }}</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit">{{ mode === 'add' ? 'Tambah' : 'Simpan' }}</button>
                        <button type="button" @click="closeForm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../services/api.js';
import Swal from 'sweetalert2';

const formData = reactive({
    username: '',
    email: '',
    password: '',
    nama_role: '',
});

const roles = ref([]);
const route = useRoute();
const router = useRouter();
const mode = ref('add');

const getRoles = async () => {
    try {
        const response = await axios.get('/roles');
        roles.value = response.data;
    } catch (error) {
        console.error('Error fetching roles:', error.message);
    }
};

const getUser = async (id) => {
    try {
        const response = await axios.get(`/user/${id}`);
        if (response.status === 200 && response.data.status === 'success') {
            const userData = response.data.data;
            Object.assign(formData, {
                id: userData.id,
                username: userData.username,
                email: userData.email,
                nama_role: userData.nama_role,
            });
            mode.value = 'edit';
        } else {
            console.error('Failed to fetch user:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching user:', error.message);
    }
};

onMounted(async () => {
    await getRoles();
    const { id } = route.params;
    if (id) {
        await getUser(id);
    }
});

const handleSubmit = async () => {
    const action = mode.value === 'add' ? 'menambahkan' : 'mengedit';

    const result = await Swal.fire({
        title: `Apakah Anda yakin ingin ${action} user ini?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        let response;
        if (mode.value === 'add') {
            response = await axios.post('/user/store-byAdmin', formData);
        } else if (mode.value === 'edit') {
            response = await axios.put(`/user/${formData.id}`, formData);
        } else {
            console.error('Invalid mode:', mode.value);
            return;
        }

        if (response.status === 200 && response.data.status === 'success') {
            const userId = response.data.data.id;
            localStorage.setItem('id_user', userId);

            router.push({ name: 'DataUser' });
            closeForm();
        } else {
            console.error(mode.value === 'add' ? 'Failed to add user:' : 'Failed to edit user:', response.data.message);
        }
    } catch (error) {
        console.error('Error saving data:', error.message);
    }
};

const closeForm = () => {
    formData.username = '';
    formData.email = '';
    formData.password = '';
    formData.nama_role = '';
    mode.value = 'add';
    router.push({ name: 'DataUser' });
};
</script>

<style lang="scss" scoped>
main {
    background-color: #f7941e;
}
.auth-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f7941e;
}

.auth-form {
    background-color: #fff;
    width: 90vw;
    height: 90vw;
    max-width: 650px;
    max-height: 700px;
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

        label {
        display: block;
        margin-bottom: 0.5rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 35vw;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
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
        background-color: #f7941e;
    }

    button[type="submit"]:hover {
        background-color: #e6871c;
    }

    button[type="button"] {
        background-color: #f44336;
    }

    button[type="button"]:hover {
        background-color: #da190b;
    }
}
</style>
