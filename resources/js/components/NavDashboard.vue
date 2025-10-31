<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-maroon shadow-sm fixed-top">
    <div class="container-fluid">
      <!-- Marca -->
      <RouterLink to="/" class="navbar-brand d-flex align-items-center">
        <i class="bi bi-bank fs-4 me-2"></i>
        <span class="fw-bold">Paróquia São João Baptista do Fomento</span>
      </RouterLink>

      <!-- Botão de menu para mobile (abre offcanvas) -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar"
        aria-controls="mobileSidebar"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu desktop normal -->
      <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarContent">
        <ul class="navbar-nav ms-auto align-items-center gap-3">
          <!-- Ícone de Orações -->
          <li v-if="user.role === 'sacerdote'" class="nav-item position-relative">
            <RouterLink
              :to="`/dashboard/sacerdote/oracoes`"
              class="nav-link"
              :class="linkActiveClass('/dashboard/sacerdote/oracoes')"
            >
              <i class="bi bi-journal-text fs-5"></i>
              <span
                v-if="oracoesCount > 0"
                class="badge bg-warning position-absolute top-0 start-100 translate-middle"
              >
                {{ oracoesCount }}
              </span>
            </RouterLink>
          </li>

          <!-- Notificações -->
          <li class="nav-item position-relative">
            <RouterLink
              to="/dashboard/avisos"
              class="nav-link"
              :class="linkActiveClass('/dashboard/avisos')"
            >
              <i class="bi bi-bell fs-5"></i>
              <span
                v-if="avisosCount > 0"
                class="badge bg-danger position-absolute top-0 start-100 translate-middle"
              >
                {{ avisosCount }}
              </span>
            </RouterLink>
          </li>

          <!-- Perfil do Usuário (dropdown bootstrap) -->
          <li class="nav-item dropdown" ref="dropdownRef">
            <a
              class="nav-link dropdown-toggle d-flex align-items-center"
              href="#"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <img
                :src="user.photo"
                alt="avatar"
                class="rounded-circle me-2"
                width="32"
                height="32"
              />
              {{ user.name }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
              <li>
                <RouterLink class="dropdown-item" to="/dashboard/perfil">
                  Perfil
                </RouterLink>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button class="dropdown-item text-danger" @click="logout">
                  Sair
                </button>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- === MENU LATERAL MOBILE (OFFCANVAS) === -->
  <div
    class="offcanvas offcanvas-start bg-maroon text-white"
    tabindex="-1"
    id="mobileSidebar"
    aria-labelledby="mobileSidebarLabel"
    ref="mobileSidebarRef"
  >
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">
        Menu Paroquial
      </h5>
      <button
        type="button"
        class="btn-close btn-close-white text-reset"
        data-bs-dismiss="offcanvas"
        aria-label="Close"
      ></button>
    </div>

    <div class="offcanvas-body">
      <!-- Reproduzimos os mesmos itens do Sidebar (incluindo submenus) -->
      <ul class="nav flex-column gap-2">
        <li>
          <a
            href="#"
            class="nav-link text-white"
            :class="linkActiveClass('/dashboard')"
            @click.prevent="navigate('/dashboard')"
          >
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
          </a>
        </li>

        <!-- itens simples -->
        <li v-for="item in navItems" :key="item.label">
          <template v-if="!item.adminOnly || user.role === 'admin'">
            <a
              href="#"
              class="nav-link text-white"
              :class="linkActiveClass(item.path)"
              @click.prevent="navigate(item.path)"
            >
              <i :class="`bi ${item.icon} me-2`"></i> {{ item.label }}
            </a>
          </template>
        </li>

        <!-- dropdown menus com subitems -->
        <li v-for="(menu, idx) in filteredDropdownMenus" :key="menu.label" class="mt-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <i :class="`bi ${menu.icon} me-2`"></i> {{ menu.label }}
              <span v-if="menu.label === 'Avisos' && avisosCount > 0" class="badge bg-danger ms-1">
                {{ avisosCount }}
              </span>
            </div>
            <button
              class="btn btn-sm btn-outline-light"
              type="button"
              @click="toggleMenu(idx)"
              :aria-expanded="openMobileMenu === idx"
            >
              <i :class="openMobileMenu === idx ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
            </button>
          </div>

          <ul v-show="openMobileMenu === idx" class="ps-3 mt-2">
            <li v-for="sub in menu.subItems" :key="sub.label" class="mb-1">
              <template v-if="!sub.adminOnly || user.role === 'admin'">
                <a
                  href="#"
                  class="nav-link ps-4 text-white"
                  @click.prevent="navigate(sub.path)"
                >
                  <i class="bi bi-chevron-right me-2"></i> {{ sub.label }}
                </a>
              </template>
            </li>
          </ul>
        </li>

        <!-- Finanças (somente admin) -->
        <li v-if="user.role === 'admin'" class="mt-3">
          <a
            href="#"
            class="nav-link text-white"
            :class="linkActiveClass('/dashboard/financeiro')"
            @click.prevent="navigate('/dashboard/financeiro')"
          >
            <i class="bi bi-cash-coin me-2"></i> Finanças
          </a>
        </li>

        <!-- botao sair -->
        <li class="mt-3">
          <button class="btn btn-outline-light w-100" @click="logout">
            <i class="bi bi-box-arrow-right me-2"></i> Sair
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Dropdown from 'bootstrap/js/dist/dropdown'
import Offcanvas from 'bootstrap/js/dist/offcanvas'

// --- Router / route
const router = useRouter()
const route = useRoute()

// --- Refs para bootstrap
const dropdownRef = ref(null)
const mobileSidebarRef = ref(null)
let mobileOffcanvasInstance = null

// --- estado
const oracoesCount = ref(0)
const avisosCount = ref(0)
const openMobileMenu = ref(null)

// --- user
const userData = JSON.parse(localStorage.getItem('user')) || {}
const user = reactive({
  name: userData?.nome || 'Usuário',
  photo: userData?.foto_url || userData?.foto || '/default-user.png',
  role: userData?.role || '',
})


// --- dados de menu (idos os mesmos do Sidebar)
const navItems = [
  { label: 'Missas', path: '/dashboard/missas', icon: 'bi-alarm' },
  { label: 'Leituras', path: '/dashboard/leituras', icon: 'bi-book', adminOnly: true },
  { label: 'Usuários', path: '/dashboard/admin/usuarios', icon: 'bi-people', adminOnly: true },
  { label: 'Aniversariantes', path: '/dashboard/aniversariantes', icon: 'bi-cake' },
  { label: 'Doações', path: '/dashboard/doacoes', icon: 'bi-gift' },
]

const dropdownMenus = [
  {
    label: 'Eventos',
    icon: 'bi-calendar-event',
    subItems: [
      { label: 'Lista', path: '/dashboard/eventos' },
      { label: 'Criar', path: '/dashboard/eventos/create', adminOnly: true },
    ],
  },
  {
    label: 'Avisos',
    icon: 'bi-megaphone',
    subItems: [
      { label: 'Lista', path: '/dashboard/avisos' },
      { label: 'Adicionar', path: '/dashboard/avisos/registo', adminOnly: true },
    ],
  },
  {
    label: 'Sacramentos',
    icon: 'bi-droplet-half',
    subItems: [
      { label: 'Batismo', path: '/dashboard/batismo/register' },
      { label: 'Casamento', path: '/dashboard/casamento/register' },
    ],
  },
  {
    label: 'Ministérios',
    icon: 'bi-person-badge',
    subItems: [
      { label: 'Lista', path: '/dashboard/userMinister' },
      { label: 'Adicionar', path: '/dashboard/ministerio', adminOnly: true },
    ],
  },
  {
    label: 'Sacerdote',
    icon: 'bi-person-hearts',
    onlySacerdote: true,
    subItems: [
      { label: 'Painel', path: '/dashboard/sacerdote/painel' },
      { label: 'Batismos', path: '/dashboard/sacerdote/batismos/todos' },
      { label: 'Casamentos', path: '/dashboard/sacerdote/casamentos' },
      { label: 'Orações', path: '/dashboard/sacerdote/oracoes' },
    ],
  },
]

const filteredDropdownMenus = computed(() =>
  dropdownMenus.filter(menu => {
    if (menu.onlySacerdote) {
      return user.role === 'sacerdote' || user.role === 'admin'
    }
    return true
  })
)

// --- funções auxiliares
const fetchOracoes = async () => {
  try {
    const res = await axios.get('/pedir-oracao')
    oracoesCount.value = Array.isArray(res.data) ? res.data.length : (res.data?.length || 0)
  } catch (e) {
    console.warn('Erro ao buscar orações', e)
  }
}

const fetchAvisos = async () => {
  try {
    const res = await axios.get('/avisos')
    avisosCount.value = res.data.total_nao_lidos || 0
  } catch (e) {
    console.warn('Erro ao buscar avisos', e)
  }
}

const logout = () => {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('user')
  router.push({ name: 'Login' })
}

// fecha offcanvas (se estiver aberto) e navega
const navigate = (path) => {
  // fechar offcanvas mobile
  if (mobileOffcanvasInstance) {
    try {
      mobileOffcanvasInstance.hide()
    } catch (e) {
      // ignora
    }
  }
  // pequena guarda para caminhos
  router.push(path).catch(() => {})
}

// marca link ativo (utilizado para adicionar classes)
const linkActiveClass = (path) => {
  return route.path.startsWith(path) ? 'active text-warning fw-bold' : ''
}

const toggleMenu = (idx) => {
  openMobileMenu.value = openMobileMenu.value === idx ? null : idx
}

onMounted(() => {
  fetchAvisos()
  fetchOracoes()

  // inicializar dropdown do perfil (desktop)
  if (dropdownRef.value) {
    new Dropdown(dropdownRef.value.querySelector('[data-bs-toggle="dropdown"]') || dropdownRef.value)
  }

  // inicializar offcanvas (para poder fechar via JS)
  if (mobileSidebarRef.value) {
    mobileOffcanvasInstance = new Offcanvas(mobileSidebarRef.value)
  }
})
</script>

<style scoped>
.bg-maroon {
  background-color: #8b0000;
}
.navbar-brand span {
  font-size: 1.0rem;
}
.badge {
  font-size: 0.65rem;
}

/* estilos do offcanvas */
.offcanvas-body .nav-link {
  font-size: 1.05rem;
  font-weight: 500;
  transition: background 0.15s;
}
.offcanvas-body .nav-link:hover {
  background: rgba(255, 255, 255, 0.06);
  border-radius: 8px;
}

/* destaque visual para active no offcanvas */
.offcanvas-body .active {
  color: #f39c12 !important;
}

/* pequenas melhorias mobile spacing */
.offcanvas .nav {
  padding-top: 0.3rem;
}
</style>
