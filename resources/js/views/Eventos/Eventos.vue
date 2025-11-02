<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar Desktop -->
    <aside class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </aside>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100 bg-light">
      <NavDashboard />

      <div class="container mt-5 pt-5">
        <h2 class="text-center fw-bold text-maroon mb-4">
          Próximos Eventos
        </h2>

        <!-- Carregando -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-maroon" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
        </div>

        <!-- Lista de eventos -->
        <section
          v-else-if="uniqueEvents.length"
          class="row gx-4 gy-4 justify-content-center"
        >
          <div
            v-for="event in uniqueEvents"
            :key="event.id"
            class="col-12 col-sm-6 col-lg-3 d-flex"
          >
            <div class="event-card shadow-sm text-center w-100">
              <img
                :src="getImageUrl(event.image_url)"
                :alt="`Imagem de ${event.title}`"
                class="event-image"
                @error="handleImageError"
              />
              <h5 class="fw-bold mt-3 text-maroon">{{ event.title }}</h5>
              <p class="date">
                📅 {{ formatDate(event.date) }} ⏰ {{ event.time }}
              </p>
              <p class="location">📍 {{ event.location }}</p>
              <p class="description">{{ event.description }}</p>
            </div>
          </div>
        </section>

        <!-- Nenhum evento -->
        <div v-else class="text-center py-5 text-muted">
          <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
          Nenhum evento disponível no momento.
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
    <aside class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </aside>
  </div>
</template>

<script>
import axios from "axios";
import NavDashboard from "../../components/NavDashboard.vue";
import SidebarDashboard from "../../components/SidebarDashboard.vue";

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      events: [],
      loading: true,
      placeholder: "/img/placeholder-evento.jpg",
      apiUrl: import.meta.env.VITE_API_URL || "http://127.0.0.1:8000/api",
    };
  },
  computed: {
    // Evita duplicação de eventos
    uniqueEvents() {
      const map = new Map();
      this.events.forEach((e) => map.set(e.id, e));
      return [...map.values()];
    },
  },
  methods: {
    async fetchEvents() {
      try {
        const url = `${this.apiUrl}/events`;
        console.log("🔗 URL chamada:", url);

        const { data } = await axios.get(url);
        console.log("✅ Eventos carregados:", data);

        this.events = Array.isArray(data) ? data : [];
        console.log("📦 Eventos atribuídos:", this.events.length);
      } catch (error) {
        console.error("❌ Erro ao buscar eventos:", error);
      } finally {
        this.loading = false;
      }
    },

    // 🔗 Gera URL completa da imagem
    getImageUrl(imagePath) {
      if (!imagePath) return this.placeholder;

      // Se a imagem já é uma URL completa (começa com http), retorna direto
      if (imagePath.startsWith("http")) return imagePath;

      // Caso contrário, gera o link completo
      const base = this.apiUrl.replace("/api", "");
      return `${base}/storage/${imagePath}`;
    },

    // Substitui imagem quebrada pelo placeholder
    handleImageError(event) {
      event.target.src = this.placeholder;
    },

    // Formata datas
    formatDate(dateStr) {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      return date.toLocaleDateString("pt-PT", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
    },
  },
  mounted() {
    this.fetchEvents();
  },
};
</script>

<style scoped>
.text-maroon {
  color: #8b0000;
}

.event-card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: all 0.3s ease;
  border: 1px solid #e0e0e0;
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.event-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
}

.date,
.location {
  font-size: 0.9em;
  color: #555;
}

.description {
  font-size: 0.9em;
  color: #666;
  margin-top: 10px;
  text-align: center;
}
</style>
