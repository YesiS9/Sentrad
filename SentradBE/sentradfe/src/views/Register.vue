<template>
  <main>
    <div class="auth-container">
      <div class="auth-form">
        <img :src="logoURL" alt="Sentrad Logo" class="logo" />
        <h2>Sign Up</h2>
        <input v-model="username" type="text" placeholder="Username" />
        <input v-model="email" type="email" placeholder="Email" />
        <input v-model="password" type="password" placeholder="Password" />
        <input v-model="password_confirmation" type="password" placeholder="Confirm Password" required />
        <button @click="register">Sign Up</button>
        <a href="#" @click.prevent="goToHome">Back Home</a>
      </div>
    </div>
  </main>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';
import logo from '../assets/Sentradlogo.png';

export default {
  name: 'Register',
  data() {
    return {
      username: '',
      email: '',
      password: '',
      password_confirmation: '',
      logoURL: logo,
    };
  },
  methods: {
    async register() {
      const toast = useToast();
      try {
        const response = await axios.post('http://localhost:8000/api/register', {
          username: this.username,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation,
        });

        if (response.status === 200 || response.status === 201) {
          toast.success(response.data.message);
          localStorage.setItem('token', response.data.token);
          localStorage.setItem('user_id', response.data.data.id);
          console.log(response.data);

          this.$router.push('/seniman');
        } else {
          toast.error('Registration failed: ' + response.data.message);
        }

      } catch (error) {
        if (error.response) {
          toast.error('Server responded with: ' + error.response.status + ' ' + error.response.data.message);
        } else if (error.request) {
          toast.error('No response received: ' + error.request);
        } else {
          toast.error('Error: ' + error.message);
        }
      }
    },
    goToHome() {
      this.$router.push('/');
    },
  },
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
  width: 85vw;
  height: 85vw;
  max-width: 450px;
  max-height: 450px;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.logo {
  width: 100px;
  margin-bottom: 1rem;
}

select, input {
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
</style>
