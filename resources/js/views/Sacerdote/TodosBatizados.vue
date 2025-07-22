<template>
  <div class="d-flex flex-column flex-lg-row">
    <div class="d-none d-lg-block">
      <SidebarDashboard />
    </div>

    <main class="flex-grow-1">
      <NavDashboard />

      <div class="container" style="margin-top: 100px;">
        <div class="text-center mb-4">
          <h2 class="text-primary fw-bold">Todos os Batismos</h2>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
          <div>
            <span class="fw-semibold me-2">Filtrar por estado:</span>
            <div class="btn-group" role="group">
              <button class="btn btn-outline-secondary" :class="{ active: filtro === 'todos' }" @click="aplicarFiltro('todos')">Todos</button>
              <button class="btn btn-outline-warning" :class="{ active: filtro === 'pendente' }" @click="aplicarFiltro('pendente')">Pendente</button>
              <button class="btn btn-outline-info" :class="{ active: filtro === 'em_analise' }" @click="aplicarFiltro('em_analise')">Em Análise</button>
              <button class="btn btn-outline-success" :class="{ active: filtro === 'aprovado' }" @click="aplicarFiltro('aprovado')">Aprovado</button>
              <button class="btn btn-outline-danger" :class="{ active: filtro === 'rejeitado' }" @click="aplicarFiltro('rejeitado')">Rejeitado</button>
            </div>
          </div>

          <div class="input-group w-auto">
            <input type="text" class="form-control" v-model="pesquisa" placeholder="Pesquisar batizando..." />
          </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
          <router-link to="/painel/Sacerdote" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar
          </router-link>

          <button class="btn btn-primary" @click="exportarPDF">
            <i class="bi bi-file-earmark-pdf me-2"></i> Exportar PDF
          </button>
        </div>

        <div v-if="batismosFiltrados.length > 0" class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Nascimento</th>
                <th>Pai</th>
                <th>Mãe</th>
                <th>Estado</th>
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
                  <span :class="['badge text-capitalize', estadoClasse(batismo.estado)]">
                    {{ batismo.estado.replace('_', ' ') }}
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <button v-if="batismo.estado === 'pendente' || batismo.estado === 'em_analise'" @click="abrirModal(batismo, 'aprovar')" class="btn btn-sm btn-success">
                      <i class="bi bi-check2-circle"></i>
                    </button>
                    <button v-if="batismo.estado === 'pendente' || batismo.estado === 'em_analise'" @click="abrirModal(batismo, 'rejeitar')" class="btn btn-sm btn-danger">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="alert alert-info">Nenhum batismo encontrado.</div>

        <nav v-if="totalPaginas > 1" class="mt-4">
          <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
              <button class="page-link" @click="paginaAtual--">Anterior</button>
            </li>
            <li class="page-item" v-for="page in totalPaginas" :key="page" :class="{ active: page === paginaAtual }">
              <button class="page-link" @click="paginaAtual = page">{{ page }}</button>
            </li>
            <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
              <button class="page-link" @click="paginaAtual++">Próxima</button>
            </li>
          </ul>
        </nav>
      </div>

      <!-- Modal Bootstrap -->
      <div class="modal fade" id="modalDecisao" tabindex="-1" aria-labelledby="modalDecisaoLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" :class="tipoModal === 'aprovar' ? 'bg-success text-white' : 'bg-danger text-white'">
              <h5 class="modal-title" id="modalDecisaoLabel">
                {{ tipoModal === 'aprovar' ? 'Aprovar Batismo' : 'Rejeitar Batismo' }}
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <p>
                {{ tipoModal === 'aprovar' ? `Confirme a aprovação do batismo de ${batismoSelecionado?.nome_batizando}` : `Informe o motivo da rejeição para ${batismoSelecionado?.nome_batizando}` }}
              </p>
              <div v-if="tipoModal === 'aprovar'">
                <label class="form-label">Data do Batismo:</label>
                <input type="date" v-model="dataBatismo" class="form-control" />
              </div>
              <div v-else>
                <label class="form-label">Motivo:</label>
                <textarea v-model="descricaoRejeicao" class="form-control" rows="3"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn" :class="tipoModal === 'aprovar' ? 'btn-success' : 'btn-danger'" @click="confirmarDecisao">
                Confirmar
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'
import * as bootstrap from 'bootstrap'
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'

