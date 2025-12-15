<template>
  <NavbarHome />

  <div class="login-container">
    <div class="login-box">
      <h2>Redefinir Senha</h2>

      <form @submit.prevent="resetPassword">
        <input type="password" v-model="password" placeholder="Nova senha" required class="form-control" />
        <input type="password" v-model="password_confirmation" placeholder="Confirmar senha" required class="form-control" />

        <button class="btn-primary w-100" :disabled="loading">
          {{ loading ? "Salvando..." : "Redefinir" }}
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
      password: "",
      password_confirmation: "",
      email: "",
      token: "",
      loading: false,
      message: "",
      error: ""
    };
  },
  mounted() {
    const params = new URLSearchParams(window.location.search);
    this.token = params.get("token");
    this.email = params.get("email");
  },
  methods: {
    async resetPassword() {
      this.loading = true;
      this.error = "";
      this.message = "";

      try {
        const res = await axios.post("/reset-password", {
          token: this.token,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });

        this.message = res.data.message;

        setTimeout(() => this.$router.push("/login"), 2000);

      } catch (e) {
        this.error = "Erro ao redefinir senha.";
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
