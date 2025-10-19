<template>
  <div class="carousel-wrapper py-5 mt-5">
    <!-- Carousel -->
    <div class="w-100 mx-0">
      <div class="carousel slide carousel-dark rounded-4 shadow-lg overflow-hidden" id="carouselExampleDark" data-bs-ride="carousel">
        <!-- Indicadores -->
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
         <!-- Carousel -->
        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
              <img src="../assets/images/img-1.jpg" class="d-block w-100 slide-img" alt="Primeiro slide" />
              <div class="carousel-caption enhanced-caption">
                <button type="button" class="btn btn-warning btn-lg fw-bold shadow" data-bs-toggle="modal" data-bs-target="#modalHorariosMissas">
                  <i class="bi bi-calendar2-week"></i> Horários das Missas
                </button>
                <p class="mt-2 text-white fw-medium">Participe conosco das celebrações!</p>
              </div>
            </div>
          </div>
        </div>


          <div class="carousel-item" data-bs-interval="5000">
            <img src="../assets/images/arjun-mj-UEeiZeKFlks-unsplash.jpg" class="d-block w-100 slide-img" alt="Segundo slide" />
            <div class="carousel-caption enhanced-caption">
              <h5>Aniversariantes</h5>
              <p>Aniversariantes da semana</p>
            </div>
          </div>

          <div class="carousel-item">
            <img src="../assets/images/debby-hudson-qa74Yh6-xlc-unsplash.jpg" class="d-block w-100 slide-img" alt="Terceiro slide" />
            <div class="carousel-caption enhanced-caption">
              <h5>Calendário de Eventos</h5>
              <button type="button" class="btn btn-primary" @click="openCalendarModal"> 
                Ver Eventos
              </button>
            </div>
          </div>
        </div>

        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Próximo</span>
        </button>
      </div>
    </div>


    <!-- Modal Horarios -->
<!-- Modal -->
<div class="modal fade" id="modalHorariosMissas" tabindex="-1" aria-labelledby="modalHorariosMissasLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header bg-primary text-white rounded-4">
        <h5 class="modal-title" id="modalHorariosMissasLabel">🕊️ Horários de Missas e Padres Responsáveis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Tabela de Horários de Missas -->
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead class="thead-dark bg-light">
              <tr>
                <th>Dia da Semana</th>
                <th>Horário</th>
                <th>Padre Responsável</th>
              </tr>
            </thead>
            <tbody>
              <!-- Segunda-feira -->
              <tr>
                <td>Segunda-feira</td>
                <td>07h00</td>
                <td>Padre João</td>
              </tr>
              <tr>
                <td>Segunda-feira</td>
                <td>19h00</td>
                <td>Padre Marcos</td>
              </tr>

              <!-- Terça-feira -->
              <tr>
                <td>Terça-feira</td>
                <td>07h00</td>
                <td>Padre João</td>
              </tr>
              <tr>
                <td>Terça-feira</td>
                <td>19h00</td>
                <td>Padre Lucas</td>
              </tr>

              <!-- Quarta-feira -->
              <tr>
                <td>Quarta-feira</td>
                <td>07h00</td>
                <td>Padre Marcos</td>
              </tr>
              <tr>
                <td>Quarta-feira</td>
                <td>19h00</td>
                <td>Padre João</td>
              </tr>

              <!-- Quinta-feira -->
              <tr>
                <td>Quinta-feira</td>
                <td>07h00</td>
                <td>Padre Lucas</td>
              </tr>
              <tr>
                <td>Quinta-feira</td>
                <td>19h00</td>
                <td>Padre João</td>
              </tr>

              <!-- Sexta-feira -->
              <tr>
                <td>Sexta-feira</td>
                <td>07h00</td>
                <td>Padre Marcos</td>
              </tr>
              <tr>
                <td>Sexta-feira</td>
                <td>19h00</td>
                <td>Padre Lucas</td>
              </tr>

              <!-- Sábado -->
              <tr>
                <td>Sábado</td>
                <td>18h00</td>
                <td>Padre João</td>
              </tr>

              <!-- Domingo -->
              <tr>
                <td>Domingo</td>
                <td>07h00</td>
                <td>Padre Marcos</td>
              </tr>
              <tr>
                <td>Domingo</td>
                <td>09h00</td>
                <td>Padre Lucas</td>
              </tr>
             
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>


    <!-- Modal de Calendário -->
     <!-- Modal de Calendário -->
