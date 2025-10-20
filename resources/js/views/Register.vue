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
        <div class="form-group">
          <input type="text" placeholder="Endereço" v-model="form.endereco" class="form-control" />
        </div>

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
          <input type="file" @change="handleFileUpload" class="form-control" />
        </div>

        <!-- Senha e confirmação -->
        <div class="row">
          <input type="password" placeholder="Senha" v-model="form.password" class="form-control" required />
          <input type="password" placeholder="Confirme a Senha" v-model="form.password_confirmation" class="form-control" required />
        </div>

        <!-- Botão e mensagens -->
        <button type="submit" class="btn btn-primary w-100">Registrar</button>
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
    };
  },
  methods: {
    handleFileUpload(event) {
      this.form.foto = event.target.files[0];
    },
    async register() {
      try {
        const formData = new FormData();

        for (const key in this.form) {
          if (this.form[key] !== null) {
            formData.append(key, this.form[key]);
          }
        }
        const response = await axios.post("/register", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Accept: "application/json",
          },
        });


       


        if (response.data && response.data.token) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("user", JSON.stringify(response.data.user));
        }

        this.successMessage = "Registro efetuado com sucesso! Você será redirecionado em 3 segundos.";
        this.errorMessage = "";

        // Espera 3 segundos antes de redirecionar
        await new Promise(resolve => setTimeout(resolve, 3000));

        this.$router.push("/login");

      

      } catch (error) {
        this.errorMessage =
          error.response?.data?.message ||
          "Erro ao registrar. Verifique os dados e tente novamente.";

          this.successMessage = "";
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
  box-sizing: border-box;
}

.register-box {
  background: #ffffff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
}

.register-title {
  color: #8b0000;
  font-family: 'Georgia', serif;
  font-size: 2.2rem;
  text-align: center;
  margin-bottom: 2rem;
  text-transform: uppercase;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-control {
  padding: 0.9rem;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.3s ease;
  width: 100%;
  box-sizing: border-box;
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
  font-size: 1rem;
  font-weight: bold;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #600000;
}

.success {
  color: green;
  font-weight: bold;
  margin-top: 1rem;
  text-align: center;
}

.error {
  color: red;
  font-weight: bold;
  margin-top: 1rem;
  text-align: center;
}

@media (max-width: 600px) {
  .row {
    grid-template-columns: 1fr;
  }
}


</style>
