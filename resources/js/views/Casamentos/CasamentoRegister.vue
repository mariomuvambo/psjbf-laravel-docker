<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo principal -->
    <main class="flex-grow-1 min-vh-100">
      <NavDashboard />

      <div class="container py-5 px-3 mt-4">
        <div class="bg-white bg-opacity-75 rounded-4 shadow-lg p-5">
          <!-- <h1 class="text-center mb-5 text-rose fs-2 fw-bold">💍 Registro de Casamento</h1> -->

          <!-- Formulário de Registro e Edição -->
          <form @submit.prevent="submitForm">
            <div class="row">
              <div class="col-md-6 mb-4">
                <h5 class="text-rose mb-3">👨 Dados do Noivo</h5>
                <input v-model="form.nome_noivo" type="text" class="form-control mb-3" placeholder="Nome do Noivo" />
                <div v-for="(file, index) in documentosNoivo" :key="'noivo-'+index" class="row g-2 mb-3">
                  <div class="col-6">
                    <input type="file" class="form-control" @change="handleFileChange($event, index, 'noivo')" />
                  </div>
                  <div class="col-6">
                    <select class="form-select" v-model="tiposNoivo[index]">
                      <option disabled value="">Tipo Documento</option>
                      <option value="BI">BI</option>
                      <option value="Certidão de Batismo">Certidão de Batismo</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-6 mb-4">
                <h5 class="text-rose mb-3">👰 Dados da Noiva</h5>
                <input v-model="form.nome_noiva" type="text" class="form-control mb-3" placeholder="Nome da Noiva" />
                <div v-for="(file, index) in documentosNoiva" :key="'noiva-'+index" class="row g-2 mb-3">
                  <div class="col-6">
                    <input type="file" class="form-control" @change="handleFileChange($event, index, 'noiva')" />
                  </div>
                  <div class="col-6">
                    <select class="form-select" v-model="tiposNoiva[index]">
                      <option disabled value="">Tipo Documento</option>
                      <option value="BI">BI</option>
                      <option value="Certidão de Batismo">Certidão de Batismo</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <input v-model="form.data_casamento" type="date" class="form-control" />
              </div>
              <div class="col-md-6 mt-3 mt-md-0">
                <input v-model="form.local_casamento" type="text" class="form-control" placeholder="Local do Casamento" />
              </div>
            </div>

            <div class="text-center mb-4">
              <button type="submit" class="btn btn-rose btn-lg px-5 shadow">
                {{ editingId ? 'Atualizar' : 'Registrar' }} 💒
              </button>
            </div>
          </form>

          <!-- Tabela de Casamentos -->
          <div class="mt-5">
            <h4 class="text-center text-rose mb-4">📋 Meus Registros de Casamento</h4>
            <div class="table-responsive">
              <table class="table table-bordered table-hover align-middle text-center shadow-sm bg-white rounded">
                <thead class="table-rose text-white">
                  <tr>
                    <th>👨 Noivo</th>
                    <th>👰 Noiva</th>
                    <th>📅 Data</th>
                    <th>📍 Local</th>
                    <th>📊 Estado</th>
                    <th>⚙️ Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="registro in casamentos" :key="registro.id">
                    <td>{{ registro.nome_noivo }}</td>
                    <td>{{ registro.nome_noiva }}</td>
                    <td>{{ registro.data_casamento }}</td>
                    <td>{{ registro.local_casamento }}</td>
                    <td>
                      <span class="badge px-3 py-2 fs-6 rounded-pill" :class="estadoClass(registro.estado)">
                        {{ registro.estado }}
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-2" @click="loadRegistro(registro)">✏️</button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteRegistro(registro.id)">🗑️</button>
                    </td>
                  </tr>
                  <tr v-if="casamentos.length === 0">
                    <td colspan="6">Nenhum registro encontrado.</td>
                  </tr>
                </tbody>
              </table>
            </div>
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
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'

const form = ref({ nome_noivo: '', nome_noiva: '', data_casamento: '', local_casamento: '' })
const documentosNoivo = ref([null, null])
const documentosNoiva = ref([null, null])
const tiposNoivo = ref(['', ''])
const tiposNoiva = ref(['', ''])
const casamentos = ref([])
const editingId = ref(null)

function loadRegistro(registro) {
  editingId.value = registro.id
  form.value = {
    nome_noivo: registro.nome_noivo,
    nome_noiva: registro.nome_noiva,
    data_casamento: registro.data_casamento,
    local_casamento: registro.local_casamento,
  }
  documentosNoivo.value = [null, null]
  documentosNoiva.value = [null, null]
  tiposNoivo.value = ['', '']
  tiposNoiva.value = ['', '']
}

function handleFileChange(e, index, tipo) {
  const file = e.target.files[0]
  if (tipo === 'noivo') documentosNoivo.value[index] = file
  if (tipo === 'noiva') documentosNoiva.value[index] = file
}

function estadoClass(estado) {
  return {
    'bg-secondary text-white': estado === 'pendente',
    'bg-warning text-dark': estado === 'em_analise',
    'bg-success text-white': estado === 'aprovado',
    'bg-danger text-white': estado === 'rejeitado'
  }
}

async function fetchCasamentos() {
  try {
    const response = await axios.get('/casamentos')
    casamentos.value = response.data
  } catch (error) {
    console.error('Erro ao carregar os registros:', error)
  }
}

async function submitForm() {
  const formData = new FormData()
  formData.append('nome_noivo', form.value.nome_noivo)
  formData.append('nome_noiva', form.value.nome_noiva)
  formData.append('data_casamento', form.value.data_casamento)
  formData.append('local_casamento', form.value.local_casamento)

  const allDocs = [...documentosNoivo.value, ...documentosNoiva.value]
  const allTipos = [...tiposNoivo.value, ...tiposNoiva.value]

  allDocs.forEach((file, index) => {
    if (file) {
      formData.append(`documentos[${index}]`, file)
      formData.append(`tipos_documentos[${index}]`, allTipos[index] || '')
    }
  })

  try {
    if (editingId.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/casamentos/${editingId.value}`, formData)
      alert('Registro atualizado com sucesso!')
    } else {
      await axios.post('/casamentos', formData)
      alert('Registro feito com sucesso!')
    }

    await fetchCasamentos()
    resetForm()
  } catch (error) {
    console.error('Erro ao enviar dados:', error.response?.data || error.message)
    alert('Erro ao enviar dados!')
  }
}

async function deleteRegistro(id) {
  if (!confirm('Tem certeza que deseja apagar este registro?')) return
  try {
    await axios.delete(`/casamentos/${id}`)
    casamentos.value = casamentos.value.filter(r => r.id !== id)
    alert('Registro removido com sucesso!')
  } catch (error) {
    console.error(error)
    alert('Erro ao apagar registro')
  }
}

function resetForm() {
  editingId.value = null
  form.value = {
    nome_noivo: '',
    nome_noiva: '',
    data_casamento: '',
    local_casamento: '',
  }
  documentosNoivo.value = [null, null]
  documentosNoiva.value = [null, null]
  tiposNoivo.value = ['', '']
  tiposNoiva.value = ['', '']
}

onMounted(fetchCasamentos)
</script>

<style scoped>
.text-rose {
  color: #c2185b;
}
.btn-rose {
  background-color: #c2185b;
  color: white;
}
.btn-rose:hover {
  background-color: #ad1457;
}
.table-rose {
  background-color: #c2185b;
}
</style>
