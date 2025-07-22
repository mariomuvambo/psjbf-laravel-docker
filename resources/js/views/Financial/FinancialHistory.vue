<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar - Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard class="p-2" />
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 bg-light min-vh-100">
      <NavDashboard />

      <div class="container pt-5"> <!-- Aumentado o padding-top aqui -->
        <!-- Título e botão PDF -->
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">

          <h4 class="text-primary fw-bold">📊 Relatório de Doações</h4>
          <button class="btn btn-outline-primary" @click="exportarPDF">
            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
          </button>
        </div>

        <!-- Gráficos -->
        <div class="row mb-5">
          <div class="col-md-6 mb-3">
            <div class="card shadow rounded-4 p-3">
              <h6 class="text-center">Doações Mensais</h6>
              <BarChart v-if="mensalChart" :chart-data="mensalChart" />
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="card shadow rounded-4 p-3">
              <h6 class="text-center">Doações Anuais</h6>
              <LineChart v-if="anualChart" :chart-data="anualChart" />
            </div>
          </div>
        </div>

        <!-- Histórico de Doações -->
        <div ref="historicoRef">
          <h5 class="text-center text-secondary mb-3">📌 Histórico de Doações</h5>
          <div v-if="doacoes.length">
            <div
              v-for="d in doacoes"
              :key="d.id"
              class="doacao-card"
            >
              <div class="doacao-header">
                <strong>{{ d.nome_doador || 'Anônimo' }}</strong>
                <span class="valor text-success">MZN {{ d.valor }}</span>
              </div>
              <div class="doacao-body">
                <p>
                  <i class="bi bi-calendar-check"></i>
                  {{ formatarData(d.data_doacao) }} — {{ d.meio }}
                </p>
              </div>
            </div>
          </div>
          <p v-else class="text-center text-muted">Nenhuma doação registrada.</p>
        </div>
      </div>
    </main>

    <!-- Sidebar - Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard class="p-2" />
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

import NavDashboard from '../../components/NavDashboard.vue';
import SidebarDashboard from '../../components/SidebarDashboard.vue';
import BarChart from '../../components/charts/BarChart.vue';
import LineChart from '../../components/charts/LineChart.vue';

const doacoes = ref([]);
const mensalChart = ref(null);
const anualChart = ref(null);
const historicoRef = ref(null);

function formatarData(dataISO) {
  const data = new Date(dataISO);
  return data.toLocaleDateString('pt-PT');
}

function gerarGraficos() {
  const meses = Array.from({ length: 12 }, (_, i) =>
    new Date(0, i).toLocaleString('pt', { month: 'short' })
  );
  const anoAtual = new Date().getFullYear();
  const mensalData = new Array(12).fill(0);
  const anualData = {};

  doacoes.value.forEach((d) => {
    const data = new Date(d.data_doacao);
    if (!isNaN(data)) {
      if (data.getFullYear() === anoAtual) {
        mensalData[data.getMonth()] += d.valor;
      }
      const ano = data.getFullYear();
      anualData[ano] = (anualData[ano] || 0) + d.valor;
    }
  });

  mensalChart.value = {
    labels: meses,
    datasets: [
      {
        label: 'MZN',
        data: mensalData,
        backgroundColor: '#28a745'
      }
    ]
  };

  anualChart.value = {
    labels: Object.keys(anualData),
    datasets: [
      {
        label: 'MZN',
        data: Object.values(anualData),
        backgroundColor: '#007bff'
      }
    ]
  };
}

async function carregar() {
  try {
    const res = await axios.get('/financeiro');
    doacoes.value = res.data;
    gerarGraficos();
  } catch (err) {
    console.error('Erro ao carregar dados:', err);
  }
}

async function exportarPDF() {
  const el = historicoRef.value;
  const canvas = await html2canvas(el);
  const imgData = canvas.toDataURL('image/png');
  const pdf = new jsPDF();
  const width = pdf.internal.pageSize.getWidth();
  const height = (canvas.height * width) / canvas.width;
  pdf.addImage(imgData, 'PNG', 0, 10, width, height);
  pdf.save('historico-doacoes.pdf');
}

onMounted(carregar);
</script>

<style scoped>
.doacao-card {
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 20px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
}
.doacao-header {
  display: flex;
  justify-content: space-between;
}
.doacao-body {
  font-size: 0.9rem;
}
</style>
