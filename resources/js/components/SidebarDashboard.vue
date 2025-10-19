<template>
  <div :class="['d-flex', { 'dark-mode': isDarkMode }]">
    <!-- Botão Mobile -->
    <button class="btn btn-light position-fixed top-0 start-0 m-2 d-lg-none z-3" @click="toggleSidebar">
      <i class="bi bi-list fs-3"></i>
    </button>

    <!-- Sidebar -->
    <transition name="slide">
      <aside v-if="sidebarOpen" class="sidebar text-white p-3 d-flex flex-column position-fixed z-2">
        <!-- Cabeçalho -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="d-flex align-items-center">
            <i class="bi bi-bank fs-4 me-2 text-white"></i>
            <span class="fs-5 fw-bold">Painel da Igreja</span>
          </div>
          <button class="btn btn-sm btn-close-white d-lg-none" @click="toggleSidebar"></button>
        </div>

        <!-- Switch Modo Escuro -->
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" id="darkModeSwitch" v-model="isDarkMode" @change="saveDarkMode">
          <label class="form-check-label" for="darkModeSwitch">Modo Escuro</label>
        </div>

        <!-- Menu Principal -->
        <ul class="nav nav-pills flex-column mb-auto smooth-scroll">
          <li class="nav-item">
            <RouterLink class="nav-link text-white" :class="navActive('/dashboard')" to="/dashboard">
              <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </RouterLink>
          </li>

          <li v-for="item in navItemsFiltered" :key="item.label" class="nav-item">
            <RouterLink class="nav-link text-white" :class="navActive(item.path)" :to="item.path">
              <i :class="`bi ${item.icon} me-2`"></i> {{ item.label }}
            </RouterLink>
          </li>

          <!-- Dropdowns -->
          <li v-for="(menu, index) in filteredDropdownMenus" :key="menu.label" class="nav-item mt-2">
            <div @click="toggleDropdown(index)" class="nav-link text-white d-flex justify-content-between align-items-center" style="cursor: pointer;">
              <div>
                <i :class="`bi ${menu.icon} me-2`"></i> {{ menu.label }}
                <span v-if="menu.label === 'Avisos' && avisosCount > 0" class="badge bg-danger ms-1">{{ avisosCount }}</span>
              </div>
              <i :class="openDropdown === index ? 'bi bi-chevron-up' : 'bi bi-chevron-down'"></i>
            </div>
            <ul v-show="openDropdown === index" class="ps-3 submenu">
              <li v-for="sub in menu.subItems" :key="sub.label">
                <RouterLink
                  v-if="!sub.adminOnly || user.role === 'admin'"
                  class="nav-link text-white ps-4"
                  :to="sub.path"
                >
                  <i class="bi bi-chevron-right me-1"></i> {{ sub.label }}
                </RouterLink>
              </li>
            </ul>
          </li>

          <!-- Finanças (admin only) -->
          <li v-if="user.role === 'admin'" class="nav-item mt-2">
            <RouterLink class="nav-link text-white" :class="navActive('/FinancialHistory')" to="/FinancialHistory">
              <i class="bi bi-cash-coin me-2"></i> Finanças
            </RouterLink>
          </li>
        </ul>

        <!-- Usuário -->
        <hr class="mt-auto" />
        <div class="d-flex align-items-center position-relative user-dropdown-container">
          <img :src="user.photo" class="rounded-circle me-2" width="40" height="40" alt="Avatar" />
          <div class="position-relative w-100">
            <a href="#" class="text-white text-decoration-none d-block" @click.prevent="toggleUserMenu">
              {{ user.name }}
              <i class="bi" :class="showUserMenu ? 'bi-caret-up-fill' : 'bi-caret-down-fill'"></i>
            </a>
            <transition name="fade">
              <ul v-show="showUserMenu" class="dropdown-menu show position-absolute dropdown-up">
                <li><RouterLink class="dropdown-item text-white" to="/PerfilUser">Perfil</RouterLink></li>
                <li><hr class="dropdown-divider bg-light" /></li>
                <li><a class="dropdown-item text-danger" href="#" @click="logout">Sair</a></li>
              </ul>
            </transition>
          </div>
        </div>
      </aside>
    </transition>

    <!-- Conteúdo -->
    <main :class="['flex-grow-1 p-4 content-wrapper', isDarkMode ? 'bg-dark text-white' : 'bg-light']" :style="{ marginLeft: sidebarOpen ? '250px' : '0' }">
      <router-view />
    </main>
  </div>
</template>


<script>
import axios from 'axios';

