<template>
  <div class="d-flex flex-column flex-lg-row">
    <SidebarDashboard />

    <div class="flex-grow-1">
      <NavDashboard />

      <main class="container" style="margin-top: 100px;">
        <div class="text-center mb-4">
          <h2 class="text-primary fw-bold">📜 Processos de Batismo em Análise</h2>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <router-link to="/painel/Sacerdote" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i> Voltar 
          </router-link>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <input
              v-model="filtro"
              type="text"
              class="form-control"
              placeholder="🔍 Pesquisar por nome do batizando..."
            />
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary text-center">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Data Nasc.</th>
                <th>País</th>
                <th>Padrinhos</th>
                <th>Estado</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(registro, index) in registrosPaginados" :key="registro.id">
                <td class="text-center">{{ (paginaAtual - 1) * porPagina + index + 1 }}</td>
                <td>{{ registro.nome_batizando }}</td>
                <td>{{ registro.data_nascimento }}</td>
                <td>{{ registro.nome_pai }} & {{ registro.nome_mae }}</td>
                <td>{{ registro.nome_padrinho }} & {{ registro.nome_madrinha }}</td>
                <td class="text-center">
                  <span :class="{
                    'badge bg-warning text-dark': registro.estado === 'pendente',
                    'badge bg-info text-dark': registro.estado === 'em_analise',
                    'badge bg-success': registro.estado === 'aprovado',
                    'badge bg-danger': registro.estado === 'rejeitado'
                  }">
                    {{ registro.estado.replace('_', ' ') }}
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-success btn-sm me-1" @click="abrirModal(registro, 'aprovar')">
                    <i class="bi bi-check2-circle"></i>
                  </button>
                  <button class="btn btn-danger btn-sm" @click="abrirModal(registro, 'rejeitar')">
                    <i class="bi bi-x-circle"></i>
                  </button>
                </td>
              </tr>
              <tr v-if="registrosPaginados.length === 0">
                <td colspan="7" class="text-center text-muted">Nenhum registro pendente encontrado.</td>
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

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="modalDecisao" tabindex="-1" aria-labelledby="modalDecisaoLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div
            class="modal-header"
            :class="tipoModal === 'aprovar' ? 'bg-success text-white' : 'bg-danger text-white'"
          >
            <h5 class="modal-title" id="modalDecisaoLabel">
              {{ tipoModal === 'aprovar' ? 'Aprovar Batismo' : 'Rejeitar Batismo' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <p>
              {{ tipoModal === 'aprovar'
                ? `Confirme a aprovação do batismo de ${batismoSelecionado?.nome_batizando}`
                : `Informe o motivo da rejeição para ${batismoSelecionado?.nome_batizando}` }}
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
            <button
              type="button"
              class="btn"
              :class="tipoModal === 'aprovar' ? 'btn-success' : 'btn-danger'"
              @click="confirmarDecisao"
            >
              Confirmar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import * as bootstrap from 'bootstrap' // ← IMPORTAÇÃO NECESSÁRIA

import SidebarDashboard from '../../components/SidebarDashboard.vue'
import NavDashboard from '../../components/NavDashboard.vue'

const batismos = ref([])
const filtro = ref('')
const paginaAtual = ref(1)
const porPagina = 10

const batismoSelecionado = ref(null)
const dataBatismo = ref('')
const descricaoRejeicao = ref('')
const tipoModal = ref('')
let modalInstance = null

// Modal
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
  try {
    if (tipoModal.value === 'aprovar' && !dataBatismo.value) {
      alert('Por favor, selecione a data do batismo.')
      return
    }

    const payload = {
      estado: tipoModal.value === 'aprovar' ? 'aprovado' : 'rejeitado',
      data_batismo: tipoModal.value === 'aprovar' ? dataBatismo.value : null,
      descricao_rejeicao: tipoModal.value === 'rejeitar' ? descricaoRejeicao.value : null
    }

    await axios.put(`/batismos/${batismoSelecionado.value.id}/estado`, payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })

    batismoSelecionado.value.estado = payload.estado
    if (payload.estado === 'aprovado') batismoSelecionado.value.data_batismo = payload.data_batismo

    modalInstance.hide()
  } catch (error) {
    console.error('Erro ao atualizar estado:', error)
    alert('Erro ao confirmar decisão.')
  }
}

// Filtro e paginação
const batismosFiltrados = computed(() =>
  batismos.value.filter((registro) =>
    registro.nome_batizando.toLowerCase().includes(filtro.value.toLowerCase())
  )
)

const totalPaginas = computed(() => Math.ceil(batismosFiltrados.value.length / porPagina))

const registrosPaginados = computed(() => {
  const inicio = (paginaAtual.value - 1) * porPagina
  return batismosFiltrados.value.slice(inicio, inicio + porPagina)
})

// Carregamento
onMounted(async () => {
  try {
    const response = await axios.get('/batismos/em_analise', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    batismos.value = response.data
  } catch (error) {
    console.error('Erro ao carregar batismos:', error)
  }
})
</script>
<style scoped>
textarea {
  resize: none;
}
</style>
