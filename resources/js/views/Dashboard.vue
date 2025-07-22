<template>
  <div>
    <!-- Sidebar Acima (para mobile) -->
    <div class="d-block d-lg-none">
      <SidebarDashboard />
    </div>

    <!-- Layout Principal com Sidebar à esquerda (para telas grandes) -->
    <div class="d-flex">
      <!-- Sidebar lateral (somente telas grandes) -->
      <div class="d-none d-lg-block">
        <SidebarDashboard />
      </div>

      <!-- Conteúdo Principal -->
      <main class="flex-grow-1 bg-light">
        <!-- Navbar -->
        <NavDashboard />

        <!-- Carrossel -->
        <div id="carousel" class="my-4 mx-3">
          <carousel />
        </div>

        <!-- Estatísticas -->
        <section class="container mb-4">
          <div class="row g-3">
            <div class="col-md-6 col-lg-3" v-for="stat in estatisticas" :key="stat.label">
              <div class="card text-white h-100" :class="stat.color">
                <div class="card-body text-center">
                  <h5>{{ stat.label }}</h5>
                  <p class="display-6 fw-bold">{{ stat.valor }}</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Leitura do dia -->
        <section class="container mb-4">
          <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
              📖 Leitura do Dia - {{ evangelho.data }}
            </div>
            <div class="card-body">
              <blockquote class="blockquote">
                <p class="mb-0 fst-italic">"{{ evangelho.versiculo }}"</p>
                <footer class="blockquote-footer">{{ evangelho.referencia }}</footer>
              </blockquote>
            </div>
          </div>
        </section>

        <!-- Avisos e Aniversariantes -->
        <section class="container mb-5">
          <div class="row g-4">
            <!-- Avisos -->
            <div class="col-md-6">
              <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <span>📢 Avisos Paroquiais</span>
                  <router-link to="/avisos" class="btn btn-outline-light btn-sm">Ver todos</router-link>
                </div>
                <div class="card-body">
                  <ul v-if="avisosNaoLidos.length" class="list-unstyled mb-0">
                    <li
                      v-for="aviso in avisosNaoLidos"
                      :key="aviso.id"
                      class="mb-3"
                    >
                      <router-link
                        :to="`/avisos/${aviso.id}`"
                        class="text-decoration-none d-flex justify-content-between align-items-center"
                      >
                        <span class="text-dark fw-semibold">{{ aviso.title }}</span>
                        <small class="text-muted">
                          <i class="bi bi-clock me-1"></i>{{ formatarHora(aviso.hora) }}
                        </small>
                      </router-link>
                    </li>
                  </ul>
                  <p v-else class="text-muted">Nenhum aviso novo.</p>
                </div>
              </div>
            </div>

            <!-- Aniversariantes -->
            <div class="col-md-6">
              <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                  <div>🎉 Aniversariantes do Mês</div>
                  <router-link to="/aniversariantes" class="btn btn-outline-light btn-sm d-flex align-items-center">
                    <i class="bi bi-people-fill me-1"></i> Ver todos
                  </router-link>
                </div>
                <div class="card-body">
                  <ul v-if="aniversariantes.length" class="list-unstyled mb-0">
                    <li
                      v-for="pessoa in aniversariantes"
                      :key="pessoa.id"
                      class="mb-2 d-flex align-items-center"
                    >
                      <i class="bi bi-person-circle text-success me-2 fs-5"></i>
                      <span class="fw-semibold">{{ pessoa.nome }}</span>
                      <span class="ms-auto text-muted small">{{ pessoa.data_nascimento }}</span>
                    </li>
                  </ul>
                  <p v-else class="text-muted mb-0">Nenhum aniversariante neste mês.</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Galeria de Fotos -->
        <section class="container mb-5">
          <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
              <span>📸 Eventos Recentes</span>
              <button @click="irParaPaginaEventos" class="btn btn-sm btn-outline-light">Ver Todos</button>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <div v-for="evento in eventosRecentes" :key="evento.id" class="col-6 col-md-3">
                  <img :src="getImageUrl(evento.image)" class="img-fluid rounded shadow-sm mb-2" :alt="evento.description" />
                  <p class="mb-0 text-truncate" :title="evento.description">{{ evento.description }}</p>
                </div>
              </div>
            </div>
          </div>
        </section>


        <!-- Doações -->
      <section class="container mb-5">
        <div class="card shadow-sm text-center p-4">
          <h4 class="mb-3">💕 Apoie nossa Paróquia</h4>
          <p>As doações mantêm nossas atividades e ajudam os mais necessitados.</p>
          <button class="btn btn-danger" @click="irParaPaginaDoacoes">Fazer uma Doação</button>
        </div>
      </section>


      <!-- Mensagem do Pároco -->
      <section class="container mb-5">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white">🕋️ Mensagem do Pároco</div>
          <div class="card-body">
            <p class="lead">{{ mensagemParoco }}</p>
            <p class="text-muted">Pe. José Antônio - Pároco</p>

            <!-- Pedido de Oração -->
            <hr />
            <h5 class="mt-4">🙏 Envie seu Pedido de Oração</h5>
            <form @submit.prevent="enviarPedidoOracao">
              <div class="mb-3">
                <textarea
                  v-model="novoPedido"
                  class="form-control"
                  placeholder="Digite seu pedido de oração aqui..."
                  rows="4"
                  required
                ></textarea>
              </div>
              <button type="submit" class="btn btn-danger">Enviar Pedido</button>
            </form>
            <div v-if="respostaPedido" class="alert alert-success mt-3" role="alert">
              {{ respostaPedido }}
            </div>
          </div>
        </div>
      </section>


        
        <!-- Rodapé -->
        <footer class="footer mt-auto">
          <div class="footer-container container">
            <div class="footer-section">
              <h3>Igreja São Francisco</h3>
              <p>Paróquia dedicada à fé, caridade e comunidade.</p>
            </div>
            <div class="footer-section">
              <h3>Contato</h3>
              <p>Email: paroquia@igreja.org</p>
              <p>Telefone: (21) 99999-9999</p>
            </div>
            <div class="footer-section">
              <h3>Redes Sociais</h3>
              <div class="social-links">
                <i class="bi bi-facebook fs-4"></i>
                <i class="bi bi-instagram fs-4"></i>
                <i class="bi bi-whatsapp fs-4"></i>
              </div>
            </div>
          </div>
        </footer>
      </main>
    </div>
  </div>