export default {
  name: 'SidebarDashboard',
  data() {
    return {
      sidebarOpen: true,
      isDarkMode: false,
      openDropdown: null,
      showUserMenu: false,
      userData: this.getUserFromStorage(),
      avisosCount: 0,
      navItems: [
        { label: 'Missas', path: '/Missas', icon: 'bi-alarm', adminOnly: false },
        { label: 'Leituras', path: '/Leituras', icon: 'bi-book', adminOnly: true },
        { label: 'Usuários', path: '/admin/usuarios', icon: 'bi-people', adminOnly: true },
        { label: 'Aniversariantes', path: '/aniversariantes', icon: 'bi-cake', adminOnly: false },
        { label: 'Doações', path: '/doacoes', icon: 'bi-gift', adminOnly: false },
      ],
      dropdownMenus: [
        {
          label: 'Eventos',
          icon: 'bi-calendar-event',
          subItems: [
            { label: 'Lista', path: '/Eventos' },
            { label: 'Criar', path: '/CreateEvent', adminOnly: true },
          ],
        },
        {
          label: 'Avisos',
          icon: 'bi-megaphone',
          subItems: [
            { label: 'Lista', path: '/avisos' },
            { label: 'Adicionar', path: '/avisos/registo', adminOnly: true },
          ],
        },
        {
          label: 'Sacramentos',
          icon: 'bi-droplet-half',
          subItems: [
            { label: 'Batismo', path: '/Batismo/Register' },
            { label: 'Casamento', path: '/casamento/Register' },
          ],
        },
        {
          label: 'Ministérios',
          icon: 'bi-person-badge',
          subItems: [
            { label: 'Registar', path: '/userMinister' },
            { label: 'Adicionar', path: '/Ministerio', adminOnly: true },
          ],
        },
        {
          label: 'Sacerdote',
          icon: 'bi-person-hearts',
          onlySacerdote: true,
          subItems: [
            { label: 'Baptismo', path: '/Painel/Sacerdote' },
            { label: 'Casamentos', path: '/painel/casamento' },
          ],
        },
      ],
    };
  },
  computed: {
    user() {
      return {
        name: this.userData?.nome || 'Usuário',
        role: this.userData?.role || '',
        photo: this.userData?.foto || '/default-user.png',
      };
    },
    navItemsFiltered() {
      return this.navItems.filter(item => {
        return !item.adminOnly || this.user.role === 'admin';
      });
    },
    filteredDropdownMenus() {
      return this.dropdownMenus.filter(menu => {
        if (menu.onlySacerdote) {
          return this.user.role === 'sacerdote' || this.user.role === 'admin';
        }
        return true;
      });
    },
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
      localStorage.setItem('sidebarOpen', this.sidebarOpen);
    },
    toggleDropdown(index) {
      this.openDropdown = this.openDropdown === index ? null : index;
    },
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },
    getUserFromStorage() {
      try {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
      } catch {
        return null;
      }
    },
    saveDarkMode() {
      localStorage.setItem('darkMode', this.isDarkMode);
    },
    async fetchAvisosCount() {
      try {
        const res = await axios.get('/avisos');
        // this.avisosCount = res.data.total_nao_lidos || 0;
      } catch (e) {
        console.error('Erro ao buscar avisos');
      }
    },
    logout() {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
      this.$router.push({ name: 'Login' });
    },
    navActive(path) {
      return this.$route.path.startsWith(path) ? 'active text-warning fw-bold' : 'text-white';
    },
  },
  mounted() {
    this.sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
    this.isDarkMode = localStorage.getItem('darkMode') === 'true';
    this.fetchAvisosCount();
  },
};
</script>


<style scoped>
.bg-maroon {
  background-color: #8b0000;
}

.sidebar {
  width: 250px;
  height: 100vh;
  overflow-y: auto;
  background-color: #8b0000;
  transition: all 0.3s ease-in-out;
}

.nav-link.active {
  background-color: rgba(255, 255, 255, 0.2);
}

.nav-link:hover {
  color: #ffdd00 !important;
}

.dropdown-item:hover {
  background-color: #ffdd00;
  color: #8b0000;
}

.content-wrapper {
  transition: margin-left 0.3s ease-in-out;
}

.smooth-scroll {
  scroll-behavior: smooth;
}

.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from, .slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

.submenu {
  list-style: none;
  padding-left: 0.75rem;
}

.submenu .nav-link {
  font-size: 0.9rem;
  padding: 0.3rem 0;
  display: block;
}

.user-dropdown-container {
  padding-bottom: 2rem;
}

.dropdown-up {
  bottom: 100%;
  margin-bottom: 0.5rem;
  background-color: #333;
  z-index: 999;
  width: 100%;
  padding: 0.5rem 0;
  border-radius: 0.25rem;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.dropdown-up .dropdown-item {
  color: #fff;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* DARK MODE */
.dark-mode {
  background-color: #1e1e1e;
  color: #fff;
}

.dark-mode .bg-light {
  background-color: #2c2c2c !important;
  color: #fff !important;
}

.dark-mode .sidebar {
  background-color: #2b1d1d !important;
}

.dark-mode .nav-link,
.dark-mode .dropdown-item {
  color: #ccc !important;
}

.dark-mode .nav-link:hover,
.dark-mode .dropdown-item:hover {
  background-color: #444 !important;
  color: #fff !important;
}

.dark-mode .dropdown-up {
  background-color: #444;
}

.dark-mode .form-check-label {
  color: #ccc;
}

@media (max-width: 991px) {
  .sidebar {
    width: 220px;
  }

  .content-wrapper {
    margin-left: 0 !important;
  }
}


</style>
