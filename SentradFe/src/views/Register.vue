<template>
    <main>
      <div class="auth-container">
        <div class="auth-form">
          <img :src="logoURL" alt="Sentrad Logo" class="logo" />
          <h2>Sign Up</h2>
          <div class="form-group">
            <label for="username">Username</label>
            <input id="username" v-model="username" type="text" placeholder="Enter your username" />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input id="email" v-model="email" type="email" placeholder="Enter your email" />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input id="password" v-model="password" type="password" placeholder="Enter your password" />
          </div>
          <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" v-model="password_confirmation" type="password" placeholder="Confirm your password" />
          </div>
          <div class="form-group">
            <label for="foto">Foto Profil (Optional)</label>
            <input id="foto" type="file" @change="handleFileChange" accept="image/*" />
            <small v-if="errors.photo.length" class="error-text">{{ errors.photo.join(', ') }}</small>
          </div>
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
        photo: null, // Adjusted to null to match the second example
        logoURL: logo,
        errors: {
          photo: [],
        },
      };
    },
    methods: {
      handleFileChange(event) {
        const file = event.target.files[0];
        this.photo = file ? file : null;
        console.log('File selected:', this.photo);
      },
      async register() {
        const toast = useToast();
        try {
          let formData = new FormData();
          formData.append('username', this.username);
          formData.append('email', this.email);
          formData.append('password', this.password);
          formData.append('password_confirmation', this.password_confirmation);

          if (this.photo) {
            formData.append('photo', this.photo);
          }

          const response = await axios.post('http://sentrad-production-2d25.up.railway.app/api/register', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

          if (response.status === 200 || response.status === 201) {
            toast.success(response.data.message);
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user_id', response.data.data.id);
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
    max-height: 600px;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
  }
  .logo {
    width: 100px;
    margin-bottom: 1rem;
  }
  .form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 1rem;
    width: 100%;
  }
  label {
    margin-bottom: 0.5rem;
    font-weight: bold;
    font-size: 0.9rem;
  }
  input {
    width: 100%;
    padding: 0.5rem;
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
