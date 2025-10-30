<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <main class="flex-grow-1 min-vh-100 bg-light">
      <NavDashboard />


      <!-- Conteúdo principal com espaçamento -->
      <div class="container mt-5 py-4">
        
        <div class="row g-4">
          <!-- Coluna Direita: Lista com Pesquisa -->
          <div class="col-lg-7 order-1 order-lg-1">
            <div class="card shadow-sm p-4 rounded-4 bg-white">
               <div v-if="mensagemSucesso" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ mensagemSucesso }}
                <button type="button" class="btn-close" @click="mensagemSucesso = ''" aria-label="Close"></button>
               </div>


              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-maroon">Pesquisar Avisos</h5>
                <input
                  v-model="pesquisa"
                  type="text"
                  placeholder="Buscar por título ou local"
                  class="form-control w-50"
                />
              </div>

              <div v-if="avisosFiltrados.length > 0" class="list-group">
                <div
                  v-for="aviso in avisosFiltrados"
                  :key="aviso.id"
                  class="list-group-item list-group-item-action d-flex justify-content-between align-items-start"
                > 
                  <div>
                    <div class="fw-bold">{{ aviso.title }}</div>
                    <small>{{ formatarData(aviso.date_realize) }} - {{ aviso.address }}</small>
                  </div>
                  <div>
                    <button class="btn btn-sm btn-outline-primary me-2" @click="editarAviso(aviso)">Editar</button>
                    <button class="btn btn-sm btn-outline-danger" @click="deletarAviso(aviso.id)">Excluir</button>
                  </div>
                </div>
              </div>

              <p v-else class="text-muted mt-4">Nenhum aviso encontrado.</p>
            </div>
          </div>

          <!-- Coluna Esquerda: Formulário -->
          <div class="col-lg-5 order-2 order-lg-2">
            <div class="card shadow-sm p-4 rounded-4 bg-white">
              <h5 class="mb-4 text-maroon fw-bold">{{ editando ? 'Editar Aviso' : 'Criar Aviso' }}</h5>

              <form @submit.prevent="salvarAviso">
                <div class="mb-3">
                  <label class="form-label">Título</label>
                  <input type="text" v-model="form.title" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Data de Notificação</label>
                  <input type="date" v-model="form.date_notify" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Data do Evento</label>
                  <input type="date" v-model="form.date_realize" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Hora</label>
                  <input type="time" v-model="form.hora" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Local</label>
                  <input type="text" v-model="form.address" class="form-control" required />
                </div>

                <div class="mb-3">
                  <label class="form-label">Descrição</label>
                  <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-maroon w-100">
                  {{ editando ? 'Atualizar' : 'Salvar' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NavDashboard from '../components/NavDashboard.vue';
import SidebarDashboard from '../components/SidebarDashboard.vue';

export default {
  components: { NavDashboard, SidebarDashboard },
 data() {
  return {
    form: {
      title: '',
      date_notify: '',
      date_realize: '',
      hora: '',
      address: '',
      description: '',
    },
    avisos: [],
    editando: false,
    idEditando: null,
    pesquisa: '',
    mensagemSucesso: '',
    timeoutMensagem: null,
  };
},
  computed: {
    avisosFiltrados() {
      const termo = this.pesquisa.toLowerCase();
      return this.avisos.filter(
        aviso =>
          aviso.title.toLowerCase().includes(termo) ||
          aviso.address.toLowerCase().includes(termo)
      );
    },
  },
  methods: {
    async buscarAvisos() {
      try {
        const { data } = await axios.get('/avisos');
        this.avisos = [...data.avisos_lidos, ...data.avisos_nao_lidos];
      } catch (error) {
        console.error('Erro ao buscar avisos:', error);
      }
    },
  async salvarAviso() {
  try {
    if (this.editando) {
      await axios.put(`/avisos/${this.idEditando}`, this.form);
      this.mostrarMensagem("Aviso atualizado com sucesso!");
    } else {
      await axios.post('/avisos', this.form);
      this.mostrarMensagem("Aviso criado com sucesso!");
    }
    this.resetarFormulario();
    await this.buscarAvisos();
  } catch (error) {
    console.error('Erro ao salvar aviso:', error);
  }
}
,
    editarAviso(aviso) {
  this.editando = true;
  this.idEditando = aviso.id;

  this.form = {
    title: aviso.title,
    date_notify: this.formatarDataInput(aviso.date_notify),
    date_realize: this.formatarDataInput(aviso.date_realize),
    hora: this.formatarHoraInput(aviso.hora),
    address: aviso.address,
    description: aviso.description,
  };
},
 formatarDataInput(data) {
    const d = new Date(data);
    return d.toISOString().split('T')[0]; // YYYY-MM-DD
  },
  formatarHoraInput(hora) {
  if (!hora) return '';
  
  // Se for no formato "14:30:00"
  if (hora.length === 8 && hora.includes(':')) {
    return hora.slice(0, 5); // "14:30"
  }

  // Se for datetime ou outra string longa
  try {
    const d = new Date(hora);
    const horas = d.getHours().toString().padStart(2, '0');
    const minutos = d.getMinutes().toString().padStart(2, '0');
    return `${horas}:${minutos}`;
  } catch {
    return '';
  }
},
async deletarAviso(id) {
  if (confirm('Tem certeza que deseja excluir este aviso?')) {
    try {
      await axios.delete(`/avisos/${id}`);
      this.mostrarMensagem("Aviso excluído com sucesso!");
      await this.buscarAvisos();
    } catch (error) {
      console.error('Erro ao excluir aviso:', error);
    }
  }
}
,
    resetarFormulario() {
      this.form = {
        title: '',
        date_notify: '',
        date_realize: '',
        hora: '',
        address: '',
        description: '',
      };
      this.editando = false;
      this.idEditando = null;
    },
    formatarData(data) {
      return new Date(data).toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
      });
    },
    mostrarMensagem(texto) {
  this.mensagemSucesso = texto;

  // Remove após 4 segundos automaticamente
  clearTimeout(this.timeoutMensagem);
  this.timeoutMensagem = setTimeout(() => {
    this.mensagemSucesso = '';
  }, 4000);
}

  },
  mounted() {
    this.buscarAvisos();
  },
};
</script>

<style scoped>
.text-maroon {
  color: #800000;
}
.btn-maroon {
  background-color: #800000;
  color: #fff;
}
.btn-maroon:hover {
  background-color: #a00000;
}
</style>
