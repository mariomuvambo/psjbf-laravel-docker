<template>
  <aside v-if="sidebarOpen" class="sidebar text-white p-3 d-flex flex-column position-fixed z-2">

    
    <!-- Cabeçalho -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="d-flex align-items-center">
        <i class="bi bi-bank fs-4 me-2 text-white"></i>
        <span class="fs-5 fw-bold">Painel da Igreja</span>
      </div>
      <button class="btn btn-sm btn-close-white d-lg-none" @click="$emit('toggle-sidebar')"></button>
    </div>

    <!-- Switch Modo Escuro -->
    <div class="form-check form-switch mb-3">
      <input
        class="form-check-input"
        type="checkbox"
        id="darkModeSwitch"
        v-model="isDarkModeLocal"
        @change="emitDarkMode"
      />
      <label class="form-check-label" for="darkModeSwitch">Modo Escuro</label>
    </div>

    <!-- Menu Principal -->
    <ul class="nav nav-pills flex-column mb-auto smooth-scroll">
      <li class="nav-item">
        <RouterLink
          class="nav-link text-white"
          :class="navActive('/dashboard')"
          to="/dashboard"
        >
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </RouterLink>
      </li>

      <li
        v-for="item in navItemsFiltered"
        :key="item.label"
        class="nav-item"
      >
        <RouterLink
          class="nav-link text-white"
          :class="navActive(item.path)"
          :to="item.path"
        >
          <i :class="`bi ${item.icon} me-2`"></i> {{ item.label }}
        </RouterLink>
      </li>

      <!-- Dropdowns -->
      <li
        v-for="(menu, index) in filteredDropdownMenus"
        :key="menu.label"
        class="nav-item mt-2"
      >
        <div
          @click="toggleDropdown(index)"
          class="nav-link text-white d-flex justify-content-between align-items-center"
          style="cursor: pointer;"
        >
          <div>
            <i :class="`bi ${menu.icon} me-2`"></i> {{ menu.label }}
            <span v-if="menu.label === 'Avisos' && avisosCount > 0" class="badge bg-danger ms-1">
              {{ avisosCount }}
            </span>
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

      <!-- Finanças (somente admin) -->
      <li v-if="user.role === 'admin'" class="nav-item mt-2">
        <RouterLink
          class="nav-link text-white"
          :class="navActive('/dashboard/financeiro')"
          to="/dashboard/financeiro"
        >
          <i class="bi bi-cash-coin me-2"></i> Finanças
        </RouterLink>
      </li>
    </ul>

    <!-- Usuário -->
    <hr class="mt-auto" />
    <div class="d-flex align-items-center position-relative user-dropdown-container">
      <img
        :src="user.photo"
        class="rounded-circle me-2"
        width="40"
        height="40"
        alt="Avatar"
      />
      <div class="position-relative w-100">
        <a
          href="#"
          class="text-white text-decoration-none d-block"
          @click.prevent="toggleUserMenu"
        >
          {{ user.name }}
          <i class="bi" :class="showUserMenu ? 'bi-caret-up-fill' : 'bi-caret-down-fill'"></i>
        </a>

        <transition name="fade">
          <ul
            v-show="showUserMenu"
            class="dropdown-menu show position-absolute dropdown-up"
          >
            <li>
              <RouterLink class="dropdown-item text-white" to="/dashboard/perfil">
                Perfil
              </RouterLink>
            </li>
            <li><hr class="dropdown-divider bg-light" /></li>
            <li>
              <a class="dropdown-item text-danger" href="#" @click="logout">Sair</a>
            </li>
          </ul>
        </transition>
      </div>
    </div>
  </aside>
</template>

<script>
import axios from "axios";

