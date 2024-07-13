// src/services/api.js

import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json'
  }
});

// Add a request interceptor
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  console.log('Using token:', token); // Tambahkan logging untuk memastikan token diambil
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});


api.interceptors.response.use(response => {
  // Check if the response contains a token (for example, after login)
  if (response.data.token) {
    localStorage.setItem('token', response.data.token);
  }
  return response;
}, error => {
  return Promise.reject(error);
});

export const getUserCount = () => api.get('/user/count');
export const getIndividualRegistrationCount = () => api.get('/register-individu/count');
export const getGroupRegistrationCount = () => api.get('/register-kelompok/count');
export const fetchUser = () => api.get('/user'); // Adjust this endpoint if necessary

export default api;
