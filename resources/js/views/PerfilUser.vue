<template>
  <div class="d-flex flex-column flex-lg-row">
    <!-- Sidebar Desktop -->
    <aside class="d-none d-lg-block flex-shrink-0">
      <SidebarDashboard />
    </aside>

    <!-- Conteúdo Principal -->
    <main class="flex-grow-1 min-vh-100 bg-light">
      <NavDashboard />

      <div class="container py-5 mt-4">
        <div class="card shadow-sm border-0 p-4">
          <!-- LOADING -->
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
          </div>

          <!-- PERFIL -->
          <div v-else-if="profile" class="d-flex flex-column flex-md-row align-items-center">
            <!-- Foto -->
            <div class="text-center me-md-4 mb-4 mb-md-0 position-relative">
              <img
                :src="preview || profile.foto_url || placeholder"
                alt="Foto do Usuário"
                class="rounded-circle shadow-sm"
                style="width: 150px; height: 150px; object-fit: cover;"
                @error="handleImageError"
              />
              <label
                v-if="editMode"
                class="btn btn-sm btn-light border position-absolute bottom-0 end-0 mb-1 me-2"
              >
                <i class="bi bi-camera"></i>
                <input type="file" class="d-none" accept="image/*" @change="onFileChange" />
              </label>
            </div>

            <!-- Informações -->
            <div class="flex-grow-1">
              <div v-if="!editMode" class="text-center text-md-start">
                <h3 class="fw-bold mb-2 text-primary">
                  {{ profile.nome }} {{ profile.apelido }}
                </h3>
                <p class="text-muted mb-1">📅 {{ formatDate(profile.data_nascimento) }}</p>
                <p class="mb-1">📧 {{ profile.email }}</p>
                <p class="mb-1">📱 {{ profile.telefone }}</p>
                <p class="mb-1">🏠 {{ profile.endereco }}</p>
                <p class="mb-1">⚧️ {{ profile.genero }}</p>

                <button class="btn btn-outline-primary btn-sm mt-3" @click="editMode = true">
                  ✏️ Editar Perfil
                </button>
              </div>

              <!-- FORMULÁRIO DE EDIÇÃO -->
              <form v-else class="text-start" @submit.prevent="updateProfile">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nome</label>
                    <input v-model="profile.nome" type="text" class="form-control" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Apelido</label>
                    <input v-model="profile.apelido" type="text" class="form-control" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Telefone</label>
                    <input v-model="profile.telefone" type="text" class="form-control" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Endereço</label>
                    <input v-model="profile.endereco" type="text" class="form-control" />
                  </div>
                </div>

                <div class="text-center mt-3">
                  <button class="btn btn-success me-2" :disabled="saving">
                    <span v-if="!saving">💾 Salvar</span>
                    <span v-else>⏳ Salvando...</span>
                  </button>
                  <button
                    type="button"
                    class="btn btn-secondary"
                    @click="cancelEdit"
                    :disabled="saving"
                  >
                    ❌ Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- SEM PERFIL -->
          <div v-else class="text-center text-muted py-5">
            <i class="bi bi-person-x fs-1 d-block mb-3"></i>
            Nenhum perfil encontrado.
          </div>
        </div>

        <!-- ALERTA DE SUCESSO -->
        <div
          v-if="successMessage"
          class="alert alert-success mt-4 text-center"
          role="alert"
        >
          {{ successMessage }}
        </div>
      </div>
    </main>

    <!-- Sidebar Mobile -->
    <aside class="d-block d-lg-none flex-shrink-0">
      <SidebarDashboard />
    </aside>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import NavDashboard from "../components/NavDashboard.vue";
import SidebarDashboard from "../components/SidebarDashboard.vue";

const profile = ref(null);
const loading = ref(true);
const saving = ref(false);
const editMode = ref(false);
const preview = ref(null);
const successMessage = ref("");
const placeholder = "/img/placeholder-user.jpg";

const handleImageError = (e) => (e.target.src = placeholder);

onMounted(async () => {
  try {
    const { data } = await axios.get("/profilusers/me");
    profile.value = data;
  } catch (error) {
    console.error("❌ Erro ao carregar perfil:", error);
  } finally {
    loading.value = false;
  }
});

// Preview da imagem
const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    profile.value.foto = file;
    preview.value = URL.createObjectURL(file);
  }
};

// Cancelar edição
const cancelEdit = () => {
  preview.value = null;
  editMode.value = false;
  successMessage.value = "";
  onMounted(); // recarrega dados do backend
};

// Atualizar o perfil
const updateProfile = async () => {
  try {
    saving.value = true;
    successMessage.value = "";

    const formData = new FormData();
    formData.append("nome", profile.value.nome || "");
    formData.append("apelido", profile.value.apelido || "");
    formData.append("telefone", profile.value.telefone || "");
    formData.append("endereco", profile.value.endereco || "");
    if (profile.value.foto instanceof File) {
      formData.append("foto", profile.value.foto);
    }

    const { data } = await axios.post("/profiluser/update", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    profile.value = data.user;
    successMessage.value = "✅ Perfil atualizado com sucesso!";
    editMode.value = false;
  } catch (err) {
    console.error("❌ Erro ao atualizar perfil:", err);
    alert("Erro ao atualizar o perfil.");
  } finally {
    saving.value = false;
  }
};

const formatDate = (d) =>
  !d
    ? "-"
    : new Date(d).toLocaleDateString("pt-PT", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
</script>

<style scoped>
.card {
  border-radius: 16px;
}
.text-primary {
  color: #8b0000 !important;
}
.btn-light {
  background-color: #fff;
}
</style>