const batismos = ref([])
const pesquisa = ref('')
const filtro = ref('todos')
const paginaAtual = ref(1)
const porPagina = 10
const batismoSelecionado = ref(null)
const dataBatismo = ref('')
const descricaoRejeicao = ref('')
const tipoModal = ref('')
let modalInstance = null

const carregarBatismos = async () => {
  let url = '/batismos'
  if (filtro.value === 'pendente') url = '/batismos/pendentes'
  else if (filtro.value === 'aprovado') url = '/batismos/aprovados'
  else if (filtro.value === 'rejeitado') url = '/batismos/rejeitados'
  else if (filtro.value === 'em_analise') url = '/batismos/em_analise'

  const res = await axios.get(url)
  batismos.value = res.data
}

const aplicarFiltro = async (estadoSelecionado) => {
  filtro.value = estadoSelecionado
  paginaAtual.value = 1
  await carregarBatismos()
}

const abrirModal = (registro, tipo) => {
  batismoSelecionado.value = registro
  tipoModal.value = tipo
  dataBatismo.value = ''
  descricaoRejeicao.value = ''

  const modalEl = document.getElementById('modalDecisao')
  modalInstance = new bootstrap.Modal(modalEl)
  modalInstance.show()
}

const confirmarDecisao = async () => {
  if (tipoModal.value === 'aprovar' && !dataBatismo.value) {
    alert('Por favor, selecione a data do batismo.')
    return
  }

  const payload = {
    estado: tipoModal.value === 'aprovar' ? 'aprovado' : 'rejeitado',
    data_batismo: tipoModal.value === 'aprovar' ? dataBatismo.value : null,
    descricao_rejeicao: tipoModal.value === 'rejeitar' ? descricaoRejeicao.value : null
  }

  await axios.put(`/batismos/${batismoSelecionado.value.id}/estado`, payload)
  await carregarBatismos()
  modalInstance.hide()
}

const formatarData = (dataStr) => new Date(dataStr).toLocaleDateString('pt-PT')

const estadoClasse = (estado) => {
  return {
    aprovado: 'bg-success text-white',
    rejeitado: 'bg-danger text-white',
    pendente: 'bg-warning text-dark',
    em_analise: 'bg-info text-white',
  }[estado] || 'bg-secondary text-white'
}

const batismosFiltrados = computed(() =>
  batismos.value.filter((b) => b.nome_batizando.toLowerCase().includes(pesquisa.value.toLowerCase()))
)

const totalPaginas = computed(() =>
  Math.ceil(batismosFiltrados.value.length / porPagina)
)

const registrosPaginados = computed(() => {
  const inicio = (paginaAtual.value - 1) * porPagina
  return batismosFiltrados.value.slice(inicio, inicio + porPagina)
})

const exportarPDF = () => {
  const doc = new jsPDF()
  doc.setFontSize(14)
  doc.text('Todos os Batismos', 14, 20)

  const dados = batismosFiltrados.value.map((registro, index) => [
    index + 1,
    registro.nome_batizando,
    formatarData(registro.data_nascimento),
    registro.nome_pai,
    registro.nome_mae,
    registro.estado,
  ])

  autoTable(doc, {
    head: [['#', 'Nome', 'Nascimento', 'Pai', 'Mãe', 'Estado']],
    body: dados,
    startY: 30,
  })

  doc.save('todos_os_batismos.pdf')
}

onMounted(() => carregarBatismos())
</script>

<style scoped>
textarea {
  resize: none;
}
</style>