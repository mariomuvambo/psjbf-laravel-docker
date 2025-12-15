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

      <!-- Conteúdo Principal -->
      <div class="container py-5 mt-4">
        <!-- Formulário de Registro -->
        <div class="card shadow-lg rounded-4 mb-5">
          <div class="card-header bg-danger text-white text-center">
            <h1 class="h4 fw-bold">Registro de Baptismo</h1>
            <p class="fst-italic mb-0">O pedido será analisado por um sacerdote antes da confirmação.</p>
          </div>

          <form @submit.prevent="submitForm" enctype="multipart/form-data" class="card-body bg-white">
            <!-- Alerta Informativo -->
            <div class="alert alert-warning">
              <strong>Atenção:</strong> Após o envio, o baptismo será avaliado por um sacerdote. Você será notificado sobre o estado.
            </div>

            <!-- Dados do Batizando -->
            <section class="mb-4">
              <h5 class="text-danger fw-semibold mb-3">Dados do Baptizando</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <input v-model="form.nome_batizando" type="text" class="form-control" placeholder="Nome do Batizando" required />
                </div>
                <div class="col-md-6">
                  <input v-model="form.data_nascimento" type="date" class="form-control" required />
                </div>
                <div class="col-12">
                  <input v-model="form.local_nascimento" type="text" class="form-control" placeholder="Local de Nascimento" required />
                </div>
              </div>
            </section>

            <!-- Filiação -->
            <section class="mb-4">
              <h5 class="text-danger fw-semibold mb-3">Filiação</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <input v-model="form.nome_pai" type="text" class="form-control" placeholder="Nome do Pai" required />
                </div>
                <div class="col-md-6">
                  <input v-model="form.nome_mae" type="text" class="form-control" placeholder="Nome da Mãe" required />
                </div>
              </div>
            </section>

            <!-- Padrinhos -->
            <section class="mb-4">
              <h5 class="text-danger fw-semibold mb-3">Padrinhos</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <input v-model="form.nome_padrinho" type="text" class="form-control" placeholder="Nome do Padrinho" required />
                </div>
                <div class="col-md-6">
                  <input v-model="form.nome_madrinha" type="text" class="form-control" placeholder="Nome da Madrinha" required />
                </div>
              </div>
            </section>

            <!-- Documento -->
            <section class="mb-4">
              <h5 class="text-danger fw-semibold mb-3">Documento de Identificação</h5>
              <input type="file" class="form-control" @change="handleFileUpload" accept=".pdf,.jpg,.jpeg,.png" required />
              <small class="text-muted d-block mt-1">Anexe BI ou Certidão (PDF ou imagem máx. 2MB)</small>
            </section>

            <!-- Botão Enviar -->
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-danger px-5 py-2 fw-bold">Enviar Pedido</button>
            </div>

            <!-- Mensagens -->
            <div v-if="successMessage" class="alert alert-success text-center mt-4">
              {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="alert alert-danger text-center mt-4">
              {{ errorMessage }}
            </div>
          </form>
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

// Formulário principal
const form = ref({
  nome_batizando: '',
  data_nascimento: '',
  local_nascimento: '',
  nome_pai: '',
  nome_mae: '',
  nome_padrinho: '',
  nome_madrinha: '',
})

const documento = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const batismos = ref([])

// Submeter formulário
const submitForm = async () => {
  const formData = new FormData()
  for (const key in form.value) {
    formData.append(key, form.value[key])
  }
  formData.append('documento_identificacao', documento.value)

  try {
    const response = await axios.post('/batismos', formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })
    successMessage.value = response.data.message || 'Pedido enviado com sucesso!'
    errorMessage.value = ''
    fetchBatismos()
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Erro ao enviar o pedido.'
    successMessage.value = ''
  }
}

// Captura do ficheiro
const handleFileUpload = (e) => {
  documento.value = e.target.files[0]
}

// Buscar registros do usuário
const fetchBatismos = async () => {
  try {
    const response = await axios.get('/batismos/minhas', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    batismos.value = response.data
  } catch (err) {
    console.error('Erro ao buscar registros:', err)
  }
}

// Formatar datas para DD/MM/YYYY
const formatarData = (data) => {
  const d = new Date(data)
  return d.toLocaleDateString('pt-PT')
}

onMounted(fetchBatismos)
</script>

<style scoped>
.card-header h5,
.card-header h4 {
  margin-bottom: 0;
}
</style>
