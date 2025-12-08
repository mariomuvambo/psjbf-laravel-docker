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
         <h2 class="text-warning fw-bold">Pedidos de Batismo Pendentes</h2>
        </div>

        <!-- Botão de Voltar -->
        <div class="mb-3">
          <router-link to="/dashboard/painel/Sacerdote" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
          </router-link>
        </div>

        <!-- Barra de Pesquisa e Exportação -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
          <div class="col-md-6">
            <input
              type="text"
              v-model="pesquisa"
              class="form-control"
              placeholder="Pesquisar pelo nome do batizando..."
            />
          </div>

          <button class="btn btn-warning text-white" @click="exportarPDF">
            <i class="bi bi-file-earmark-pdf me-2"></i> Exportar PDF
          </button>
        </div>

        <!-- Tabela -->
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle" ref="tabelaPDF">
            <thead class="table-warning text-center">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Nascimento</th>
                <th>Pai</th>
                <th>Mãe</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(batismo, index) in registrosPaginados" :key="batismo.id">
                <td>{{ (paginaAtual - 1) * porPagina + index + 1 }}</td>
                <td>{{ batismo.nome_batizando }}</td>
                <td>{{ formatarData(batismo.data_nascimento) }}</td>
                <td>{{ batismo.nome_pai }}</td>
                <td>{{ batismo.nome_mae }}</td>
               <td>
                <button
                  class="btn btn-outline-info btn-sm me-1"
                  @click="atualizarEstado(batismo.id, 'em_analise')"
                  v-if="batismo.estado === 'pendente'"
                >
                  <i class="bi bi-eye"></i> Analisar
                </button>

              </td>

              </tr>
            </tbody>
          </table>

          <!-- Nenhum registro -->
          <div v-if="batismosFiltrados.length === 0" class="alert alert-info mt-3">
            Nenhum batismo pendente encontrado.
          </div>
        </div>

        <!-- Paginação -->
        <nav v-if="totalPaginas > 1" class="mt-4">
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
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

// Componentes
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'

const batismos = ref([])
const pesquisa = ref('')
const paginaAtual = ref(1)
const porPagina = 10

const tabelaPDF = ref(null)

const carregarPendentes = async () => {
  try {
    const res = await axios.get('/batismos/pendentes')
    batismos.value = res.data
  } catch (err) {
    alert('Erro ao carregar dados.')
  }
}

const atualizarEstado = async (id, novoEstado) => {
  try {
    await axios.put(`/batismos/${id}/estado`, { estado: novoEstado })
    await carregarPendentes()
  } catch (err) {
    alert('Erro ao atualizar estado.')
  }
}



const formatarData = (dataStr) => new Date(dataStr).toLocaleDateString()

const batismosFiltrados = computed(() =>
  batismos.value.filter((b) =>
    b.nome_batizando.toLowerCase().includes(pesquisa.value.toLowerCase())
  )
)

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
  doc.text('Pedidos de Batismo Pendentes', 14, 20)

  const dados = batismosFiltrados.value.map((registro, index) => [
    index + 1,
    registro.nome_batizando,
    formatarData(registro.data_nascimento),
    registro.nome_pai,
    registro.nome_mae,
  ])

  autoTable(doc, {
    head: [['#', 'Nome', 'Nascimento', 'Pai', 'Mãe']],
    body: dados,
    startY: 30,
  })

  doc.save('batismos_pendentes.pdf')
}

onMounted(carregarPendentes)
</script>
