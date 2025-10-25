<template>
  <div :class="['dashboard-layout d-flex', { 'dark-mode': isDarkMode }]">
    <!-- Sidebar -->
    <SidebarDashboard
      :sidebar-open="sidebarOpen"
      :is-dark-mode="isDarkMode"
      @toggle-sidebar="toggleSidebar"
      @toggle-dark-mode="toggleDarkMode"
    />

    <!-- Conteúdo Principal -->
    <div class="main-content flex-grow-1">
      <!-- Topbar -->
      <nav class="navbar navbar-expand-lg bg-light shadow-sm sticky-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
          <!-- Botão Mobile -->
          <button
            class="btn btn-outline-dark d-lg-none me-2"
            type="button"
            @click="toggleSidebar"
          >
            <i class="bi bi-list fs-4"></i>
          </button>

          <div class="d-flex align-items-center">
            <i class="bi bi-bank fs-4 me-2 text-danger"></i>
            <span class="fw-bold">Painel da Igreja</span>
          </div>

          <!-- Botão Modo Escuro -->
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="toggleDarkMode"
          >
            <i :class="isDarkMode ? 'bi bi-brightness-high-fill' : 'bi bi-moon-stars-fill'"></i>
          </button>
        </div>
      </nav>

      <!-- Área de Conteúdo -->
      <main class="p-4">
        <RouterView />
      </main>
    </div>

    <!-- Overlay Mobile -->
    <transition name="fade">
      <div
        v-if="sidebarOpen && isMobile"
        class="sidebar-overlay"
        @click="toggleSidebar"
      ></div>
    </transition>
  </div>
</template>

<script>
import SidebarDashboard from "@/components/SidebarDashboard.vue";

export default {
  name: "DashboardLayout",
  components: { SidebarDashboard },
  data() {
    return {
      sidebarOpen: window.innerWidth >= 992, // aberto por padrão no desktop
      isDarkMode: localStorage.getItem("darkMode") === "true",
      isMobile: window.innerWidth < 992,
    };
  },
  mounted() {
    window.addEventListener("resize", this.handleResize);
  },
  unmounted() {
    window.removeEventListener("resize", this.handleResize);
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    toggleDarkMode() {
      this.isDarkMode = !this.isDarkMode;
      localStorage.setItem("darkMode", this.isDarkMode);
    },
    handleResize() {
      this.isMobile = window.innerWidth < 992;
      if (!this.isMobile) this.sidebarOpen = true;
    },
  },
};
</script>

<style scoped>
.dashboard-layout {
  height: 100vh;
  overflow: hidden;
  background-color: #f8f9fa;
  transition: background-color 0.3s ease;
}

.dark-mode {
  background-color: #222;
  color: #fff;
}

.main-content {
  flex-grow: 1;
  overflow-y: auto;
  transition: margin-left 0.3s ease;
  margin-left: 250px;
}

/* Mobile comportamento */
@media (max-width: 991px) {
  .main-content {
    margin-left: 0;
  }
}

/* Overlay escuro para mobile */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1;
}

/* Transição do overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
