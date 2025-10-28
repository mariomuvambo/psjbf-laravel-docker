<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100 bg-light">
      <!-- Navbar -->
      <NavDashboard />

      <!-- Conteúdo -->
      <div class="container py-5 mt-4">
        <!-- BLOCO: Perfil do Usuário -->
        <div class="card mb-5 shadow-sm border-0" id="bloco-perfil">
          <div class="card-body d-flex flex-column flex-md-row align-items-center">
            <img
              :src="user.foto_url || 'https://via.placeholder.com/150'"
              alt="Foto do Usuário"
              class="rounded-circle shadow-sm me-4 mb-4 mb-md-0"
              style="width: 150px; height: 150px; object-fit: cover;"
            />


            <div class="text-center text-md-start">
              <h3 class="fw-bold mb-2 text-primary">{{ user.nome }} {{ user.apelido }}</h3>
              <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>{{ user.email }}</p>
              <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ user.telefone }}</p>
              <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ user.endereco }}</p>
              <p class="mb-1"><i class="fas fa-venus-mars me-2"></i>{{ user.genero }}</p>
              <p class="mb-3"><i class="fas fa-birthday-cake me-2"></i>{{ formatDate(user.data_nascimento) }}</p>
              <span class="badge bg-primary text-uppercase px-3 py-2">{{ user.tipo_usuario }}</span>
            </div>
          </div>
        </div>

        <!-- BLOCO: Estado do Processo -->
<div class="card shadow-sm border-0 mb-4" id="bloco-processo" v-if="processo">
  <div
    class="card-header d-flex align-items-center"
    :class="{
      'bg-warning text-dark': processo.estado === 'pendente',
      'bg-info text-white': processo.estado === 'em_analise',
      'bg-success text-white': processo.estado === 'aprovado',
      'bg-danger text-white': processo.estado === 'rejeitado'
    }"
  >
    <i
      class="fas me-2"
      :class="{
        'fa-hourglass-half': processo.estado === 'pendente',
        'fa-search': processo.estado === 'em_analise',
        'fa-check-circle': processo.estado === 'aprovado',
        'fa-times-circle': processo.estado === 'rejeitado'
      }"
    ></i>
    <strong>Status do Processo de Batismo / Casamento</strong>
  </div>
  <div class="card-body">
    <p class="mb-2">
      <strong>Estado Atual:</strong>
      <span class="text-capitalize">{{ processo.estado.replace('_', ' ') }}</span>
    </p>
    <p v-if="processo.data_cerimonia">
      <strong>Data da Cerimônia:</strong>
      {{ formatDate(processo.data_cerimonia) }}
    </p>
    <p class="text-muted small mb-0">Você será notificado sempre que o status for atualizado.</p>
  </div>
</div>



        <!-- BLOCO: Histórico de Doações -->
        <div class="card shadow-sm border-0" id="bloco-doacoes">
          <div class="card-header bg-success text-white d-flex align-items-center">
            <i class="fas fa-hand-holding-heart me-2"></i>
            <strong>Histórico de Doações</strong>
          </div>
          <div class="card-body p-0" v-if="doacoes.length > 0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Valor</th>
                  <th>Data</th>
                  <th>Meio</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(doacao, index) in doacoes" :key="index">
                  <td class="fw-semibold text-success">{{ doacao.valor.toFixed(2) }} MZN</td>
                  <td>{{ formatDate(doacao.data_doacao) }}</td>
                  <td class="text-capitalize">{{ doacao.meio }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="p-4 text-center text-muted" v-else>
            Nenhuma doação registrada até o momento.
          </div>
        </div>

        <!-- BLOCO: Ministérios Registrados -->
        <div class="card shadow-sm border-0 mt-5" id="bloco-ministerios">
          <div class="card-header bg-info text-white d-flex align-items-center">
            <i class="fas fa-church me-2"></i>
            <strong>Ministérios que Participo</strong>
          </div>
          <div class="card-body p-0" v-if="ministerios.length > 0">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Ministério</th>
                  <th>Nome</th>
                  <th>Contacto</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(min, index) in ministerios" :key="index">
                  <td>{{ min.reg_minister?.newMinister }}</td>
                  <td>{{ min.name }} {{ min.surname }}</td>
                  <td>{{ min.contacto }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="p-4 text-center text-muted" v-else>
            Nenhum ministério registrado até o momento.
          </div>
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import NavDashboard from '../components/NavDashboard.vue'
import SidebarDashboard from '../components/SidebarDashboard.vue'

const user = ref({})
const doacoes = ref([])
const ministerios = ref([])
const processo = ref(null)

const formatDate = (dateStr) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(dateStr).toLocaleDateString('pt-PT', options)
}

onMounted(async () => {
  try {
    const res = await axios.get('/user')

    user.value = res.data.user
    doacoes.value = res.data.doacoes
    ministerios.value = res.data.ministerios
    processo.value = res.data.processo

  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  }
})

</script>

<style scoped>
h2 {
  padding-top: 60px;
}

#bloco-doacoes,
#bloco-ministerios {
  border-radius: 14px;
  background-color: white;
}

.table-hover tbody tr:hover {
  background-color: #f1f3f5;
}

#bloco-ministerios table tbody tr:hover {
  background-color: #eef6fb;
}

.badge {
  font-size: 0.9rem;
}

#bloco-processo .card-body {
  padding: 1.5rem;
  font-size: 1rem;
}

</style>
