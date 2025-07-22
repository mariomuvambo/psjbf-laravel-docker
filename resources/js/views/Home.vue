<template>
  <div class="home-container">
    <NavbarHome />
    <div class="image-wrapper">
      <img src="../assets/images/img-1.jpg" alt="Bem-vindo" class="fullscreen-image" />
    </div>
  </div>
</template>

<script>
import NavbarHome from "../components/NavbarHome.vue";

export default {
  name: "Home",
  components: {
    NavbarHome,
  },
};
</script>

<style scoped>
.home-container {
  height: 100vh;
  width: 100vw;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background-color: #ffffff;
}

/* Wrapper com borda branca */
.image-wrapper {
  flex: 1;
  padding: 8px;
  box-sizing: border-box;
  background-color: #ffffff;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Imagem com borda */
.fullscreen-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  border: 8px solid #ffffff;
  box-sizing: border-box;
  border-radius: 12px;
}
</style>
