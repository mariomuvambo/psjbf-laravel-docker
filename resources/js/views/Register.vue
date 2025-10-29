<template>
  <NavbarHome />

  <div class="register-container d-flex justify-content-center align-items-center">
    <div class="register-box p-4 shadow rounded">
      <h1 class="register-title text-center mb-4">Registro</h1>

      <form @submit.prevent="register">
        <!-- Nome e Apelido -->
        <div class="row mb-3">
          <input type="text" v-model="form.nome" placeholder="Nome" class="form-control me-2" required />
          <input type="text" v-model="form.apelido" placeholder="Apelido" class="form-control" />
        </div>

        <!-- Email e Telefone -->
        <div class="row mb-3">
          <input type="email" v-model="form.email" placeholder="Email" class="form-control me-2" required />
          <input type="text" v-model="form.telefone" placeholder="Telefone" class="form-control" />
        </div>

        <!-- Endereço -->
        <div class="mb-3">
          <input type="text" v-model="form.endereco" placeholder="Endereço" class="form-control" />
        </div>

        <!-- Gênero e Tipo de Usuário -->
        <div class="row mb-3">
          <select v-model="form.genero" class="form-control me-2">
            <option disabled value="">Gênero</option>
            <option>Masculino</option>
            <option>Feminino</option>
          </select>

          <select v-model="form.tipo_usuario" class="form-control" required>
            <option disabled value="">Tipo de Usuário</option>
            <option value="fiel">Fiel</option>
            <option value="voluntario">Voluntário</option>
            <option value="sacerdote">Sacerdote</option>
          </select>
        </div>

        <!-- Data de nascimento -->
        <div class="mb-3">
          <label for="data_nascimento" class="form-label">Data de Nascimento</label>
          <input type="date" id="data_nascimento" v-model="form.data_nascimento" class="form-control" />
        </div>

        <!-- Foto -->
        <div class="mb-3">
          <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" />
          <small v-if="fileError" class="text-danger">{{ fileError }}</small>
        </div>

        <!-- Senha -->
        <div class="row mb-3">
          <input type="password" v-model="form.password" placeholder="Senha" class="form-control me-2" required />
          <input type="password" v-model="form.password_confirmation" placeholder="Confirmar Senha" class="form-control" required />
        </div>

        <!-- Botão -->
        <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
          {{ isSubmitting ? "Registrando..." : "Registrar" }}
        </button>

        <!-- Mensagens -->
        <p v-if="successMessage" class="text-success text-center mt-2">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-danger text-center mt-2">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script>
import NavbarHome from "../components/NavbarHome.vue";

export default {
  components: { NavbarHome },
  data() {
    return {
      form: {
        nome: "",
        apelido: "",
        email: "",
        telefone: "",
        endereco: "",
        genero: "",
        data_nascimento: "",
        tipo_usuario: "",
        password: "",
        password_confirmation: "",
        foto: null,
      },
      successMessage: "",
      errorMessage: "",
      fileError: "",
      isSubmitting: false,
    };
  },
  methods: {
    handleFileUpload(e) {
      const file = e.target.files[0];
      if (file && file.size > 5 * 1024 * 1024) {
        this.fileError = "A imagem deve ter no máximo 5 MB.";
        this.form.foto = null;
        e.target.value = "";
      } else {
        this.fileError = "";
        this.form.foto = file;
      }
    },

    async register() {
      if (this.fileError) return;

      this.isSubmitting = true;
      this.successMessage = "";
      this.errorMessage = "";

      try {
        const formData = new FormData();
        Object.entries(this.form).forEach(([key, value]) => {
          if (value !== null && value !== "") formData.append(key, value);
        });

        const { data } = await this.$axios.post(
          `${import.meta.env.VITE_API_URL || ""}/register`,
          formData,
          { headers: { "Content-Type": "multipart/form-data" } }
        );

        this.successMessage = "Registro efetuado com sucesso! Redirecionando...";
        localStorage.setItem("token", data.token);
        localStorage.setItem("user", JSON.stringify(data.user));

        setTimeout(() => this.$router.push("/login"), 3000);
      } catch (err) {
        console.error(err);
        this.errorMessage =
          err.response?.data?.message ||
          err.response?.data?.errors?.foto?.[0] ||
          "Erro ao registrar. Verifique os dados e tente novamente.";
      } finally {
        this.isSubmitting = false;
      }
    },
  },
};
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  background-color: #8b0000;
  padding: 2rem;
}

.register-box {
  background-color: #fff;
  max-width: 600px;
  width: 100%;
  border-radius: 12px;
}

.register-title {
  color: #8b0000;
  font-family: "Georgia", serif;
  font-size: 2rem;
  text-align: center;
  margin-bottom: 2rem;
}

.form-control:focus {
  border-color: #8b0000;
  outline: none;
}

.btn-primary {
  background-color: #8b0000;
  border: none;
  font-weight: bold;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: #600000;
}
</style>
