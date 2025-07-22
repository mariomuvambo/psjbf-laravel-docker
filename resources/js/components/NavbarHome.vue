<template>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
      <!-- Logo da Marca -->
      <router-link class="navbar-brand d-flex align-items-center" to="/">
        <img 
          src="../assets/images/img-1.jpg" 
          alt="Logo Igreja" 
          class="logo-img me-2"
        />
        <span class="brand-name">PSJBF</span>
      </router-link>

      <!-- Botão Responsivo -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarResponsive"
        aria-controls="navbarResponsive"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Links do Menu -->
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/login">Aceder</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/register">Registar</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavbarHome",
};
</script>

<style scoped>
/* Navbar Principal */
.navbar-custom {
  background-color: #8b0000; /* Cor vinho */
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  padding: 0.5rem 0;
}

/* Estilo da Marca */
.navbar-brand {
  font-family: 'Georgia', serif;
  font-size: 1.5rem;
  font-weight: bold;
  color: #fff;
}

.brand-name {
  color: #fff;
}

.logo-img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #fff;
  padding: 3px;
}

/* Links de Navegação */
.nav-link {
  color: #fff;
  font-weight: bold;
  text-transform: uppercase;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: #ffdd00; /* Dourado */
}

/* Botão Responsivo */
.navbar-toggler {
  border: none;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba%280, 0, 0, 0.5%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

/* Responsividade */
@media (max-width: 768px) {
  .brand-name {
    font-size: 1.2rem;
  }

  .logo-img {
    width: 30px;
    height: 30px;
  }

  .nav-link {
    font-size: 0.9rem;
  }
}
</style>
