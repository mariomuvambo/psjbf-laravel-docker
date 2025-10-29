<template>
  <NavbarHome />

  <div class="login-container d-flex justify-content-center align-items-center">
    <div class="login-box p-4 shadow rounded">
      <h1 class="login-title text-center mb-4">Login</h1>

      <form @submit.prevent="login">
        <!-- Email -->
        <div class="mb-3">
          <input 
            type="email" 
            v-model="email" 
            placeholder="Email" 
            class="form-control" 
            required
          />
        </div>

        <!-- Senha -->
        <div class="mb-3">
          <input 
            type="password" 
            v-model="password" 
            placeholder="Senha" 
            class="form-control" 
            required
          />
        </div>

        <!-- Botão Login -->
        <button type="submit" class="btn btn-primary w-100 mb-2">
          Entrar
        </button>

        <!-- Login com Google -->
        <button type="button" class="btn btn-outline-dark w-100 mb-3 d-flex align-items-center justify-content-center" @click="loginWithGoogle">
          <img src="https://img.icons8.com/color/24/google-logo.png" alt="Google" class="me-2"/>
          Entrar com Google
        </button>

        <!-- Mensagem de Erro -->
        <p v-if="errorMessage" class="text-danger text-center">{{ errorMessage }}</p>

        <!-- Recuperar Senha -->
        <router-link to="/recuperar-senha" class="d-block text-center mt-3 text-decoration-underline text-primary">
          Esqueceu a senha?
        </router-link>
      </form>
    </div>
  </div>
</template>

<script>
import NavbarHome from "../components/NavbarHome.vue";

export default {
  components: { NavbarHome },
  data() {
    return {
      email: '',
      password: '',
      errorMessage: ''
    };
  },
  methods: {
    async login() {
      this.errorMessage = '';
      try {
        const { data } = await this.$axios.post('/login', {
          email: this.email,
          password: this.password
        });

        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        this.$router.push('/dashboard');
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Credenciais inválidas. Tente novamente.';
      }
    },
    loginWithGoogle() {
      window.location.href = `${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/auth/google`;
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  background-color: #8b0000;
  padding: 2rem;
}

.login-box {
  background-color: #fff;
  max-width: 380px;
  width: 100%;
  border-radius: 16px;
}

.login-title {
  color: #8b0000;
  font-family: 'Segoe UI', sans-serif;
}

.btn-primary {
  background-color: #8b0000;
  border: none;
  font-weight: 600;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: #600000;
}

.btn-outline-dark {
  border-color: #8b0000;
  color: #8b0000;
}

.btn-outline-dark:hover {
  background-color: #8b0000;
  color: #fff;
}

.text-danger {
  font-size: 0.9rem;
}
</style>
