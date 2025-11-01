<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar - Mobile Hidden, Desktop Visible -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 bg-light">
      <!-- Navbar - Always Visible -->
      <NavDashboard />

      <!-- Main Content Area -->
      <div class="container py-4">
        <section class="anniversaries row justify-content-center mt-5">
          
          <!-- Mensagem caso não haja aniversariantes -->
          <div v-if="aniversariantes.length === 0" class="alert alert-info text-center w-100">
            🎉 Nenhum aniversariante disponível neste momento.
          </div>

          <!-- Lista de aniversariantes -->
          <div
            v-for="user in aniversariantes"
            :key="user.id"
            class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4"
            v-else
          >
            <div class="card h-100 shadow-sm text-center">
              <!-- Profile Image -->
              <div class="photo-wrapper p-3">
                <img
                  :src="getFoto(user)"
                  :alt="`Imagem de ${user.nome}`"
                  class="profile-pic"
               
                />                
              </div>

              <!-- User Info & Actions -->
              <div class="card-body">
                <h5 class="card-title">{{ user.nome }} <small>({{ user.apelido }})</small></h5>
                <p class="text-muted">🎂 {{ formatDate(user.data_nascimento) }}</p>

                <div class="actions d-flex justify-content-center gap-2 mb-2">
                  <button class="btn btn-warning" @click="like(user)">
                    ❤️ {{ user.total_curtidaRecebidas }}
                  </button>
                  <button class="btn btn-warning" @click="toggleComment(user)">
                    💬 {{ user.total_comentariosRecebidos }}
                  </button>
                </div>

                <!-- Comments Section -->
                <div v-if="user.showCommentBox" class="comments">
                  <input
                    v-model="user.newComment"
                    placeholder="Escreva um comentário..."
                    class="form-control mb-2"
                  />
                  <button class="btn btn-dark btn-sm" @click="submitComment(user)">
                    Enviar
                  </button>

                  <ul class="mt-3 text-start list-unstyled">
                    <li v-for="comentario in user.comentarios" :key="comentario.id">
                      <strong>{{ comentario.user.nome }}:</strong> {{ comentario.mensagem }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Sidebar - Desktop Hidden, Mobile Visible -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>


<script>
import axios from 'axios';
import NavDashboard from '../../components/NavDashboard.vue';
import SidebarDashboard from '../../components/SidebarDashboard.vue';

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      aniversariantes: []
    };
  },
  mounted() {
    this.fetchAniversariantes();
  },
  methods: {
   getToken() {
    return localStorage.getItem('token');
  },
  fetchAniversariantes() {
    axios
      .get('/aniversariantes', {
        headers: { Authorization: `Bearer ${this.getToken()}` }
      })
      .then(response => {
         console.log(response.data)
        this.aniversariantes = response.data.map(user => ({
          ...user,
          showCommentBox: false,
          newComment: ''
        }));
      })
      .catch(error => {
        console.error('Erro ao carregar aniversariantes:', error);
      });
  },
    formatDate(date) {
      return new Date(date).toLocaleDateString('pt-BR');
    },
    getFoto(user) {
  if (user.foto_url) {
    return user.foto_url
  } else {
    return 'https://via.placeholder.com/100'
  }
},
    getImageUrl(path) {
      return path ? `http://localhost:8000/storage/${path}` : 'https://via.placeholder.com/100';
    }, 
    like(user) {
      axios
        .post(`/aniversariantes/${user.id}/curtir`, {}, {
          headers: { Authorization: `Bearer ${this.getToken()}` }
        })
        .then(() => {
          user.total_curtidaRecebidas++;
        })
        .catch(error => {
          if (error.response?.status === 409) {
            alert('Você já curtiu este aniversariante.');
          } else {
            console.error('Erro ao curtir:', error);
          }
        });
    },
    toggleComment(user) {
      user.showCommentBox = !user.showCommentBox;
    },
    submitComment(user) {
      if (!user.newComment) return;

      axios
        .post(
          `/aniversariantes/${user.id}/comentar`,
          { mensagem: user.newComment },
          { headers: { Authorization: `Bearer ${this.getToken()}` } }
        )
        .then(response => {
          user.comentarios.push(response.data);
          user.total_comentariosRecebidos++;
          user.newComment = '';
        })
        .catch(error => {
          console.error('Erro ao comentar:', error);
        });
    }
  }
};
</script>

<style scoped>
.profile-pic {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 3px solid #ffd700;
  object-fit: cover;
}

/* Responsive Layout Adjustments */
@media (max-width: 991px) {
  /* Adjust cards for smaller screens */
  .anniversaries .card {
    margin-bottom: 15px;
  }
}
</style>
