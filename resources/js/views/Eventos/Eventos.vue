<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar - Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100">
      <NavDashboard />

      <div class="container" style="margin-top: 120px;">
        <!-- Lista de Eventos -->
        <section class="row gx-4 gy-4 justify-content-center">
          <div
            v-for="(event, index) in events"
            :key="index"
            class="col-12 col-sm-6 col-lg-3 d-flex"
          >
            <div class="event-card shadow-sm text-center w-100">
              <img
                :src="getImageUrl(event.image)"
                :alt="`Imagem de ${event.title}`"
                class="event-image"
              />
              <h5 class="fw-bold mt-3 text-maroon">{{ event.title }}</h5>
              <p class="date">📅 {{ formatDate(event.date) }} ⏰ {{ event.time }}</p>
              <p class="location">📍 {{ event.location }}</p>
              <p class="description">{{ event.description }}</p>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Sidebar - Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '../../components/NavDashboard.vue';
import SidebarDashboard from '../../components/SidebarDashboard.vue';

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      events: []
    };
  },
  methods: {
    fetchEvents() {
      axios.get('/events')
        .then(response => {
          this.events = response.data;
        })
        .catch(error => {
          console.error('Erro ao buscar eventos:', error);
        });
    },
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });
    },
    getImageUrl(filename) {
      return filename
        ? `http://localhost:8000/storage/events/${filename}`
        : 'https://via.placeholder.com/300x200?text=Evento';
    }
  },
  mounted() {
    this.fetchEvents();
  }
};
</script>

<style scoped>
.text-maroon {
  color: #8B0000;
}

.event-card {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  transition: all 0.3s ease;
  border: 1px solid #e0e0e0;
}

.event-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.event-image {
  width: 100%;
  max-height: 180px;
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
