import './bootstrap'; // Arquivo de configuração do Laravel (opcional)
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

import axios from 'axios';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

console.log('API URL carregada:', import.meta.env.VITE_API_URL);

// 🧠 Define a baseURL dinamicamente
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api';
axios.defaults.headers.common['Accept'] = 'application/json';

// 🔒 Intercepta requests para adicionar token JWT, se existir
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// 📦 Torna o axios acessível globalmente no Vue
const app = createApp(App);
app.config.globalProperties.$axios = axios;

// 🔗 Usa o roteador e monta a aplicação
app.use(router);
app.mount('#app');
