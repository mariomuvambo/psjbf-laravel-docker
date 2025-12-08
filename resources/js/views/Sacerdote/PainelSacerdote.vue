<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar lateral -->
    <SidebarDashboard class="d-none d-lg-block bg-white shadow-sm border-end" style="min-width: 250px;" />

    <!-- Conteúdo principal -->
    <main class="flex-grow-1">
      <NavDashboard />

      <div class="container mt-4">
        <!-- Boas-vindas -->
        <div class="text-center mb-5">
          <h1 class="text-danger fw-bold">Painel do Sacerdote</h1>
          <p class="text-muted">Bem-vindo, padre. Abaixo estão os acessos rápidos para gerir os registros de batismo.</p>
        </div>

        <!-- Acesso Rápido -->
        <div class="row g-4">

           <!-- Todos os Batismos -->
          <div class="col-md-3">
            <div class="card border-secondary h-100 shadow-sm">
              <div class="card-body text-center">
                <i class="bi bi-people text-secondary fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Todos os Batismos</h5>
                <p class="card-text text-muted">Histórico completo dos baptismos.</p>
                <router-link to="/dashboard/sacerdote/batismos/todos" class="btn btn-secondary mt-2">Ver Todos</router-link>
              </div>
            </div>
          </div>

          
          <!-- Batismos Aprovados -->
          <div class="col-md-3">
            <div class="card border-success h-100 shadow-sm">
              <div class="card-body text-center">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Aprovados</h5>
                <p class="card-text text-muted">Baptismos aprovados.</p>
                <router-link to="/dashboard/sacerdote/batismos/aprovados" class="btn btn-success mt-2">Ver Lista</router-link>
              </div>
            </div>
          </div>

          <!-- Pedidos Pendentes -->
          <div class="col-md-3">
            <div class="card border-warning h-100 shadow-sm">
              <div class="card-body text-center">
                <i class="bi bi-hourglass-split text-warning fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Pendentes</h5>
                <p class="card-text text-muted">Aprove ou rejeite os pedidos.</p>
                <router-link to="/dashboard/sacerdote/batismos/pendentes" class="btn btn-warning mt-2 text-white">Gerenciar</router-link>
              </div>
            </div>
          </div>

          <!-- Em Análise -->
          <div class="col-md-3">
            <div class="card border-info h-100 shadow-sm">
              <div class="card-body text-center">
                <i class="bi bi-search text-info fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Em Análise</h5>
                <p class="card-text text-muted">Batismos em processo de verificação.</p>
                <router-link to="/dashboard/sacerdote/batismos/em-analise" class="btn btn-info mt-2 text-white">Visualizar</router-link>
              </div>
            </div>
          </div>

       

         

             <!-- Certificados -->
          <div class="col-md-3">
            <div class="card border-primary h-100 shadow-sm">
              <div class="card-body text-center">
                <i class="bi bi-printer text-primary fs-1"></i>
                <h5 class="card-title mt-3 fw-bold">Certificados</h5>
                <p class="card-text text-muted">Imprima certificados aprovados.</p>
                <router-link to="/dashboard/sacerdote/certificados" class="btn btn-primary mt-2">Imprimir</router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Versículo -->
        <div class="mt-5 text-center text-muted">
          <small>“Deixai vir a mim os pequeninos, porque deles é o Reino dos Céus.” – <em>Mateus 19:14</em></small>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import NavDashboard from '../../components/NavDashboard.vue'
import SidebarDashboard from '../../components/SidebarDashboard.vue'
</script>
