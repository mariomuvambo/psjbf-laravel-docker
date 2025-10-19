<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar -->
    <SidebarDashboard />

    <div class="flex-grow-1 bg-light min-vh-100">
      <!-- Navbar -->
      <NavDashboard />

      <div class="container py-4">
        <h2 class="mb-4 text-center text-primary fw-bold">
          Painel da Missa de Hoje
        </h2>

        <div class="row g-4">
          <!-- Horário da Missa -->
          <InfoCard
            icon="fa-church"
            title="Horário da Missa"
            :items="horarioMissaItems"
            color="success"
          />

          <!-- Leituras do Dia -->
          <InfoCard
            icon="fa-book-bible"
            title="Leituras do Dia"
            :items="leiturasDoDia"
            color="primary"
          />

          <!-- Liturgia -->
          <InfoCard
            icon="fa-cross"
            title="Liturgia"
            :items="liturgiaItems"
            color="warning"
          />

          <!-- Intenções da Missa -->
          <InfoCard
            icon="fa-praying-hands"
            title="Intenções da Missa"
            :items="ultimasOracoesItems"
            color="danger"
          />

          <!-- Padre Responsável -->
          <InfoCard
            icon="fa-user"
            title="Padre Responsável"
            :items="padreItems"
            color="info"
          />

          <!-- Músicas da Missa -->
          <InfoCard
            icon="fa-music"
            title="Músicas da Missa"
            :items="musicasItems"
            color="secondary"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import SidebarDashboard from '../../components/SidebarDashboard.vue'
import NavDashboard from '../../components/NavDashboard.vue'
import InfoCard from '../../components/InfoCard.vue'

// 🔹 Dados reativos para os cards
const horarioMissaItems = ref([])
const leiturasDoDia = ref([])
const liturgiaItems = ref([])
const ultimasOracoesItems = ref([])
const padreItems = ref([])
const musicasItems = ref([])

// 🔹 Função auxiliar para formatar data (Dom, 05/10/2025)
function formatarDataCurta(data) {
  const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']
  const d = new Date(data)
  const diaSemana = dias[d.getDay()]
  const dia = String(d.getDate()).padStart(2, '0')
  const mes = String(d.getMonth() + 1).padStart(2, '0')
  const ano = d.getFullYear()
  return `${diaSemana}, ${dia}/${mes}/${ano}`
}

// 🔹 Buscar missa do dia corrente
const fetchMissaHoje = async () => {
  try {
    const { data } = await axios.get('/missa/hoje');

    const dataFormatada = formatarDataCurta(data.date);
    const horaFormatada = data.time ? data.time : '---'; // caso null, mostra "---"

    // 🕊️ Horário da Missa
    horarioMissaItems.value = [
      { label: 'Data', value: dataFormatada },
      { label: 'Hora', value: horaFormatada }
    ];

    // 📖 Leituras do Dia
    leiturasDoDia.value = [
      { label: '1ª Leitura', value: `${data.first_reading || '---'} — ${data.first_reader || ''}` },
      { label: 'Salmo', value: `${data.psalm || '---'} — ${data.psalm_reader || ''}` },
      { label: '2ª Leitura', value: `${data.second_reading || '---'} — ${data.second_reader || ''}` },
      { label: 'Evangelho', value: data.gospel || '---' }
    ];

    // ✝️ Liturgia
    liturgiaItems.value = [
      { label: 'Dia Litúrgico', value: data.liturgical_day || '---' },
      { label: 'Cor Litúrgica', value: data.liturgical_color || 'Verde' },
      { label: 'Santo do Dia', value: data.saint_of_day || '---' }
    ];

    // 👨‍🦳 Padre Responsável
    padreItems.value = [
      { label: 'Padre Responsável', value: data.celebrant || '---' },
      { label: 'Mensagem', value: '“Que a paz de Cristo esteja em todos os lares!”' }
    ];

    // 🎵 Músicas
    musicasItems.value = [
      { label: 'Entrada', value: 'Vem, vem, vem Espírito Santo' },
      { label: 'Ofertório', value: 'Recebe Senhor' },
      { label: 'Comunhão', value: 'Pão da Vida' },
      { label: 'Final', value: 'A Barca' }
    ];
  } catch (error) {
    console.error('Erro ao buscar missa do dia:', error);
    horarioMissaItems.value = [
      { label: '', value: 'Nenhuma missa encontrada para hoje.' }
    ];
    padreItems.value = [
      { label: '', value: 'Nenhum padre registrado para hoje.' }
    ];
  }
};




// 🔹 Buscar intenções (orações)
const fetchUltimasOracoes = async () => {
  try {
    const { data } = await axios.get('/oracoes/ultimas')
    ultimasOracoesItems.value = data.map(o => ({
      label: '',
      value: o.mensagem
    }))
  } catch (error) {
    console.error('Erro ao buscar intenções:', error)
    ultimasOracoesItems.value = [
      { label: '', value: 'Erro ao carregar intenções da missa.' }
    ]
  }
}

// 🔹 Montagem inicial
onMounted(() => {
  fetchMissaHoje()
  fetchUltimasOracoes()
})
</script>
