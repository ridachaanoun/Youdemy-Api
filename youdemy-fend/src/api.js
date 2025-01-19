import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Add Authorization header if token exists in cookies
api.interceptors.request.use(config => {
  const token = document.cookie.split('; ').find(row => row.startsWith('api_key='))?.split('=')[1];

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export default api;
