<template>
    <div class="reset-container">
      <div class="reset-box">
        <h2 class="title">Nova Senha</h2>
        <form @submit.prevent="resetPassword">
          <input v-model="email" type="email" placeholder="Email" class="form-control" required />
          <input v-model="password" type="password" placeholder="Nova senha" class="form-control" required />
          <input v-model="password_confirmation" type="password" placeholder="Confirmar senha" class="form-control" required />
          <button type="submit" class="btn btn-primary w-100">Redefinir Senha</button>
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
        password: '',
        password_confirmation: '',
        token: '',
        message: '',
        error: '',
      };
    },
    mounted() {
      this.token = this.$route.query.token || '';
      this.email = this.$route.query.email || '';
    },
    methods: {
      async resetPassword() {
        this.message = '';
        this.error = '';
        try {
          const res = await axios.post('/api/reset-password', {
            token: this.token,
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
          });
          this.message = res.data.message;
          // Redireciona para o login após sucesso
          setTimeout(() => this.$router.push('/login'), 2000);
        } catch (err) {
          this.error = err.response?.data?.message || 'Erro ao redefinir a senha.';
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .reset-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #8b0000;
  }
  
  .reset-box {
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
  