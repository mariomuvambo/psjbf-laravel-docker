<template>
    <div class="forgot-container">
      <div class="forgot-box">
        <h2 class="title">Recuperar Senha</h2>
        <form @submit.prevent="sendResetLink">
          <input 
            v-model="email" 
            type="email" 
            placeholder="Digite seu email" 
            class="form-control" 
            required 
          />
          <button type="submit" class="btn btn-primary w-100">Enviar Link</button>
          <p v-if="message" class="success">{{ message }}</p>
          <p v-if="error" class="error">{{ error }}</p>
        </form>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        email: '',
        message: '',
        error: ''
      };
    },
    methods: {
      async sendResetLink() {
        this.message = '';
        this.error = '';
        try {
          const res = await axios.post('/api/forgot-password', { email: this.email });
          this.message = res.data.message;
        } catch (err) {
          this.error = err.response?.data?.message || "Erro ao enviar link.";
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .forgot-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #8b0000;
  }
  
  .forgot-box {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    max-width: 400px;
    width: 100%;
    text-align: center;
  }
  
  .title {
    margin-bottom: 1rem;
    color: #8b0000;
    font-size: 1.5rem;
  }
  
  .success {
    color: green;
  }
  
  .error {
    color: red;
  }
  
  .form-control {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1rem;
  }
  </style>
  