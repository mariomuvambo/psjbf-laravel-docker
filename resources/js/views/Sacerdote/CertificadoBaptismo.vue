<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar (Desktop) -->
    <div class="d-none d-lg-block">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1">
      <NavDashboard />

      <div class="container mt-4">
        <h2 class="text-danger mb-4">Certificado de Batismo</h2>

        <!-- Botões responsivos -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
          <router-link to="/painel/Sacerdote" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
          </router-link>

          <button class="btn btn-outline-primary" @click="window.print()">
            <i class="bi bi-printer me-2"></i> Imprimir
          </button>
        </div>

        <!-- Certificado -->
        <div class="border border-dark p-5 bg-white rounded shadow-sm" id="certificado">
          <h3 class="text-center text-uppercase fw-bold mb-4">Certificado de Batismo</h3>

          <p>
            Certificamos que <strong>{{ batismo.nome_batizando }}</strong>,
            filho(a) de <strong>{{ batismo.nome_pai }}</strong> e <strong>{{ batismo.nome_mae }}</strong>,
            nascido(a) em <strong>{{ batismo.data_nascimento }}</strong>, foi batizado(a) na Igreja Católica.
          </p>

          <p class="mt-4">
            Este sacramento foi ministrado no dia <strong>{{ batismo.data_batismo }}</strong> na paróquia
            <strong>{{ batismo.paroquia }}</strong>.
          </p>

          <p class="mt-4">
            Como padrinhos: <strong>{{ batismo.nome_padrinho }}</strong> e <strong>{{ batismo.nome_madrinha }}</strong>.
          </p>

          <p class="mt-5 text-end">
            __________________________<br />
            <strong>{{ sacerdote }}</strong><br />
            Sacerdote
          </p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'

const batismo = ref({})
const sacerdote = 'Pe. António Manuel'

onMounted(async () => {
  const id = 1 // Ex: via rota /certificado/:id
  const res = await axios.get(`/batismos/${id}`, {
    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
  })
  batismo.value = res.data
})
</script>

<style scoped>
@media print {
  .btn,
  .d-none,
  nav,
  aside {
    display: none !important;
  }

  #certificado {
    margin: 0;
    border: none;
  }
}
</style>
