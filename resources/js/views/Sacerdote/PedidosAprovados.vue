<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <SidebarDashboard />

    <!-- Conteúdo principal -->
    <div class="flex-grow-1">
      <NavDashboard />

      <main class="container" style="margin-top: 100px;">
        <!-- Título -->
        <div class="mb-4 text-center">
          <h2 class="text-danger fw-bold">Lista de Batismos Aprovados</h2>
        </div>

        <!-- Botões -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <router-link to="/painel/Sacerdote" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
          </router-link>

          <button class="btn btn-danger" @click="exportarPDF">
            <i class="bi bi-file-earmark-pdf me-2"></i> Exportar PDF
          </button>
        </div>

        <!-- Filtro -->
        <div class="row mb-4">
          <div class="col-md-6">
            <input
              v-model="filtro"
              type="text"
              class="form-control"
              placeholder="Pesquisar por nome do batizando..."
            />
          </div>
        </div>

        <!-- Tabela -->
        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle" ref="tabelaPDF">
            <thead class="table-danger text-center">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Data de Nascimento</th>
                <th>Pais</th>
                <th>Padrinhos</th>
                <th>Aprovado em</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(registro, index) in registrosPaginados" :key="registro.id">
                <td class="text-center">{{ (paginaAtual - 1) * porPagina + index + 1 }}</td>
                <td>{{ registro.nome_batizando }}</td>
                <td>{{ registro.data_nascimento }}</td>
                <td>{{ registro.nome_pai }} & {{ registro.nome_mae }}</td>
                <td>{{ registro.nome_padrinho }} & {{ registro.nome_madrinha }}</td>
                <td>{{ registro.data_aprovacao }}</td>
              </tr>
              <tr v-if="registrosPaginados.length === 0">
                <td colspan="6" class="text-center text-muted">Nenhum registro encontrado.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <nav class="mt-4">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
              <button class="page-link" @click="paginaAtual--">Anterior</button>
            </li>
            <li
              class="page-item"
              v-for="page in totalPaginas"
              :key="page"
              :class="{ active: page === paginaAtual }"
            >
              <button class="page-link" @click="paginaAtual = page">{{ page }}</button>
            </li>
            <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
              <button class="page-link" @click="paginaAtual++">Próxima</button>
            </li>
          </ul>
        </nav>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

// Componentes
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'

const batismos = ref([])
const filtro = ref('')
const paginaAtual = ref(1)
const porPagina = 10
const tabelaPDF = ref(null)

const batismosFiltrados = computed(() => {
  if (!filtro.value.trim()) return batismos.value
  return batismos.value.filter((registro) =>
    registro.nome_batizando.toLowerCase().includes(filtro.value.toLowerCase())
  )
})

const totalPaginas = computed(() => {
  return Math.ceil(batismosFiltrados.value.length / porPagina)
})

const registrosPaginados = computed(() => {
  const inicio = (paginaAtual.value - 1) * porPagina
  const fim = inicio + porPagina
  return batismosFiltrados.value.slice(inicio, fim)
})

const exportarPDF = () => {
  const doc = new jsPDF()
  doc.setFontSize(14)
  doc.text('Lista de Batismos Aprovados', 14, 20)

  const dados = batismosFiltrados.value.map((registro, index) => [
    index + 1,
    registro.nome_batizando,
    registro.data_nascimento,
    `${registro.nome_pai} & ${registro.nome_mae}`,
    `${registro.nome_padrinho} & ${registro.nome_madrinha}`,
    registro.data_aprovacao,
  ])

  autoTable(doc, {
    head: [['#', 'Nome', 'Data Nasc.', 'Pais', 'Padrinhos', 'Aprovado em']],
    body: dados,
    startY: 30,
  })

  doc.save('batismos_aprovados.pdf')
}

onMounted(async () => {
  try {
    const res = await axios.get('/batismos/aprovados', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })
    batismos.value = res.data
  } catch (error) {
    console.error('Erro ao buscar batismos:', error)
  }
})
</script>
