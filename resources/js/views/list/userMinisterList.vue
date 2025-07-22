<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="d-none d-lg-block">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 bg-light">
      <NavDashboard />

      <div class="container py-4">
        <h2 class="text-primary mb-4 text-center">🙌 Ministérios da Paróquia</h2>

        <div v-if="successMessage" class="alert alert-success text-center">
          {{ successMessage }}
        </div>

        <div class="row g-4">
          <div class="col-md-6" v-for="minister in ministers" :key="minister.id">
            <div class="card shadow h-100">
              <div class="card-body">
                <h4 class="card-title text-success">{{ minister.newMinister }}</h4>
                <ul class="list-unstyled text-secondary">
                  <li><strong>Descrição:</strong> {{ minister.finally }}</li>
                  <li><strong>Responsável Principal:</strong> {{ minister.responseMinister }}</li>
                  <li><strong>Adjunto:</strong> {{ minister.responseAdjunto }}</li>
                  <li><strong>Setor Geral:</strong> {{ minister.SectorGeral }}</li>
                  <li><strong>Setor do Ministério:</strong> {{ minister.SectorMinister }}</li>
                </ul>
                <button
                  class="btn btn-outline-primary mt-3"
                  @click="registerToMinister(minister.id)"
                  :disabled="userMinisters.some(u => u.reg_minister_id === minister.id)"
                >
                  {{ userMinisters.some(u => u.reg_minister_id === minister.id) ? 'Já Registrado' : 'Quero Participar' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="userMinisters.length > 0" class="mt-5">
          <h4 class="text-center text-success mb-3">📋 Meus Ministérios</h4>
          <ul class="list-group">
            <li
              class="list-group-item d-flex justify-content-between align-items-center"
              v-for="userMin in userMinisters"
              :key="userMin.id"
            >
              {{ userMin.reg_minister.newMinister }}
              <span class="badge bg-success">Inscrito</span>
            </li>
          </ul>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import SidebarDashboard from '../../components/SidebarDashboard.vue';
import NavDashboard from '../../components/NavDashboard.vue';
import axios from 'axios';

export default {
  components: { SidebarDashboard, NavDashboard },
  data() {
    return {
      ministers: [],
      userMinisters: [],
      successMessage: "",
    };
  },
  methods: {
    async fetchData(endpoint, target) {
      try {
        const response = await axios.get(endpoint);
        this[target] = response.data;
      } catch (error) {
        console.error(`Erro ao buscar ${target}:`, error);
      }
    },
    async registerToMinister(ministerId) {
      try {
        const response = await axios.post("/user_ministers", {
          reg_minister_id: ministerId,
        });

        this.successMessage = response.data.message;
        this.fetchData("/user_ministers", "userMinisters");
      } catch (error) {
        console.error("Erro ao registrar-se:", error);
        this.successMessage = error.response?.data?.message || "Erro ao processar o registro.";
      } finally {
        setTimeout(() => (this.successMessage = ""), 3000);
      }
    },
  },
  mounted() {
    this.fetchData("/reg_ministers", "ministers");
    this.fetchData("/user_ministers", "userMinisters");
  },
};
</script>

<style scoped>
.card {
  border-radius: 1rem;
  transition: transform 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
}

.btn {
  border-radius: 0.5rem;
  font-weight: 500;
}
</style>