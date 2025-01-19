import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
});

// Add Authorization header if API key exists in cookies
api.interceptors.request.use(config => {
  const apiKey = document.cookie.split('; ').find(row => row.startsWith('api_key='))?.split('=')[1];

  if (apiKey) {
    config.headers.Authorization = apiKey;  // i forget add Bearer tooken in backend 
  }

  return config;
});

export default api;
