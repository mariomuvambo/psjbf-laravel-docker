<template>
  <div class="container">
    <header class="header">
      <h1>Faça a Sua Doação</h1>
      <p>
        Sua contribuição ajuda a manter as obras da nossa paróquia e apoiar os mais necessitados.
      </p>
    </header>

    <blockquote class="versiculo">
      “Cada um contribua segundo tiver proposto no coração, não com tristeza ou por necessidade;
      porque Deus ama a quem dá com alegria.” — 2 Coríntios 9:7
    </blockquote>

    <form @submit.prevent="salvarDoacao" class="formulario">
      <input v-model="form.nome_doador" type="text" placeholder="Seu nome completo (opcional)" class="input" />

      <div class="input-wrapper">
        <span class="prefix">MT</span>
        <input
          v-model.number="form.valor"
          type="number"
          step="0.01"
          placeholder="Valor da doação"
          class="input pl"
          required
        />
      </div>

      <input v-model="form.data_doacao" type="date" class="input" required />

      <select v-model="form.meio" class="input" required>
        <option disabled value="">Escolha o meio de doação</option>
        <option>Dinheiro</option>
        <option>Transferência Bancária</option>
        <option>M-Pesa</option>
        <option>eFectivo</option>
        <option>e-Mola</option>
        <option>PayPal</option>
        <option>BIM</option>
        <option>BCI</option>
      </select>

      <textarea
        v-model="form.descricao"
        placeholder="Intenção da doação, nome do santo, ação de graças..."
        class="input descricao"
        rows="3"
      ></textarea>

      <div class="botoes">
        <button type="submit" class="btn btn-primaria">
          {{ form.id ? 'Atualizar Doação' : 'Fazer Doação' }}
        </button>
        <button type="button" @click="resetar" class="btn btn-secundaria">Cancelar</button>
      </div>
    </form>

    <section class="historico">
      <h2>Histórico de Doações</h2>
      <table class="tabela">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Meio</th>
            <th>Intenção</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doacao in doacoes" :key="doacao.id">
            <td>{{ doacao.nome_doador || 'Anônimo' }}</td>
            <td class="valor">MT {{ doacao.valor.toFixed(2) }}</td>
            <td>{{ doacao.data_doacao }}</td>
            <td>{{ doacao.meio }}</td>
            <td>{{ doacao.descricao || '-' }}</td>
            <td class="acoes">
              <button @click="editar(doacao)" class="link editar">Editar</button>
              <button @click="removerDoacao(doacao.id)" class="link remover">Remover</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const doacoes = ref([])

const form = ref({
  id: null,
  nome_doador: '',
  valor: null,
  data_doacao: '',
  meio: '',
  descricao: ''
})

const carregarDoacoes = async () => {
  const res = await axios.get('doacoes')
  doacoes.value = res.data
}

const salvarDoacao = async () => {
  if (form.value.id) {
    await axios.put(`/doacoes/${form.value.id}`, form.value)
  } else {
    await axios.post('/doacoes', form.value)
  }
  resetar()
  carregarDoacoes()
}

const editar = (doacao) => {
  form.value = { ...doacao }
}

const removerDoacao = async (id) => {
  await axios.delete(`doacoes/${id}`)
  carregarDoacoes()
}

const resetar = () => {
  form.value = {
    id: null,
    nome_doador: '',
    valor: null,
    data_doacao: '',
    meio: '',
    descricao: ''
  }
}

onMounted(carregarDoacoes)
</script>

<style scoped>
.container {
  max-width: 900px;
  margin: 2rem auto;
  padding: 1rem;
}

.header {
  text-align: center;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2.25rem;
  color: #6b21a8;
  margin-bottom: 0.5rem;
}

.header p {
  color: #4b5563;
}

.versiculo {
  background-color: #fffbea;
  border-left: 6px solid #facc15;
  padding: 1rem;
  font-style: italic;
  color: #92400e;
  border-radius: 0.5rem;
  margin-bottom: 2rem;
}

.formulario {
  background: white;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  background-color: #f9fafb;
}

.input-wrapper {
  position: relative;
}

.prefix {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}

.pl {
  padding-left: 2.5rem !important;
}

.descricao {
  grid-column: 1 / -1;
}

.botoes {
  grid-column: 1 / -1;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 1rem;
}

.btn {
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primaria {
  background-color: #4c1d95;
  color: white;
}

.btn-primaria:hover {
  background-color: #5b21b6;
}

.btn-secundaria {
  background-color: #9ca3af;
  color: white;
}

.btn-secundaria:hover {
  background-color: #6b7280;
}

.historico {
  margin-top: 3rem;
  background: #f9fafb;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.historico h2 {
  font-size: 1.5rem;
  color: #374151;
  margin-bottom: 1rem;
}

.tabela {
  width: 100%;
  border-collapse: collapse;
}

.tabela th,
.tabela td {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.valor {
  color: #16a34a;
  font-weight: bold;
}

.acoes {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.link {
  font-weight: bold;
  cursor: pointer;
  border: none;
  background: none;
}

.editar {
  color: #2563eb;
}

.editar:hover {
  text-decoration: underline;
}

.remover {
  color: #dc2626;
}

.remover:hover {
  text-decoration: underline;
}
</style>
