<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import SidebarDashboard from '../components/SidebarDashboard.vue'
import NavDashboard from '../components/NavDashboard.vue'
import Carousel from '../components/Carousel.vue'
import { useRouter } from 'vue-router'

// === Estado reativo ===
const avisosNaoLidos = ref([])
const events = ref([])
const aniversariantes = ref([])
const estatisticas = ref([])
const novoPedido = ref('')
const respostaPedido = ref('')
const router = useRouter()

const evangelho = ref({
  data: '07/07/2025',
  versiculo: 'Eu sou o caminho, a verdade e a vida.',
  referencia: 'João 14:6',
})

const mensagemParoco = ref(
  'Queridos irmãos, continuemos unidos na fé e na solidariedade, espalhando o amor de Cristo em nossas ações diárias.'
)

// === Helpers ===
const formatarHora = hora => {
  if (!hora) return ''
  return new Date(hora).toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getImageUrl = filename =>
  filename
    ? `http://localhost:8000/storage/events/${filename}`
    : 'https://via.placeholder.com/300x200?text=Evento'

const getNomeMes = mesIndex => {
  const meses = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
  ]
  return meses[mesIndex]
}

// === Computed ===
const eventosRecentes = computed(() =>
  events.value
    .slice()
    .sort((a, b) => new Date(b.date) - new Date(a.date))
    .slice(0, 2)
)

// === Navegação ===
const irParaPaginaEventos = () => router.push('/dashboard/eventos')
const irParaPaginaDoacoes = () => router.push('/dashboard/doacoes')

// === Requisições ===
const carregarDadosIniciais = async () => {
  try {
    const [aniv, avisos, ev, doacoes] = await Promise.all([
      axios.get('/data_aniversarianteMes'),
      axios.get('/avisos'),
      axios.get('/events'),
      axios.get('/doacoes-por-mes'),
    ])

    aniversariantes.value = aniv.data
    avisosNaoLidos.value = avisos.data.avisos_nao_lidos || []
    events.value = ev.data || []

    const estatisticasBase = [
      { label: 'Total de Fiéis', valor: 150, color: 'bg-info' },
      { label: 'Missas Hoje', valor: 3, color: 'bg-success' },
      { label: 'Eventos Ativos', valor: 5, color: 'bg-danger' },
    ]

    const dataAtual = new Date()
    const mesAtual = ('0' + (dataAtual.getMonth() + 1)).slice(-2) + '/' + dataAtual.getFullYear()
    const doacaoMes = doacoes.data.find(d => d.mes === mesAtual)
    const valorDoacao = doacaoMes ? parseFloat(doacaoMes.total) : 0

    estatisticasBase.push({
      label: `Doações (${getNomeMes(dataAtual.getMonth())})`,
      valor: `${valorDoacao.toLocaleString('pt-MZ')} MT`,
      color: 'bg-warning',
    })

    estatisticas.value = estatisticasBase
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  }
}

// === Pedido de oração ===
const enviarPedidoOracao = async () => {
  if (!novoPedido.value.trim()) {
    alert('Por favor, escreva seu pedido.')
    return
  }

  try {
    const res = await axios.post('/pedir-oracao', { mensagem: novoPedido.value })
    respostaPedido.value = res.data.message
    novoPedido.value = ''
  } catch (error) {
    console.error('Erro ao enviar pedido de oração:', error)
    respostaPedido.value = 'Erro ao enviar seu pedido. Tente novamente.'
  }

  setTimeout(() => (respostaPedido.value = ''), 5000)
}

onMounted(carregarDadosIniciais)
</script>