</template>

<script>
import SidebarDashboard from '../components/SidebarDashboard.vue';
import NavDashboard from '../components/NavDashboard.vue';
import carousel from '../components/Carousel.vue';
import axios from 'axios';

export default {
  components: {
    SidebarDashboard,
    NavDashboard,
    carousel,
  },
  data() {
    return {
      avisosNaoLidos: [],
      events: [],
      estatisticas: [
        { label: 'Total de Fiéis', valor: 150, color: 'bg-info' },
        { label: 'Missas Hoje', valor: 3, color: 'bg-success' },
        { label: 'Doações (Julho)', valor: 'R$ 2.400', color: 'bg-warning' },
        { label: 'Eventos Ativos', valor: 5, color: 'bg-danger' },
      ],
      evangelho: {
        data: '07/07/2025',
        versiculo: 'Eu sou o caminho, a verdade e a vida.',
        referencia: 'João 14:6',
      },
      aniversariantes: [],
      novoPedido: '',
      mensagemParoco: 'Queridos irmãos, continuemos unidos na fé e na solidariedade, espalhando o amor de Cristo em nossas ações diárias.',
      respostaPedido: '', // Nova propriedade para feedback
    };
  },
  computed: {
    eventosRecentes() {
      return this.events
        .slice()
        .sort((a, b) => new Date(b.date) - new Date(a.date))
        .slice(0, 2);
    }
  },
  methods: {
    async carregarAniversariantes() {
      try {
        const response = await axios.get('/data_aniversarianteMes');
        this.aniversariantes = response.data;
      } catch (error) {
        console.error('Erro ao carregar aniversariantes:', error);
      }
    },
    async carregarAvisos() {
      try {
        const response = await axios.get('/avisos');
        this.avisosNaoLidos = response.data.avisos_nao_lidos || [];
      } catch (error) {
        console.error('Erro ao carregar avisos:', error);
      }
    },
    async carregarEventos() {
      try {
        const response = await axios.get('/events');
        this.events = response.data || [];
      } catch (error) {
        console.error('Erro ao carregar eventos:', error);
      }
    },
    formatarHora(hora) {
      if (!hora) return '';
      const horaObj = new Date(hora);
      return horaObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    },
    getImageUrl(filename) {
      return filename
        ? `http://localhost:8000/storage/events/${filename}`
        : 'https://via.placeholder.com/300x200?text=Evento';
    },
    irParaPaginaEventos() {
      this.$router.push('/eventos');
    },
    irParaPaginaDoacoes() {
      this.$router.push('/doacoes');
    },
   
    async enviarPedidoOracao() {
  if (!this.novoPedido.trim()) {
    alert('Por favor, escreva seu pedido.');
    return;
  }

    try {
      const response = await axios.post('/pedir-oracao', {
        mensagem: this.novoPedido
      });

      this.respostaPedido = response.data.message;
      this.novoPedido = '';

      // Oculta a mensagem após 5 segundos
      setTimeout(() => {
        this.respostaPedido = '';
      }, 5000);
    } catch (error) {
      console.error('Erro ao enviar pedido de oração:', error);
      this.respostaPedido = 'Erro ao enviar seu pedido. Tente novamente mais tarde.';

      setTimeout(() => {
        this.respostaPedido = '';
      }, 5000);
    }
  },
  },
  mounted() {
    this.carregarAniversariantes();
    this.carregarAvisos();
    this.carregarEventos();
  }
};
</script>


<style scoped>
/* Estatísticas com bordas suaves e ícones grandes */
.card {
  border-radius: 1rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
}

/* Imagens da galeria */
.img-fluid {
  border-radius: 8px;
  object-fit: cover;
  height: 120px;
  width: 100%;
}

/* Rodapé */
.footer {
  background-color: #2c3e50;
  color: white;
  padding: 2rem 1rem;
  font-size: 0.95rem;
  margin-top: 2rem;
  border-top: 5px solid #f39c12;
}
.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 1.5rem;
  text-align: center;
}
.footer-section {
  flex: 1 1 250px;
}
.footer-section h3 {
  color: #f39c12;
  margin-bottom: 0.75rem;
}
.social-links {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

/* Botões e inputs */
button,
textarea {
  border-radius: 0.5rem;
}
textarea:focus {
  box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.25);
  border-color: #f39c12;
}

/* Leitura bíblica */
blockquote {
  font-size: 1.1rem;
  color: #555;
  border-left: 5px solid #f39c12;
  padding-left: 1rem;
}

/* Responsividade extra */
@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
  }

  .img-fluid {
    height: auto;
  }
}
</style>
