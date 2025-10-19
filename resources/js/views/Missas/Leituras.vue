<template>
  <div class="d-flex flex-column flex-lg-row">
    <SidebarDashboard />

    <div class="flex-grow-1">
      <NavDashboard />

      <div class="container mt-5 py-4 ">
        <!-- Card: Informações Gerais -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-church text-white">
            <h5 class="mb-0">Informações da Missa</h5>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="date" class="form-label">Data da Missa</label>
                <input
                  type="date"
                  id="date"
                  v-model="form.date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.date }"
                />
                <div class="invalid-feedback" v-if="errors.date">
                  {{ errors.date }}
                </div>
              </div>

              <div class="col-md-6">
                <label for="time" class="form-label">Hora da Missa</label>
                <input
                  type="time"
                  id="time"
                  v-model="form.time"
                  class="form-control"
                  :class="{ 'is-invalid': errors.time }"
                />
                <div class="invalid-feedback" v-if="errors.time">
                  {{ errors.time }}
                </div>
              </div>

              <div class="col-md-12 mt-3">
                <label for="liturgicalDay" class="form-label">Dia Litúrgico</label>
                <input
                  type="text"
                  id="liturgicalDay"
                  v-model="form.liturgicalDay"
                  class="form-control"
                  :class="{ 'is-invalid': errors.liturgicalDay }"
                  placeholder="Ex: 27º Domingo do Tempo Comum"
                />
                <div class="invalid-feedback" v-if="errors.liturgicalDay">
                  {{ errors.liturgicalDay }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card: Primeira Leitura -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-light-gold text-dark">
            <h6 class="mb-0">1ª Leitura</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Leitura</label>
              <input
                type="text"
                v-model="form.firstReading"
                class="form-control"
                placeholder="Ex: Isaías 5, 1-7"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Leitor</label>
              <input
                type="text"
                v-model="form.firstReader"
                class="form-control"
              />
            </div>
          </div>
        </div>

        <!-- Card: Salmo Responsorial -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-light-blue text-dark">
            <h6 class="mb-0">Salmo Responsorial</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Salmo</label>
              <input
                type="text"
                v-model="form.psalm"
                class="form-control"
                placeholder="Ex: Salmo 79"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Responsável</label>
              <input
                type="text"
                v-model="form.psalmReader"
                class="form-control"
              />
            </div>
          </div>
        </div>

        <!-- Card: Segunda Leitura -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-light-gold text-dark">
            <h6 class="mb-0">2ª Leitura</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Leitura</label>
              <input
                type="text"
                v-model="form.secondReading"
                class="form-control"
                placeholder="Ex: Filipenses 4, 6-9"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Leitor</label>
              <input
                type="text"
                v-model="form.secondReader"
                class="form-control"
              />
            </div>
          </div>
        </div>

        <!-- Card: Evangelho -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-light-vine text-white">
            <h6 class="mb-0">Evangelho</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Evangelho</label>
              <input
                type="text"
                v-model="form.gospel"
                class="form-control"
                placeholder="Ex: Mateus 21, 33-43"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Celebrante</label>
              <input
                type="text"
                v-model="form.celebrant"
                class="form-control"
              />
            </div>
          </div>
        </div>

        <!-- Card: Observações -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">Observações Litúrgicas</h6>
          </div>
          <div class="card-body">
            <textarea
              v-model="form.notes"
              class="form-control"
              rows="3"
              placeholder="Procissão, cor litúrgica, intenções especiais..."
            ></textarea>
          </div>
        </div>

        <!-- Botão de Envio -->
        <div class="text-end mb-5">
          <button type="button" class="btn btn-church px-4" @click="submitForm">
            Salvar Leituras
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SidebarDashboard from '../../components/SidebarDashboard.vue';
import NavDashboard from '../../components/NavDashboard.vue';

const form = ref({
  date: '',
  time: '',
  liturgicalDay: '',
  firstReading: '',
  firstReader: '',
  psalm: '',
  psalmReader: '',
  secondReading: '',
  secondReader: '',
  gospel: '',
  celebrant: '',
  notes: '',
});

const errors = ref({});
const masses = ref([]);
const loading = ref(false);
const message = ref("");

const validate = () => {
  errors.value = {};
  if (!form.value.date) errors.value.date = 'A data é obrigatória.';
  if (!form.value.time) errors.value.time = 'A hora é obrigatória.';
  if (!form.value.liturgicalDay) errors.value.liturgicalDay = 'Informe o dia litúrgico.';
  return Object.keys(errors.value).length === 0;
};

const submitForm = async () => {
  if (!validate()) return;

  try {
    loading.value = true;
    const response = await axios.post('/masses', {
      date: form.value.date,
      time: form.value.time,
      liturgical_day: form.value.liturgicalDay,
      first_reading: form.value.firstReading,
      first_reader: form.value.firstReader,
      psalm: form.value.psalm,
      psalm_reader: form.value.psalmReader,
      second_reading: form.value.secondReading,
      second_reader: form.value.secondReader,
      gospel: form.value.gospel,
      celebrant: form.value.celebrant,
      notes: form.value.notes,
    });

    message.value = response.data.message;

    await fetchMasses();

    form.value = {
      date: '',
      time: '',
      liturgicalDay: '',
      firstReading: '',
      firstReader: '',
      psalm: '',
      psalmReader: '',
      secondReading: '',
      secondReader: '',
      gospel: '',
      celebrant: '',
      notes: '',
    };
  } catch (error) {
    console.error(error);
    message.value = "Erro ao salvar a missa.";
  } finally {
    loading.value = false;
  }
};

const fetchMasses = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/masses');
    masses.value = response.data;
  } catch (error) {
    console.error("Erro ao buscar missas:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchMasses();
});
</script>

<style scoped>
.bg-church { background-color: #6d2c2f; }
.bg-light-gold { background-color: #f8f1e3; }
.bg-light-blue { background-color: #dcecfb; }
.bg-light-vine { background-color: #843c45; }
.btn-church { background-color: #1e3d59; color: white; }
.btn-church:hover { background-color: #142b40; }
</style>