<template>
  <div class="d-flex justify-content-start">
    <SidebarDashboard />

    <main class="flex-grow-1">
      <div class="content-wrapper px-2 px-md-3">
        <NavDashboard />

        <div id="carousel" class="my-4"><Carousel /></div>

        <!-- Estatísticas -->
        <section class="mb-4">
          <div class="row g-3">
            <div v-for="stat in estatisticas" :key="stat.label" class="col-md-6 col-lg-3">
              <div class="card text-white h-90" :class="stat.color">
                <div class="card-body text-center">
                  <h5>{{ stat.label }}</h5>
                  <p class="display-6 fw-bold">{{ stat.valor }}</p>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Leitura do dia -->
        <section class="mb-4">
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
        <section class="mb-5">
          <div class="row g-4">
            <!-- Avisos -->
            <div class="col-md-6">
              <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                  <span>📢 Avisos Paroquiais</span>
                  <router-link to="/dashboard/avisos" class="btn btn-outline-light btn-sm">Ver todos</router-link>
                </div>
                <div class="card-body">
                  <ul v-if="avisosNaoLidos.length" class="list-unstyled mb-0">
                    <li v-for="aviso in avisosNaoLidos" :key="aviso.id" class="mb-3">
                      <router-link :to="`/dashboard/avisos/${aviso.id}`" class="text-decoration-none d-flex justify-content-between">
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
                <div class="card-header bg-success text-white d-flex justify-content-between">
                  <div>🎉 Aniversariantes do Mês</div>
                  <router-link to="/dashboard/aniversariantes" class="btn btn-outline-light btn-sm d-flex align-items-center">
                    <i class="bi bi-people-fill me-1"></i> Ver todos
                  </router-link>
                </div>
                <div class="card-body">
                  <ul v-if="aniversariantes.length" class="list-unstyled mb-0">
                    <li v-for="pessoa in aniversariantes" :key="pessoa.id" class="mb-2 d-flex align-items-center">
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

        <!-- Eventos Recentes -->
        <section class="mb-5">
          <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between">
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
        <section class="mb-5 text-center">
          <div class="card shadow-sm p-4">
            <h4 class="mb-3">💕 Apoie nossa Paróquia</h4>
            <p>As doações mantêm nossas atividades e ajudam os mais necessitados.</p>
            <button class="btn btn-danger" @click="irParaPaginaDoacoes">Fazer uma Doação</button>
          </div>
        </section>

        <!-- Mensagem do Pároco -->
        <section class="mb-5">
          <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">🕋️ Mensagem do Pároco</div>
            <div class="card-body">
              <p class="lead">{{ mensagemParoco }}</p>
              <p class="text-muted">Pe. José Antônio - Pároco</p>

              <hr />
              <h5 class="mt-4">🙏 Envie seu Pedido de Oração</h5>
              <form @submit.prevent="enviarPedidoOracao">
                <textarea v-model="novoPedido" class="form-control mb-3" placeholder="Digite seu pedido de oração..." rows="4" />
                <button type="submit" class="btn btn-danger">Enviar Pedido</button>
              </form>

              <div v-if="respostaPedido" class="alert alert-success mt-3">{{ respostaPedido }}</div>
            </div>
          </div>
        </section>

        <!-- Rodapé -->
        <footer class="footer mt-auto">
          <div class="footer-container container">
            <div class="footer-section">
              <h3>Paróquia São João Baptista do Fomento</h3>
              <p>Paróquia dedicada à fé, caridade e comunidade.</p>
            </div>
            <div class="footer-section">
              <h3>Contato</h3>
              <p>Email: paroquia@igreja.org</p>
              <p>Telefone: (+258) 84 123 4567</p>
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
      </div>
    </main>
  </div>
</template>

<style scoped>
/* Wrapper para alinhar conteúdo à esquerda */
.content-wrapper {
  max-width: 1040px; /* ou qualquer valor menor que container padrão */
  margin-left: 0; /* alinha à esquerda */
  margin-right: auto;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

/* Card padrão */
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
  justify-content: flex-start; /* alinhamento à esquerda */
  gap: 1.5rem;
  text-align: left;
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
  justify-content: flex-start;
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

/* Responsividade */
@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    text-align: center;
  }

  .img-fluid {
    height: auto;
  }
}
</style>