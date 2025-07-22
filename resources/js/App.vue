<template>
  <div>
    <router-view></router-view>
  </div>
</template>

<script>
import NavbarHome from './components/NavbarHome.vue';

export default {
  name: "App",
  components: {
    NavbarHome,
  },
};
</script>

<style scoped>
/* Estilo para imagem responsiva */
.image-container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 10px;
}

.responsive-image {
  max-width: 90%;
  height: auto;
  border-radius: 10px; /* Opcional: arredondar os cantos */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Opcional: sombra */
}
</style>
