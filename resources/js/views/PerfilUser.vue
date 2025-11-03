<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar Desktop -->
    <aside class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </aside>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100 bg-light">
      <NavDashboard />

      <div class="container py-5 mt-4">
        <div class="card shadow-sm border-0 p-4">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
          </div>

          <div v-else-if="profile" class="d-flex flex-column flex-md-row align-items-center">
            <img
              :src="profile.foto_url || placeholder"
              alt="Foto do Usuário"
              class="rounded-circle shadow-sm me-4 mb-4 mb-md-0"
              style="width: 150px; height: 150px; object-fit: cover;"
              @error="handleImageError"
            />

            <div class="text-center text-md-start">
              <h3 class="fw-bold mb-2 text-primary">{{ profile.nome }} {{ profile.apelido }}</h3>
              <p class="text-muted mb-1">📅 {{ formatDate(profile.data_nascimento) }}</p>
              <p class="mb-1">📧 {{ profile.email }}</p>
              <p class="mb-1">📱 {{ profile.telefone }}</p>
              <p class="mb-1">🏠 {{ profile.endereco }}</p>
              <p class="mb-1">⚧️ {{ profile.genero }}</p>
            </div>
          </div>

          <div v-else class="text-center text-muted py-5">
            <i class="bi bi-person-x fs-1 d-block mb-3"></i>
            Nenhum perfil encontrado.
          </div>
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
    <aside class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </aside>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import NavDashboard from "../components/NavDashboard.vue";
import SidebarDashboard from "../components/SidebarDashboard.vue";

const profile = ref(null);
const loading = ref(true);
const placeholder = "/img/placeholder-user.jpg";

const handleImageError = (e) => (e.target.src = placeholder);
const formatDate = (d) => (!d ? "-" : new Date(d).toLocaleDateString("pt-PT", { year: "numeric", month: "short", day: "numeric" }));

onMounted(async () => {
  try {
    const { data } = await axios.get("/profilusers/me"); // ✅ busca o usuário autenticado
    profile.value = data;
  } catch (error) {
    console.error("❌ Erro ao carregar perfil:", error);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.card {
  border-radius: 16px;
}
.text-primary {
  color: #8b0000 !important;
}
</style>
