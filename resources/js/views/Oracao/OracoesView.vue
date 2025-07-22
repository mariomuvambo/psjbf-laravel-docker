<template>
  <div class="d-flex min-vh-100 bg-light">
    <!-- Sidebar -->
    <SidebarDashboard />

    <!-- Conteúdo Principal -->
    <div class="flex-grow-1 d-flex flex-column">
      <!-- Navbar -->
      <NavDashboard />

      <!-- Conteúdo -->
      <main class="flex-grow-1 px-4 pt-navbar">
        <div class="container-fluid">
          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="fw-bold text-maroon m-0">Pedidos de Oração</h2>
            <button class="btn btn-outline-primary" @click="printPage">
              <i class="bi bi-printer me-2"></i> Imprimir
            </button>
          </div>

          <div v-if="oracoes.length === 0" class="alert alert-info text-center">
            Nenhum pedido de oração encontrado.
          </div>

          <div v-else class="table-responsive bg-white rounded shadow-sm p-3">
            <table class="table table-striped align-middle">
              <thead class="table-light">
                <tr>
                  <th>Usuário</th>
                  <th>Mensagem</th>
                  <th>Data</th>
                  <th class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="oracao in oracoes" :key="oracao.id">
                  <td>{{ oracao.user_id }}</td>
                  <td>{{ oracao.mensagem }}</td>
                  <td>{{ formatarData(oracao.created_at) }}</td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-success" @click="marcarComoLida(oracao.id)">
                      <i class="bi bi-check-circle me-1"></i> Marcar como lida
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import SidebarDashboard from '@/components/SidebarDashboard.vue'
import NavDashboard from '@/components/NavDashboard.vue'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'

const oracoes = ref([])

const fetchOracoes = async () => {
  try {
    const response = await axios.get('/pedir-oracao')
    oracoes.value = response.data
  } catch (error) {
    console.error('Erro ao carregar orações:', error)
  }
}

const marcarComoLida = async (id) => {
  try {
    await axios.post(`/oracoes/${id}/marcar-lida`)
    oracoes.value = oracoes.value.filter(o => o.id !== id)
  } catch (error) {
    console.error('Erro ao marcar como lida:', error)
  }
}

const formatarData = (data) => {
  return new Date(data).toLocaleString()
}

const printPage = () => {
  const doc = new jsPDF()

  doc.setFontSize(16)
  doc.text('Lista de Pedidos de Oração', 14, 15)

  const tableData = oracoes.value.map(o => [
    o.mensagem,
    formatarData(o.created_at)
  ])

  autoTable(doc, {
    startY: 25,
    head: [['Mensagem', 'Data']],
    body: tableData,
    styles: { fontSize: 10 },
    headStyles: { fillColor: [139, 0, 0] }
  })

  doc.save('oracoes.pdf')
}

onMounted(() => {
  fetchOracoes()
})


</script>

<style scoped>
.text-maroon {
  color: #8b0000;
}
.table th, .table td {
  vertical-align: middle;
}
.pt-navbar {
  padding-top: 80px; /* ajuste conforme a altura da sua NavDashboard */
}
</style>
