<template>
  <NavbarHome />

  <div class="login-container">
    <div class="login-box">
      <h2>Recuperar Senha</h2>

      <form @submit.prevent="sendEmail">
        <input 
          type="email" 
          v-model="email" 
          placeholder="Digite seu email"
          required
          class="form-control"
        />

        <button class="btn-primary w-100" :disabled="loading">
          {{ loading ? "Enviando..." : "Enviar link" }}
        </button>

        <p v-if="message" class="success">{{ message }}</p>
        <p v-if="error" class="error">{{ error }}</p>
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
      loading: false,
      message: "",
      error: ""
    };
  },
  methods: {
    async sendEmail() {
      this.loading = true;
      this.error = "";
      this.message = "";

      try {
        const res = await axios.post("/forgot-password", { email: this.email });
        this.message = res.data.message;
      } catch (e) {
        this.error = "Email não encontrado.";
      }

      this.loading = false;
    }
  }
};
</script>

<style scoped>
.success { color: green; margin-top: 10px; }
.error { color: red; margin-top: 10px; }
</style>
