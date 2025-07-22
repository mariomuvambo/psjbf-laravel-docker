<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-maroon shadow-sm fixed-top">
    <div class="container-fluid">
      <RouterLink to="/" class="navbar-brand d-flex align-items-center">
        <i class="bi bi-bank fs-4 me-2"></i>
        <span class="fw-bold">Paróquia São João Baptista do Fomento</span>
      </RouterLink>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarContent"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav ms-auto align-items-center gap-3">
          <!-- Ícone de Orações -->
        <li v-if="user.role === 'sacerdote'" class="nav-item position-relative">
          <RouterLink to="/sacerdote/oracoes" class="nav-link">
            <i class="bi bi-journal-text fs-5"></i>
            <span v-if="oracoesCount.value > 0" class="badge bg-warning position-absolute top-0 start-100 translate-middle">
              {{ oracoesCount.value }}
            </span>
          </RouterLink>
        </li>

          <!-- Notificações -->
          <li class="nav-item position-relative">
            <RouterLink to="/avisos" class="nav-link">
              <i class="bi bi-bell fs-5"></i>
              <span v-if="avisosCount.value > 0" class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                {{ avisosCount.value }}
              </span>
            </RouterLink>
          </li>

          <!-- Perfil do Usuário -->
          <li class="nav-item dropdown" ref="dropdownRef">
            <a
              class="nav-link dropdown-toggle d-flex align-items-center"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <img :src="user.photo" alt="avatar" class="rounded-circle me-2" width="32" height="32" />
              {{ user.name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
              <li>
                <RouterLink class="dropdown-item" to="/PerfilUser">Perfil</RouterLink>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button class="dropdown-item text-danger" @click="logout">Sair</button>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Dropdown from 'bootstrap/js/dist/dropdown' // Importa o JS do Bootstrap

const router = useRouter()

const dropdownRef = ref(null)


const oracoesCount = reactive({ value: 0 })

const fetchOracoes = async () => {
  try {
    const response = await axios.get('/pedir-oracao')
    oracoesCount.value = response.data.length || 0
  } catch (error) {
    console.warn('Erro ao buscar orações:', error)
  }
}

const userData = JSON.parse(localStorage.getItem('user')) || {}
const user = reactive({
  name: userData?.nome || 'Usuário',
  photo: userData?.foto || '/default-user.png',
  role: userData?.role || '',
})

const avisosCount = reactive({ value: 0 })

const fetchAvisos = async () => {
  try {
    const response = await axios.get('/avisos')
    avisosCount.value = response.data.total_nao_lidos || 0
  } catch (error) {
    console.warn('Erro ao buscar avisos:', error)
  }
}


const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push({ name: 'Login' })
}


onMounted(() => {
  fetchAvisos()
  fetchOracoes()
  // Inicializa o dropdown manualmente
  if (dropdownRef.value) {
    new Dropdown(dropdownRef.value.querySelector('[data-bs-toggle="dropdown"]'))
  }
})

</script>

<style scoped>
.bg-maroon {
  background-color: #8b0000;
}
.navbar-brand span {
  font-size: 1.1rem;
}
.badge {
  font-size: 0.65rem;
}
</style>
