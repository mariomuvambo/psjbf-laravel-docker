<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar (Desktop) -->
    <div class="d-none d-lg-block">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo principal -->
    <main class="flex-grow-1">
      <NavDashboard />

      <div class="container py-5 mt-5" style="min-height: 100vh;">
        <!-- Título -->
        <div class="text-center mb-4">
          <h1 class="text-primary fw-bold">Painel de Casamentos</h1>
          <p class="text-muted fs-5">
            Bem-vindo, padre. Abaixo estão os acessos rápidos para gerir os registros de casamentos.
          </p>
        </div>

        <!-- Acesso Rápido -->
        <div class="row g-4 mb-5">
          <!-- Casamentos Pendentes -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-warning h-100 shadow-sm">
              <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="bi bi-hourglass-split text-warning fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Pendentes</h5>
                <p class="card-text text-muted">Casamentos aguardando aprovação.</p>
                <router-link to="/casamentos/pendentes" class="btn btn-warning mt-auto text-white">Gerenciar</router-link>
              </div>
            </div>
          </div>

          <!-- Casamentos Aprovados -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-success h-100 shadow-sm">
              <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Aprovados</h5>
                <p class="card-text text-muted">Casamentos aprovados para cerimônia.</p>
                <router-link to="/casamentos/aprovados" class="btn btn-success mt-auto">Ver Lista</router-link>
              </div>
            </div>
          </div>

          <!-- Casamentos Realizados -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-info h-100 shadow-sm">
              <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="bi bi-check2-all text-info fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Realizados</h5>
                <p class="card-text text-muted">Casamentos já realizados.</p>
                <router-link to="/casamentos/realizados" class="btn btn-info mt-auto text-white">Ver Histórico</router-link>
              </div>
            </div>
          </div>

          <!-- Todos os Casamentos -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-secondary h-100 shadow-sm">
              <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="bi bi-people text-secondary fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Todos os Casamentos</h5>
                <p class="card-text text-muted">Histórico completo dos casamentos.</p>
                <router-link to="/casamentos/todos" class="btn btn-secondary mt-auto">Ver Todos</router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Painel de Avisos -->
        <section>
          <h3 class="mb-4 text-primary fw-bold">Painel de Avisos</h3>

          <div class="list-group shadow-sm">
            <div
              v-for="aviso in avisos"
              :key="aviso.id"
              class="list-group-item list-group-item-action flex-column align-items-start"
            >
              <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1">{{ aviso.titulo }}</h5>
                <small class="text-muted">{{ formatarData(aviso.data) }}</small>
              </div>
              <p class="mb-1">{{ aviso.mensagem }}</p>
            </div>
            <div v-if="avisos.length === 0" class="text-center p-4 text-muted fst-italic">
              Nenhum aviso disponível.
            </div>
          </div>
        </section>

        <!-- Versículo -->
        <div class="mt-5 text-center text-muted fst-italic">
          <small>“O amor é paciente, o amor é bondoso...” – <em>1 Coríntios 13:4</em></small>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import NavDashboard from '../../../components/NavDashboard.vue'
import SidebarDashboard from '../../../components/SidebarDashboard.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const avisos = ref([])

const carregarAvisos = async () => {
  try {
    const res = await axios.get('/avisos')
    avisos.value = res.data
  } catch (e) {
    console.error('Erro ao carregar avisos:', e)
  }
}

onMounted(() => {
  carregarAvisos()
})

const formatarData = (dataStr) => new Date(dataStr).toLocaleDateString('pt-PT')
</script>

<style scoped>
/* Mantém min-height na tela */
</style>
