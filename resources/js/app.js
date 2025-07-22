import './bootstrap'; // Arquivo de configuração do Laravel (opcional)
import 'bootstrap/dist/css/bootstrap.min.css'; 
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '@fortawesome/fontawesome-free/css/all.min.css';



import axios from 'axios'; // Importação do Axios
import { createApp } from 'vue'; // Importação do Vue.js
import App from './App.vue'; // Componente raiz do Vue.js
import router from './router'; // Configuração de rotas do Vue.js

// Configuração global do Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // URL base da API
axios.defaults.headers.common['Accept'] = 'application/json'; // Definir cabeçalho de aceitação de JSON

axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token'); // Obter token armazenado no localStorage
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  });

  

// Criar a instância da aplicação Vue
const app = createApp(App);

// Adicionar o roteador à instância da aplicação
app.use(router);

// Montar a aplicação no elemento HTML com id "app"
app.mount('#app');
