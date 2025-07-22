<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar (desktop) -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <!-- Conteúdo principal -->
    <main class="flex-grow-1 bg-light min-vh-100">
      <NavDashboard />

      <!-- Conteúdo -->
      <div class="container pt-5 mt-4">
        <!-- Cabeçalho -->
        <div class="text-center mb-5">
          <h2 class="text-primary fw-bold display-6">
            <i class="fas fa-users-cog me-2"></i> Gestão de Usuários
          </h2>
          <p class="text-muted">Visualize, edite e gerencie todos os usuários do sistema</p>
          <hr class="w-25 mx-auto border-primary" />
        </div>


       <!-- Estatísticas -->
<div class="mb-5 text-center">
  <!-- Card Total -->
  <div class="row justify-content-center mb-4">
    <div class="col-md-6">
      <div class="card bg-dark text-white shadow">
        <div class="card-body py-4">
          <h4 class="mb-2">
            <i class="fas fa-users me-2"></i>Total de Usuários
          </h4>
          <h2 class="fw-bold">{{ estatisticas.total }}</h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Demais Estatísticas -->
 <div class="row justify-content-center">
  <div
    class="col-md-3 mb-3"
    v-for="[tipo, valor] in Object.entries(estatisticas).filter(([key]) => key !== 'total')"
    :key="tipo"
  >
      <div :class="['card', tipoCor(tipo), 'text-white', 'shadow']">
        <div class="card-body">
          <h5 class="mb-2">{{ capitalizar(tipo) }}</h5>
          <h3>{{ valor }}</h3>
        </div>
      </div>
    </div>
  </div>
</div>


        <!-- Campo de Pesquisa (Centralizado) -->
        <div class="row justify-content-center mb-5">
          <div class="col-md-6">
            <input
              v-model="filtro"
              type="text"
              class="form-control form-control-lg shadow-sm"
              placeholder="Pesquisar por nome ou email..."
            />
          </div>
        </div>


        <!-- Lista de Usuários -->
        <div class="card shadow rounded-4">
          <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
              <i class="fas fa-list me-2"></i> Lista de Usuários
            </h5>
          </div>

          <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Foto</th>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Tipo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in usuariosFiltrados" :key="user.id">
                  <td>
                    <img
                      :src="user.foto ? '/storage/' + user.foto : 'https://via.placeholder.com/40'"
                      class="rounded-circle"
                      style="width: 40px; height: 40px;"
                    />
                  </td>
                  <td>{{ user.nome }}</td>
                  <td>{{ user.email }}</td>
                  <td><span class="badge bg-secondary text-uppercase">{{ user.tipo_usuario }}</span></td>
                  <td>
                    <button class="btn btn-sm btn-primary me-2" @click="openEditModal(user)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" @click="deleteUsuario(user.id)">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal de Edição -->
        <div class="modal fade" id="editUserModal" tabindex="-1" ref="modalRef" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <form @submit.prevent="atualizarUsuario">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">Editar Usuário</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label>Nome</label>
                    <input v-model="form.nome" type="text" class="form-control" required />
                  </div>
                  <div class="mb-3">
                    <label>Email</label>
                    <input v-model="form.email" type="email" class="form-control" required />
                  </div>
                  <div class="mb-3">
                    <label>Tipo de Usuário</label>
                    <select v-model="form.tipo_usuario" class="form-select" required>
                      <option value="fiel">Fiel</option>
                      <option value="voluntario">Voluntário</option>
                      <option value="sacerdote">Sacerdote</option>
                      <option value="admin">Administrador</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label>Foto</label>
                    <input type="file" class="form-control" @change="handleFileUpload" />
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-success">Salvar</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </main>

    <!-- Sidebar mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </div>
  </div>
</template>


  <script setup>
  import { ref, computed, onMounted, nextTick } from 'vue'
  import axios from 'axios'
  import { Modal } from 'bootstrap'
  import SidebarDashboard from '@/components/SidebarDashboard.vue'
  import NavDashboard from '@/components/NavDashboard.vue'

  const usuarios = ref([])
  const estatisticas = ref({ total: 0, fieis: 0, voluntarios: 0, sacerdotes: 0 })
  const form = ref({ id: null, nome: '', email: '', tipo_usuario: '' })
  const foto = ref(null)
  const filtro = ref('')
  const modalRef = ref(null)
  let modalInstance = null

  onMounted(async () => {
    await carregarUsuarios()
    await nextTick(() => {
      modalInstance = new Modal(modalRef.value)
    })
  })

  const carregarUsuarios = async () => {
    const res = await axios.get('/usuarios', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    })
    usuarios.value = res.data.usuarios
    estatisticas.value = res.data.estatisticas
  }

  const usuariosFiltrados = computed(() => {
    return usuarios.value.filter(u =>
      u.nome.toLowerCase().includes(filtro.value.toLowerCase()) ||
      u.email.toLowerCase().includes(filtro.value.toLowerCase())
    )
  })

  const openEditModal = (user) => {
    form.value = { ...user }
    foto.value = null
    modalInstance.show()
  }

  const handleFileUpload = (e) => {
    foto.value = e.target.files[0]
  }

  const atualizarUsuario = async () => {
    const formData = new FormData()
    formData.append('nome', form.value.nome)
    formData.append('email', form.value.email)
    formData.append('tipo_usuario', form.value.tipo_usuario)
    if (foto.value) formData.append('foto', foto.value)

    await axios.post(`/usuarios/${form.value.id}`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'multipart/form-data',
      },
    })

    modalInstance.hide()
    await carregarUsuarios()
  }

  const deleteUsuario = async (id) => {
    if (confirm('Deseja realmente excluir este usuário?')) {
      await axios.delete(`/usuarios/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      })
      await carregarUsuarios()
    }
  }

  const tipoCor = (tipo) => {
    return {
      fieis: 'bg-primary',
      voluntarios: 'bg-success',
      sacerdotes: 'bg-danger',
    }[tipo] || 'bg-secondary'
  }

  const capitalizar = (txt) => txt.charAt(0).toUpperCase() + txt.slice(1)
  </script>

  <style scoped>
  .card-header h5 {
    margin-bottom: 0;
  }
  </style>
