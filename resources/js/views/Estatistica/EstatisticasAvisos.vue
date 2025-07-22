<template>
    <div class="container mt-4">
      <h2 class="text-center text-primary mb-4">📊 Estatísticas dos Avisos</h2>
      <canvas id="graficoAvisos"></canvas>
    </div>
  </template>
  
  <script>
  import { Chart, registerables } from 'chart.js';
  import axios from 'axios';
  Chart.register(...registerables);
  
  export default {
    data() {
      return {
        chart: null,
      };
    },
    methods: {
      async carregarEstatisticas() {
        try {
          const { data } = await axios.get('/estatisticas-avisos');
          
  
          const labels = data.avisos_por_mes.map(item => item.mes);
          const totalAvisos = data.avisos_por_mes.map(item => item.total);
          const totalLidos = labels.map(label => {
            const encontrado = data.avisos_lidos.find(item => item.mes === label);
            return encontrado ? encontrado.total : 0;
          });
  
          const ctx = document.getElementById('graficoAvisos');
          if (this.chart) this.chart.destroy(); // se existir gráfico anterior
  
          this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [
                {
                  label: 'Total de Avisos Criados',
                  data: totalAvisos,
                  backgroundColor: '#007bff',
                },
                {
                  label: 'Total de Avisos Lidos',
                  data: totalLidos,
                  backgroundColor: '#28a745',
                },
              ],
            },
            options: {
              responsive: true,
              plugins: {
                legend: {
                  position: 'bottom',
                },
              },
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
            },
          });
  
        } catch (error) {
          console.error("Erro ao carregar estatísticas:", error);
        }
      },
    },
    mounted() {
      this.carregarEstatisticas();
    },
  };
  </script>
  