<div 
  v-if="calendarVisible" 
  class="modal fade show" 
  id="calendarModal" 
  tabindex="-1" 
  aria-labelledby="calendarModalLabel" 
  :aria-hidden="calendarVisible ? 'false' : 'true'" 
  style="display: block;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4 shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="calendarModalLabel">Calendário de Eventos</h5>
        <button type="button" class="btn-close btn-close-white" @click="calendarVisible = false"></button>
      </div>
      <div class="modal-body bg-light">
        <p class="text-secondary mb-4"><i class="bi bi-info-circle"></i> Confira abaixo os eventos:</p>
        
        <!-- Calendário VueCal -->
        <vue-cal 
          :events="events" 
          :event-color="'#ff4081'" 
          :now="new Date()" 
          :disabled-days="[0, 6]" 
          :disabled="false" 
          :first-day-of-week="1">
        </vue-cal>
        
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-secondary" @click="calendarVisible = false">
          Fechar
        </button>
      </div>
    </div>
  </div>
</div>

   
  </div>
</template>

<script>
import axios from 'axios';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

export default {
  components: {
    VueCal
  },
  data() {
    return {
      calendarVisible: false,  // Controla a visibilidade do modal
      events: []  // Lista de eventos carregados
    };
  },
  methods: {
    // Função para abrir o modal
    openCalendarModal() {
      this.calendarVisible = true;
      this.loadEvents(); // Carregar os eventos ao abrir o modal
    },

    // Função para carregar eventos da API
  async loadEvents() {
  try {
    const currentDate = this.formatDate(new Date()); // Formata a data para o formato correto
    console.log("Data atual para pesquisa: ", currentDate); // Log para verificar a data

    const response = await axios.get('/events-for-date', {
      params: { date: currentDate } // Passa a data no formato correto
    });

    console.log("Eventos recebidos: ", response.data); // Verifica a resposta

    // Mapeia os dados para o formato esperado pelo VueCal
    this.events = response.data.map(event => {
      const eventDate = new Date(event.date + 'T' + event.time + ':00');
      eventDate.setHours(eventDate.getHours() + 3); // Ajustando para o fuso horário, caso necessário

      const start = eventDate.toISOString(); // Formata a data no formato ISO (YYYY-MM-DDTHH:mm:ss)
      return {
        start,
        end: start, // Se for um evento de apenas 1 horário, podemos definir "end" igual ao "start"
        title: event.title,
        location: event.location,
        description: event.description,
        color: '#ff4081', // Cor personalizada
        icon: '🎉', // Ícone, se quiser adicionar
      };
    });

  } catch (error) {
    console.error("Erro ao carregar eventos", error);
  }
},


    // Função para formatar a data para o formato YYYY-MM-DD
    formatDate(date) {
      if (!(date instanceof Date)) {
        date = new Date(date);
      }

      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Adiciona zero se necessário
      const day = date.getDate().toString().padStart(2, '0'); // Adiciona zero se necessário
      return `${year}-${month}-${day}`;
    }
  }
};
</script>

<style scoped>
.carousel-wrapper {
  margin-top: 70px;
}

.slide-img {
  object-fit: cover;
  height: 220px;
  width: 100%;
}

.enhanced-caption {
  background: rgba(0, 0, 0, 0.6);
  padding: 1rem 1.5rem;
  border-radius: 10px;
  color: white;
  max-width: 600px;
  margin: 0 auto;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  filter: invert(1);
}

.vuecal__event {
  background-color: #ff9800 !important;  /* Cor do evento */
  color: white !important;  /* Cor do texto */
  border-radius: 5px;  /* Bordas arredondadas */
}

/* Modal Customizado */
.modal-content {
  border-radius: 12px;  /* Bordas arredondadas para o modal */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);  /* Sombra suave */
}

/* Cabeçalho do Modal */
.modal-header {
  background-color: #007bff;  /* Cor de fundo azul */
  color: white;  /* Texto branco */
  border-top-left-radius: 12px;  /* Arredondando os cantos superiores */
  border-top-right-radius: 12px;
}

/* Estilo da Tabela */
.table {
  width: 100%;
  font-size: 1rem;
  border-radius: 8px;
  margin-bottom: 0;
}

.table th, .table td {
  text-align: center;
  vertical-align: middle;
  padding: 12px;
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

.table-bordered th, .table-bordered td {
  border: 1px solid #dee2e6;
}

.thead-dark th {
  background-color: #343a40;
  color: white;
}

.table-responsive {
  margin-top: 15px;
}

/* Botões */
.btn-close {
  background-color: transparent;
  border: none;
  font-size: 1.25rem;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  border-radius: 8px;
}

.btn-secondary:hover {
  background-color: #5a6268;
}


@media (max-width: 768px) {
  .slide-img {
    height: 300px;
  }

  .enhanced-caption {
    font-size: 0.9rem;
    padding: 0.8rem;
    max-width: 90%;
  }
}
</style>