export default {
  name: "SidebarDashboard",
  props: {
    sidebarOpen: Boolean,
    isDarkMode: Boolean,
  },
  emits: ["toggle-sidebar", "toggle-dark-mode"],
  data() {
    return {
      isDarkModeLocal: this.isDarkMode,
      openDropdown: null,
      showUserMenu: false,
      avisosCount: 0,
      userData: this.getUserFromStorage(),

      navItems: [
        { label: "Missas", path: "/dashboard/missas", icon: "bi-alarm" },
        { label: "Leituras", path: "/dashboard/leituras", icon: "bi-book", adminOnly: true },
        { label: "Usuários", path: "/dashboard/admin/usuarios", icon: "bi-people", adminOnly: true },
        { label: "Aniversariantes", path: "/dashboard/aniversariantes", icon: "bi-cake" },
        { label: "Doações", path: "/dashboard/doacoes", icon: "bi-gift" },
      ],

      dropdownMenus: [
        {
          label: "Eventos",
          icon: "bi-calendar-event",
          subItems: [
            { label: "Lista", path: "/dashboard/eventos" },
            { label: "Criar", path: "/dashboard/eventos/create", adminOnly: true },
          ],
        },
        {
          label: "Avisos",
          icon: "bi-megaphone",
          subItems: [
            { label: "Lista", path: "/dashboard/avisos" },
            { label: "Adicionar", path: "/dashboard/avisos/registo", adminOnly: true },
          ],
        },
        {
          label: "Sacramentos",
          icon: "bi-droplet-half",
          subItems: [
            { label: "Batismo", path: "/dashboard/batismo/register" },
            { label: "Casamento", path: "/dashboard/casamento/register" },
          ],
        },
        {
          label: "Ministérios",
          icon: "bi-person-badge",
          subItems: [
            { label: "Lista", path: "/dashboard/userMinister" },
            { label: "Adicionar", path: "/dashboard/ministerio", adminOnly: true },
          ],
        },
        {
          label: "Sacerdote",
          icon: "bi-person-hearts",
          onlySacerdote: true,
          subItems: [
            { label: "Painel", path: "/dashboard/sacerdote/painel" },
            { label: "Batismos", path: "/dashboard/sacerdote/batismos/todos" },
            { label: "Casamentos", path: "/dashboard/sacerdote/casamentos" },
            { label: "Orações", path: "/dashboard/sacerdote/oracoes" },
          ],
        },
      ],
    };
  },
  computed: {
    user() {
      return {
        name: this.userData?.nome || "Usuário",
        role: this.userData?.role || "",
        photo: this.userData?.foto || "/default-user.png",
      };
    },
    navItemsFiltered() {
      return this.navItems.filter(
        (item) => !item.adminOnly || this.user.role === "admin"
      );
    },
    filteredDropdownMenus() {
      return this.dropdownMenus.filter((menu) => {
        if (menu.onlySacerdote) {
          return this.user.role === "sacerdote" || this.user.role === "admin";
        }
        return true;
      });
    },
  },
  methods: {
    toggleDropdown(index) {
      this.openDropdown = this.openDropdown === index ? null : index;
    },
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu;
    },
    emitDarkMode() {
      this.$emit("toggle-dark-mode");
      localStorage.setItem("darkMode", this.isDarkModeLocal);
    },
    getUserFromStorage() {
      try {
        const user = localStorage.getItem("user");
        return user ? JSON.parse(user) : null;
      } catch {
        return null;
      }
    },
    async fetchAvisosCount() {
      try {
        const res = await axios.get("/avisos");
        // this.avisosCount = res.data.total_nao_lidos || 0;
      } catch {
        console.error("Erro ao buscar avisos");
      }
    },
    logout() {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      this.$router.push("/login");
    },
    navActive(path) {
      return this.$route.path.startsWith(path)
        ? "active text-warning fw-bold"
        : "text-white";
    },
  },
  mounted() {
    this.fetchAvisosCount();
  },
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  height: 100vh;
  background-color: #8b0000;
  overflow-y: auto;
}
.nav-link.active {
  background-color: rgba(255, 255, 255, 0.2);
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* --- Sidebar responsiva --- */
@media (max-width: 991px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 250px;
    background-color: #8b0000;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 1050;
  }

  /* Sidebar visível quando aberta */
  .sidebar.show {
    transform: translateX(0);
  }
}

</style>
