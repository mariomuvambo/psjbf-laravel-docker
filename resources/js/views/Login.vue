<template>
  <NavbarHome />
  
  <div class="login-container">
    <div class="login-box">
      <h1 class="login-title">Login</h1>
      
      <form @submit.prevent="login" class="login-form">
        <div class="form-group">
          <input 
            type="email" 
            v-model="email" 
            placeholder="Email" 
            class="form-control" 
            required 
          />
        </div>

        <div class="form-group">
          <input 
            type="password" 
            v-model="password" 
            placeholder="Senha" 
            class="form-control" 
            required 
          />
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
        
        <button type="button" class="btn btn-google w-100" @click="loginWithGoogle">
        <img 
          src="https://img.icons8.com/color/24/google-logo.png" 
          alt="Google" 
          class="google-icon"
        />
        </button>

        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>

        <router-link to="/recuperar-senha" class="forgot-password">
          Esqueceu a senha?
        </router-link>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavbarHome from "../components/NavbarHome.vue";

export default {
  components: { NavbarHome },
  data() {
    return {
      email: "",
      password: "",
      errorMessage: "",
    };
  },
  methods: {
    async login() {
      try {
        const response = await axios.post("/login", {
          email: this.email,
          password: this.password,
        });

        const { token, user } = response.data;
        localStorage.setItem("auth_token", token);
        localStorage.setItem("user", JSON.stringify(user));
        this.$router.push("/dashboard");
      } catch (error) {
        this.errorMessage = "Credenciais inválidas. Tente novamente.";
      }
    },
    loginWithGoogle() {
      window.location.href = "http://localhost:8000/api/auth/google";
    },
  },
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem 1rem;
  min-height: 100vh;
  background-color: #8b0000;
}

.login-box {
  background: #fff;
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
  width: 100%;
  max-width: 380px;
  text-align: center;
}

.login-title {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #8b0000;
  font-family: 'Segoe UI', sans-serif;
}

.form-group {
  margin-bottom: 1.2rem;
}

.form-control {
  width: 100%;
  padding: 0.9rem;
  font-size: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  transition: border-color 0.3s;
}

.form-control:focus {
  border-color: #8b0000;
  outline: none;
}

.btn-primary {
  background-color: #8b0000;
  color: #fff;
  border: none;
  padding: 0.9rem;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 1rem;
  transition: background-color 0.3s;
  cursor: pointer;
}

.btn-primary:hover {
  background-color: #600000;
}

.btn-google {
  background-color: #8b0000;
  color: #fff;
  padding: 0.9rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  transition: background-color 0.2s ease;
  cursor: pointer;
}

.google-icon {
  width: 20px;
  height: 20px;
}

.btn-google:hover {
  background-color: #f3f3f3;
}

.error-message {
  color: red;
  margin-top: 0.5rem;
  font-size: 0.9rem;
}

.forgot-password {
  display: block;
  margin-top: 1.2rem;
  color: #8b0000;
  font-size: 0.9rem;
  text-decoration: underline;
  transition: color 0.2s ease;
}

.forgot-password:hover {
  color: #600000;
}
</style>
