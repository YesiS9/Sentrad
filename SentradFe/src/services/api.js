// src/services/api.js

import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  console.log('Using token:', token);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  response => {
    if (response.data.token) {
      localStorage.setItem('token', response.data.token);
    }
    return response;
  },
  error => {
    return Promise.reject(error);
  }
);

export const getUnreadNotificationCount = () => api.get('/notifikasi/unread');
export const fetchNotifications = () => api.get('/notifikasi');
export const getUserCount = () => api.get('/user/count');
export const getIndividualRegistrationCount = () => api.get('/register-individu/count');
export const getGroupRegistrationCount = () => api.get('/register-kelompok/count');
export const fetchUser = () => api.get('/user');

export default api;
