<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <main class="flex-grow-1 min-vh-100">
      <NavDashboard />

      <div class="container py-4 mt-5">
        <!-- Botão para alternar entre avisos lidos e não lidos -->
        <div class="text-center mb-4">
          <button @click="toggleAvisos" class="btn btn-outline-dark rounded-pill px-4">
            <i :class="mostrarLidos ? 'fas fa-eye-slash' : 'fas fa-eye'" class="me-2"></i>
            {{ mostrarLidos ? 'Ocultar Lidos' : 'Mostrar Lidos' }}
            <span v-if="!mostrarLidos && totalNaoLidos > 0" class="badge bg-danger ms-2">{{ totalNaoLidos }}</span>
          </button>
        </div>

        <!-- Lista de Avisos -->
        <transition-group name="fade" tag="div" class="row g-4" v-if="listaAvisos.length > 0">
          <div v-for="aviso in listaAvisos" :key="aviso.id" class="col-md-6 col-lg-4">
            <div class="card shadow border-0 h-100 position-relative rounded-4 bg-light">
              <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <h6 class="fw-bold text-maroon mb-0">
                    <i class="fas fa-bell me-2"></i>{{ aviso.title }}
                  </h6>
                  <span class="badge rounded-pill" :class="aviso.lido ? 'bg-success' : 'bg-danger'">
                    {{ aviso.lido ? 'LIDO' : 'NÃO LIDO' }}
                  </span>
                </div>
                <ul class="list-unstyled small text-muted mb-0">
                  <li><i class="fas fa-calendar-alt me-2"></i><strong>Evento:</strong> {{ formatarData(aviso.date_realize) }}</li>
                  <li><i class="fas fa-map-marker-alt me-2"></i><strong>Local:</strong> {{ aviso.address }}</li>
                  <li><i class="fas fa-clock me-2"></i><strong>Hora:</strong> {{ formatarHora(aviso.hora) }}</li>
                  <li><i class="fas fa-file-alt me-2"></i><strong>Descrição:</strong> {{ formatarHora(aviso.description) }}</li>



                  
                </ul>
              </div>
              <div class="card-footer bg-transparent border-0 p-3">
                <button
                  v-if="!aviso.lido"
                  @click="marcarComoLido(aviso.id)"
                  class="btn btn-sm btn-outline-success w-100"
                  :disabled="carregando[aviso.id]"
                >
                  <i v-if="!carregando[aviso.id]" class="fas fa-check me-1"></i>
                  <i v-else class="fas fa-spinner fa-spin me-1"></i>
                  Marcar como Lido
                </button>
              </div>
            </div>
          </div>
        </transition-group>

        <!-- Nenhum aviso disponível -->
        <div v-else class="text-center text-muted py-5">
          <i class="fas fa-info-circle fa-3x mb-3"></i>
          <p class="lead">Nenhum aviso disponível no momento.</p>
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
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
      avisosLidos: [],
      avisosNaoLidos: [],
      totalNaoLidos: 0,
      mostrarLidos: false,
      carregando: {},
    };
  },
  computed: {
    listaAvisos() {
      return this.mostrarLidos ? this.avisosLidos : this.avisosNaoLidos;
    },
  },
  methods: {
    async fetchAvisos() {
      try {
        const { data } = await axios.get('/avisos');
        this.avisosLidos = data.avisos_lidos;
        this.avisosNaoLidos = data.avisos_nao_lidos;
        this.totalNaoLidos = data.total_nao_lidos;
      } catch (err) {
        console.error('Erro ao buscar avisos:', err);
      }
    },
    async marcarComoLido(id) {
      this.carregando[id] = true;
      try {
        await axios.post(`/avisos/${id}/marcar-como-lido`);
        const aviso = this.avisosNaoLidos.find(a => a.id === id);
        if (aviso) {
          aviso.lido = true;
          this.avisosNaoLidos = this.avisosNaoLidos.filter(a => a.id !== id);
          this.avisosLidos.push(aviso);
          this.totalNaoLidos--;
        }
      } catch (err) {
        console.error('Erro ao marcar aviso como lido:', err);
      } finally {
        this.carregando[id] = false;
      }
    },
    toggleAvisos() {
      this.mostrarLidos = !this.mostrarLidos;
    },
    formatarData(data) {
      return new Date(data).toLocaleDateString('pt-PT', { day: '2-digit', month: 'long', year: 'numeric' });
    },
    formatarHora(hora) {
      try {
        const date = new Date(hora);
        return date.toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' });
      } catch {
        return 'Hora inválida';
      }
    }
  },
  mounted() {
    this.fetchAvisos();
  }
};
</script>

<style scoped>
.card {
  transition: transform 0.2s ease-in-out;
  border-radius: 1rem;
}
.card:hover {
  transform: translateY(-3px);
}
.badge {
  font-size: 13px;
  padding: 5px 10px;
}
.text-maroon {
  color: #800000;
}
.fade-enter-active,
.fade-leave-active {
  transition: all 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>