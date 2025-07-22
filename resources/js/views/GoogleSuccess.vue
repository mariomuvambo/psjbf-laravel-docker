<template>
  <div class="redirecting">
    <p>Redirecionando...</p>
  </div>
</template>

<script>
export default {
  mounted() {
    const token = new URLSearchParams(window.location.search).get("token");

    if (token) {
      localStorage.setItem("token", token);
      // Opcional: pode buscar usuário com o token via API e guardar
      this.$router.push("/dashboard");
    } else {
      this.$router.push("/login");
    }
  },
};
</script>

<style scoped>
.redirecting {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  font-size: 1.2rem;
  color: #8b0000;
}
</style>
