<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar - Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100">
      <NavDashboard />

      <div class="container py-4 mt-5">
        <!-- Formulário de Registro -->
        <div class="card shadow-sm mb-4 rounded-4">
          <div class="card-header bg-maroon text-white py-2">
            <h5 class="mb-0" id="formTitle">{{ editing ? 'Editar Evento' : 'Cadastrar Evento' }}</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="submitForm" enctype="multipart/form-data" aria-labelledby="formTitle">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label" for="title">Título</label>
                  <input v-model="form.title" id="title" type="text" class="form-control" placeholder="Título" required />
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="date">Data</label>
                  <input v-model="form.date" id="date" type="date" class="form-control" required />
                </div>
                <div class="col-md-3">
                  <label class="form-label" for="time">Hora</label>
                  <input v-model="form.time" id="time" type="time" class="form-control" required />
                </div>
                <div class="col-12">
                  <label class="form-label" for="location">Local</label>
                  <input v-model="form.location" id="location" type="text" class="form-control" placeholder="Local do evento" required />
                </div>
                <div class="col-12">
                  <label class="form-label" for="description">Descrição</label>
                  <textarea v-model="form.description" id="description" class="form-control" rows="2" placeholder="Descrição" required></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label" for="image">Imagem</label>
                  <input type="file" id="image" class="form-control" @change="handleImageUpload" ref="imageInput" :required="!editing" />
                  <small v-if="editing && form.image_name" class="text-muted">Imagem atual: {{ form.image_name }}</small>
                </div>
              </div>
              <div class="text-end mt-3">
                <button type="submit" class="btn btn-maroon px-4 fw-semibold">
                  {{ editing ? 'Salvar Alterações' : 'Salvar Evento' }}
                </button>
                <button v-if="editing" type="button" class="btn btn-secondary ms-2" @click="cancelEdit">
                  Cancelar
                </button>
              </div>
              <div v-if="message" class="alert alert-info mt-3 text-center" role="alert">{{ message }}</div>
            </form>
          </div>
        </div>

        <!-- Campo de Pesquisa -->
        <div class="mb-3">
          <input type="text" class="form-control" v-model="search" placeholder="Pesquisar por título ou local..." aria-label="Campo de pesquisa" />
        </div>

        <!-- Lista de Eventos -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle text-center" aria-describedby="formTitle">
            <thead class="table-danger">
              <tr>
                <th>Imagem</th>
                <th>Título</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Local</th>
                <th>Descrição</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(event, i) in eventosPaginados" :key="i">
                <td>
                  <img :src="`/storage/events/${event.image}`" alt="Imagem do Evento" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover" />
                </td>
                <td>{{ event.title }}</td>
                <td>{{ formatDate(event.date) }}</td>
                <td>{{ event.time }}</td>
                <td>{{ event.location }}</td>
                <td>{{ event.description }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" @click="editEvent(event)">Editar</button>
                  <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(event.id)">Apagar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <nav aria-label="Paginação de eventos" class="d-flex justify-content-center">
          <ul class="pagination">
            <li class="page-item" :class="{ disabled: paginaAtual === 1 }">
              <button class="page-link" @click="paginaAtual--" :disabled="paginaAtual === 1">Anterior</button>
            </li>
            <li class="page-item" v-for="p in totalPaginas" :key="p" :class="{ active: paginaAtual === p }">
              <button class="page-link" @click="paginaAtual = p">{{ p }}</button>
            </li>
            <li class="page-item" :class="{ disabled: paginaAtual === totalPaginas }">
              <button class="page-link" @click="paginaAtual++" :disabled="paginaAtual === totalPaginas">Próxima</button>
            </li>
          </ul>
        </nav>

        <!-- Modal Confirmação de Deleção -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" ref="confirmModal">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                Tem certeza que deseja apagar este evento?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" @click="deleteEvent(confirmId)">Apagar</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>

    <!-- Sidebar - Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '../../components/NavDashboard.vue';
import SidebarDashboard from '../../components/SidebarDashboard.vue';
import { Modal } from 'bootstrap';

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      form: {
        title: '', date: '', time: '', location: '', description: '', image: null, image_name: '',
      },
      editing: false,
      editId: null,
      message: '',
      events: [],
      search: '',
      paginaAtual: 1,
      porPagina: 5,
      confirmId: null,
      confirmModalInstance: null,
    };
  },
  computed: {
    eventosFiltrados() {
      const termo = this.search.toLowerCase();
      return this.events.filter(e => e.title.toLowerCase().includes(termo) || e.location.toLowerCase().includes(termo));
    },
    totalPaginas() {
      return Math.ceil(this.eventosFiltrados.length / this.porPagina);
    },
    eventosPaginados() {
      const inicio = (this.paginaAtual - 1) * this.porPagina;
      return this.eventosFiltrados.slice(inicio, inicio + this.porPagina);
    }
  },
  methods: {
    handleImageUpload(e) {
      this.form.image = e.target.files[0];
      this.form.image_name = e.target.files[0]?.name || '';
    },
    async submitForm() {
    const formData = new FormData();
      Object.entries(this.form).forEach(([k, v]) => {
        if (k !== 'image_name' && (k !== 'image' || v)) {
          formData.append(k, v);
        }
      });



      try {

        // Verifica se já existe evento na data
      const existingEvent = await axios.get('/events-for-date', { params: { date: this.form.date } });
      if (existingEvent.data.length > 0) {
        this.message = '❌ Já existe um evento neste dia.';
        return;
      }

        if (this.editing && this.editId) {
          await axios.post(`/events/${this.editId}?_method=PUT`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          this.message = '✅ Evento atualizado com sucesso!';
        } else {
          await axios.post('/events', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
          });
          this.message = '✅ Evento cadastrado com sucesso!';
        }
        this.resetForm();
        this.carregarEventos();
    } catch (e) {
      this.message = '❌ Erro ao salvar o evento.';
      console.error(e);
      if (e.response && e.response.data && e.response.data.errors) {
        console.error('Detalhes do erro:', e.response.data.errors);
      }
    }
    },
    editEvent(event) {
      this.form = { ...event, image: null, image_name: event.image || '' };
      this.editing = true;
      this.editId = event.id;
      this.message = '';
    },
     async fetchEventsForDate(date) {
    try {
      const response = await axios.get('/events-for-date', { params: { date } });
      this.eventsOnDate = response.data;
    } catch (error) {
      console.error(error);
    }
  },
    confirmDelete(id) {
      this.confirmId = id;
      if (!this.confirmModalInstance) {
        this.confirmModalInstance = new Modal(this.$refs.confirmModal);
      }
      this.confirmModalInstance.show();
    },
    async deleteEvent(id) {
      try {
        await axios.delete(`/events/${id}`);
        this.message = '🗑️ Evento apagado com sucesso!';
        this.carregarEventos();
        this.confirmModalInstance.hide();
      } catch (e) {
        this.message = '❌ Erro ao apagar o evento.';
        console.error(e);
      }
    },
    resetForm() {
      this.form = { title: '', date: '', time: '', location: '', description: '', image: null, image_name: '' };
      this.editing = false;
      this.editId = null;
      this.message = '';
      this.$refs.imageInput && (this.$refs.imageInput.value = '');
    },
    carregarEventos() {
      axios.get('/events').then(res => this.events = res.data).catch(console.error);
    },
    formatDate(d) {
      return new Date(d).toLocaleDateString('pt-PT');
    }
  },
  mounted() {
    this.carregarEventos();
  },
};
</script>

<style scoped>
.btn-maroon {
  background-color: #8B0000;
  color: #fff;
}
.btn-maroon:hover {
  background-color: #a10000;
  color: #fff;
}
.bg-maroon {
  background-color: #8B0000;
}
</style>