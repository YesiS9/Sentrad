<template>
    <main>
        <div class="auth-container">
            <div class="auth-form">
                <img :src="logoURL" alt="Sentrad Logo" class="logo" />
                <h2>Login Form</h2>
                <input v-model="email" type="email" placeholder="Email" />
                <input v-model="password" type="password" placeholder="Password" />
                <button @click="login">Login</button>
                <a href="#" @click.prevent="goToRegister">Register</a>
            </div>
        </div>
    </main>
</template>

<script>
import axios from '../services/api.js';
import logo from '../assets/Sentradlogo.png';

export default {
    name: 'Login',
    data() {
        return {
            email: '',
            password: '',
            logoURL: logo
        };
    },
    methods: {
        async login() {
            try {
                const response = await axios.post('http://localhost:8000/api/login', {
                    email: this.email,
                    password: this.password
                });

                const { data, status, message } = response.data;

                if (status === 'success') {
                    const { user, role, token } = data;
                    console.log('Login response:', user, role, token);

                    localStorage.setItem('token', token);
                    localStorage.setItem('user_id', user.id); // Simpan user_id di local storage

                    let roleName = '';
                    if (role && typeof role === 'object' && role.nama_role) {
                        roleName = role.nama_role.toLowerCase();
                    } else {
                        console.error('Error logging in:', 'Invalid role object');
                        return;
                    }

                    if (roleName === 'seniman') {
                        this.$router.push('/dashboardSeniman');
                    } else if (roleName === 'penilai') {
                        this.$router.push('/dashboardPenilai');
                    } else if (roleName === 'admin') {
                        this.$router.push('/dashboardAdmin');
                    } else {
                        console.error('Error logging in:', 'Invalid role:', roleName);
                    }

                } else {
                    console.error('Error logging in:', message);
                }
            } catch (error) {
                console.error('Error logging in:', error.message);
            }
        },
        goToRegister() {
            this.$router.push('/register');
        }
    }
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
    width: 80vw;
    height: 80vw;
    max-width: 400px;
    max-height: 400px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    .logo {
        width: 100px;
        margin-bottom: 1rem;
    }

    input {
        display: block;
        width: 100%;
        padding: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #f7941e;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #e6830d;
    }

    a {
        display: block;
        margin-top: 1rem;
        color: #f7941e;
        text-decoration: none;
        cursor: pointer;
    }
}
</style>
