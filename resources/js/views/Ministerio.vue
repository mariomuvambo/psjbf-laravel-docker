<template>
  <div class="d-flex flex-column flex-lg-row min-vh-100">
    <!-- Sidebar Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </div>

    <main class="flex-grow-1">
      <NavDashboard />

      <div class="container py-4" style="margin-top: 60px;">
        <!-- Formulário -->
        <div class="card shadow-lg rounded-4 mb-5">
          <div class="card-body">
            <form @submit.prevent="saveMinister">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nome do Ministério</label>
                  <input v-model="form.newMinister" type="text" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Descrição</label>
                  <input v-model="form.description" type="text" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Responsável</label>
                  <input v-model="form.responseMinister" type="text" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Adjunto</label>
                  <input v-model="form.responseAdjunto" type="text" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Setor Geral</label>
                  <input v-model="form.SectorGeral" type="text" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Setor do Ministério</label>
                  <input v-model="form.SectorMinister" type="text" class="form-control" required />
                </div>
              </div>

              <div class="text-end mt-4">
                <button class="btn btn-maroon fw-bold px-4" type="submit">
                  {{ form.id ? 'Atualizar' : 'Registrar' }} Ministério
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Lista de Ministérios -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-danger text-center">
              <tr>
                <th>Ministério</th>
                <th>Descrição</th>
                <th>Responsável</th>
                <th>Adjunto</th>
                <th>Setor Geral</th>
                <th>Setor Ministério</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="min in ministers" :key="min.id">
                <td>{{ min.newMinister }}</td>
                <td>{{ min.description }}</td> 
                <td>{{ min.responseMinister }}</td>
                <td>{{ min.responseAdjunto }}</td>
                <td>{{ min.SectorGeral }}</td>
                <td>{{ min.SectorMinister }}</td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-2" @click="editMinister(min)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" @click="deleteMinister(min.id)">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
import NavDashboard from '../components/NavDashboard.vue'
import SidebarDashboard from '../components/SidebarDashboard.vue'
import axios from 'axios'

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      ministers: [],
      form: {
        id: null,
        newMinister: '',
        description: '',
        responseMinister: '',
        responseAdjunto: '',
        SectorGeral: '',
        SectorMinister: '',
      },
    }
  },
  methods: {
    async fetchMinisters() {
      try {
        const { data } = await axios.get('/reg_ministers')
        this.ministers = data
      } catch (err) {
        console.error('Erro ao buscar ministros:', err)
      }
    },
    async saveMinister() {
      try {
        if (this.form.id) {
          await axios.put(`/reg_ministers/${this.form.id}`, this.form)
          alert('Ministério atualizado com sucesso!')
        } else {
          await axios.post('/reg_ministers', this.form)
          alert('Ministério registrado com sucesso!')
        }
        this.fetchMinisters()
        this.resetForm()
      } catch (err) {
        console.error('Erro ao salvar ministro:', err)
      }
    },
    editMinister(minister) {
      this.form = { ...minister }
    },
    async deleteMinister(id) {
      if (!confirm('Deseja realmente excluir este ministério?')) return
      try {
        await axios.delete(`/reg_ministers/${id}`)
        this.fetchMinisters()
        alert('Ministério removido.')
      } catch (err) {
        console.error('Erro ao deletar ministro:', err)
      }
    },
    resetForm() {
      this.form = {
        id: null,
        newMinister: '',
        description: '',
        responseMinister: '',
        responseAdjunto: '',
        SectorGeral: '',
        SectorMinister: '',
      }
    },
  },
  mounted() {
    this.fetchMinisters()
  },
}
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
</style>
