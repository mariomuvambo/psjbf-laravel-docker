<template>
  <NavbarHome />

  <div class="register-container">
    <div class="register-box">
      <h1 class="register-title">Registro</h1>

      <form @submit.prevent="register" class="register-form">
        <!-- Nome e Apelido -->
        <div class="row">
          <input type="text" placeholder="Nome" v-model="form.nome" class="form-control" required />
          <input type="text" placeholder="Apelido" v-model="form.apelido" class="form-control" />
        </div>

        <!-- Email e Telefone -->
        <div class="row">
          <input type="email" placeholder="Email" v-model="form.email" class="form-control" required />
          <input type="text" placeholder="Telefone" v-model="form.telefone" class="form-control" />
        </div>

        <!-- Endereço -->
        <input type="text" placeholder="Endereço" v-model="form.endereco" class="form-control" />

        <!-- Gênero e Tipo de Usuário -->
        <div class="row">
          <select v-model="form.genero" class="form-control">
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
        <div class="form-group">
          <label for="data_nascimento">Data de Nascimento</label>
          <input type="date" id="data_nascimento" v-model="form.data_nascimento" class="form-control" />
        </div>

        <!-- Foto -->
        <div class="form-group">
          <input type="file" @change="handleFileUpload" accept="image/*" class="form-control" />
          <small v-if="fileError" class="error">{{ fileError }}</small>
        </div>

        <!-- Senha -->
        <div class="row">
          <input type="password" placeholder="Senha" v-model="form.password" class="form-control" required />
          <input type="password" placeholder="Confirmar Senha" v-model="form.password_confirmation" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
          {{ isSubmitting ? "Registrando..." : "Registrar" }}
        </button>

        <p v-if="successMessage" class="success">{{ successMessage }}</p>
        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
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

        const response = await axios.post(
          `${import.meta.env.VITE_API_URL || ""}/api/register`,
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
              Accept: "application/json",
            },
          }
        );

        this.successMessage = "Registro efetuado com sucesso! Redirecionando...";
        localStorage.setItem("token", response.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.user));

        setTimeout(() => this.$router.push("/login"), 3000);
      } catch (error) {
        console.error("Erro no registro:", error.response?.data || error);
        this.errorMessage =
          error.response?.data?.message ||
          error.response?.data?.errors?.foto?.[0] ||
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
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #8b0000;
  padding: 2rem;
}

.register-box {
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
}

.register-title {
  color: #8b0000;
  font-family: "Georgia", serif;
  font-size: 2rem;
  text-align: center;
  margin-bottom: 2rem;
  text-transform: uppercase;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-control {
  padding: 0.9rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 1rem;
}

.form-control:focus {
  border-color: #8b0000;
  outline: none;
}

.btn-primary {
  background-color: #8b0000;
  color: #fff;
  border: none;
  padding: 1rem;
  font-weight: bold;
  border-radius: 6px;
  cursor: pointer;
}

.btn-primary:hover {
  background-color: #600000;
}

.success {
  color: green;
  text-align: center;
  font-weight: bold;
}

.error {
  color: red;
  text-align: center;
  font-weight: bold;
}

@media (max-width: 600px) {
  .row {
    grid-template-columns: 1fr;
  }
}
</style>
