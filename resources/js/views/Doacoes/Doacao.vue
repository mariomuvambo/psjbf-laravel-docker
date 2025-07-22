<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar - Desktop -->
    <div class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard class="p-2" />
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1 bg-light min-vh-100">
      <NavDashboard class="mb-3" />

        <div class="container pt-5 mt-5 pb-5">

        <div class="row g-4">
          <!-- Formulário -->
          <div class="col-md-6">
            <div class="card shadow rounded-4">
              <div class="card-body">
                <h5 class="card-title mb-3">Faça uma Doação 🙏</h5>
                <form @submit.prevent="enviarDoacao">
                  <div class="mb-3">
                    <label for="nome_doador" class="form-label">Nome do Doador (opcional)</label>
                    <input
                      type="text"
                      v-model="nome"
                      class="form-control"
                      id="nome_doador"
                      placeholder="Ex: Maria João"
                    />
                  </div>

                  <div class="mb-3">
                    <label for="valor" class="form-label">Valor (MZN)</label>
                    <input
                      type="number"
                      v-model="valor"
                      class="form-control"
                      id="valor"
                      required
                      placeholder="Ex: 500"
                    />
                  </div>

                  <div class="mb-3">
                    <label for="metodo" class="form-label">Método de Pagamento</label>
                    <select v-model="metodo" class="form-select" required>
                      <option disabled value="">Selecione um método</option>
                      <option value="Dinheiro">💵 Dinheiro</option>
                      <option value="Transferência">🏦 Transferência</option>
                      <option value="M-Pesa">📱 M-Pesa</option>
                      <option value="eFectivo">💳 eFectivo</option>
                    </select>
                  </div>

                  <button type="submit" class="btn btn-success w-100 shadow-sm">
                    Enviar Doação
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Cartões ilustrativos -->
          <div class="col-md-6">
            <div class="row g-3 justify-content-center">
              <div
                class="col-6 col-md-4"
                v-for="(m, i) in metodosDisplay"
                :key="i"
                @click="redirectToPayment(m.link)"
              >
                <div class="card payment-card text-center p-3 h-100">
                  <img :src="m.img" class="img-fluid mb-2" :alt="m.nome" />
                  <small class="fw-bold">{{ m.nome }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lista de Doações -->
        <div class="mt-5">
          <h5 class="text-center mb-3 text-secondary">Histórico de Doações</h5>
          <div
            class="doacao-card"
            v-for="d in doacoes"
            :key="d.id"
          >
            <div class="doacao-header">
              <strong>{{ d.nome_doador || 'Anônimo' }}</strong>
              <span class="valor text-success">MZN {{ d.valor }}</span>
            </div>
            <div class="doacao-body">
              <p><i class="bi bi-calendar-check"></i> {{ formatarData(d.data_doacao) }} — {{ d.meio }}</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Sidebar - Mobile -->
    <div class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard class="p-2" />
    </div>
  </div>
</template>

<script>
import NavDashboard from '../../components/NavDashboard.vue';
import SidebarDashboard from '../../components/SidebarDashboard.vue';
import axios from 'axios';

export default {
  components: { NavDashboard, SidebarDashboard },
  data() {
    return {
      valor: '',
      metodo: '',
      nome: '',
      doacoes: [],
      metodosDisplay: [
        {
          nome: 'M-Pesa',
          img: 'https://th.bing.com/th/id/OIP.hTnWzDkW95lDrt_dd2SjAwHaD4',
          link: 'https://www.vm.co.mz/servicos/m-pesa'
        },
        {
          nome: 'eFectivo',
          img: 'https://www.efectivo.co.mz/assets/images/logo.png',
          link: 'https://www.efectivo.co.mz'
        },
        {
          nome: 'Transferência',
          img: 'https://cdn-icons-png.flaticon.com/512/254/254638.png',
          link: '#'
        },
        {
          nome: 'Dinheiro',
          img: 'https://cdn-icons-png.flaticon.com/512/2898/2898601.png',
          link: '#'
        }
      ]
    };
  },
  methods: {
    async enviarDoacao() {
      try {
        await axios.post('/doacoes', {
          valor: this.valor,
          meio: this.metodo,
          nome_doador: this.nome
        });
        alert('🙏 Doação enviada com sucesso!');
        this.valor = '';
        this.metodo = '';
        this.nome = '';
        this.carregar();
      } catch (error) {
        console.error(error);
        alert('Erro ao enviar a doação.');
      }
    },
    redirectToPayment(link) {
      if (link !== '#') {
        window.open(link, '_blank');
      }
    },
    carregar() {
      axios.get('/doacoes').then((res) => {
        this.doacoes = res.data;
      });
    },
    formatarData(dataISO) {
      const data = new Date(dataISO);
      return data.toLocaleDateString('pt-PT');
    }
  },
  mounted() {
    this.carregar();
  }
};
</script>

<style scoped>
.text-maroon {
  color: #8B0000;
}
.payment-card {
  cursor: pointer;
  transition: 0.2s ease;
  border-radius: 10px;
  border: 1px solid #ccc;
  background-color: #fff;
}
.payment-card:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.payment-card img {
  height: 60px;
  object-fit: contain;
}
.doacao-card {
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 20px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}
.doacao-header {
  display: flex;
  justify-content: space-between;
}
.doacao-body {
  font-size: 0.9rem;
}
</style>
