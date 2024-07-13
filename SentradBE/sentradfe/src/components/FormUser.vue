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
                    <div v-if="mode === 'add'" class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" v-model="formData.password" placeholder="Password" required>
                    </div>
                    <div v-if="mode === 'edit'" class="form-group">
                        <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" v-model="formData.new_password" placeholder="Password">
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
    id: '',
    username: '',
    email: '',
    password: '',
    new_password: '',
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
                nama_role: userData.nama_role
            });
            mode.value = 'edit';
            console.log('User data:', formData); // Add logging here
        } else {
            console.error('Failed to fetch user:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching user:', error.message);
    }
};

onMounted(async () => {
    await getRoles(); // Fetch roles when component mounts

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
        console.log('Form Data:', formData); // Add logging here
        console.log('mode:', mode);
        let response;
        if (mode.value === 'add') {
            response = await axios.post('/user/store-byAdmin', formData);
        } else if (mode.value === 'edit') {
            const payload = {
                username: formData.username,
                email: formData.email,
                nama_role: formData.nama_role,
            };
            if (formData.password) {
                payload.password = formData.password;
            }
            response = await axios.put(`/user/${formData.id}`, payload);
        } else {
            console.error('Invalid mode:', mode.value);
            return;
        }

        if (response.status === 200 && response.data.status === 'success') {
            if (mode.value === 'add') {
                console.log('Adding user:', response.data.data);
            } else {
                console.log('Editing user:', response.data.data);
            }
            router.push({ name: 'DataUser' });
            closeForm();
        } else {
            console.log(mode.value);
            console.error(mode.value === 'add' ? 'Failed to add user:' : 'Failed to edit user:', response.data.message);
        }
    } catch (error) {
        console.error('Error saving data:', error.message);
    }
};

const closeForm = () => {
    formData.id = '';
    formData.username = '';
    formData.email = '';
    formData.password = '';
    formData.new_password = '';
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
    height: 90vh;
    max-width: 400px;
    max-height: 450px;
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
        width: 100%;
        margin-bottom: 1rem;
        text-align: left;

        label {
            display: block;
            margin-bottom: 0.5rem;
        }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        select {
            height: auto;
            min-height: 2.5rem;
        }
    }

    .form-actions {
        margin-top: 1rem;
        text-align: right;
        width: 100%;

        button {
            margin-left: 0.5rem;
            background-color: #f7941e;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;

            &:first-child {
                margin-left: 0;
            }

            &:hover {
                background-color: #e78310;
            }
        }
    }
}
</style>
