import { createRouter, createWebHistory } from "vue-router";

// Páginas públicas
import Home from "@/views/Home.vue";
import Login from "@/views/Login.vue";
import Register from "@/views/Register.vue";
import ForgotPassword from "@/views/ForgotPassword.vue";
import ResetPassword from "@/views/ResetPassword.vue";
import GoogleSuccess from "@/views/GoogleSuccess.vue";

// Layout principal do dashboard
import DashboardLayout from "@/layouts/DashboardLayout.vue";
import Dashboard from "@/views/Dashboard.vue";

// Módulos do Dashboard
import Avisos from "@/views/Avisos.vue";
import AvisosList from "@/views/list/AvisosList.vue";
import Ministerio from "@/views/Ministerio.vue";
import UserMinisterList from "@/views/list/userMinisterList.vue";
import Eventos from "@/views/Eventos/Eventos.vue";
import CreateEvent from "@/views/Eventos/CreateEvent.vue";
import Doacao from "@/views/Doacoes/Doacao.vue";
import FinancialHistory from "@/views/Financial/FinancialHistory.vue";
import CasamentoRegister from "@/views/Casamentos/CasamentoRegister.vue";
import BatismoRegister from "@/views/Baptismo/BatismoRegister.vue";
import AdminUsuarios from "@/views/Admin/AdminUsuarios.vue";
import PerfilUser from "@/views/PerfilUser.vue";
import Missas from "@/views/Missas/Missas.vue";
import Leituras from "@/views/Missas/Leituras.vue";
import AniversarianteList from "@/views/Aniversariantes/AniversarianteList.vue";
import OracoesView from "@/views/Oracao/OracoesView.vue";

// Módulos sacerdotais
import PainelSacerdote from "@/views/Sacerdote/PainelSacerdote.vue";
import CertificadoBaptismo from "@/views/Sacerdote/CertificadoBaptismo.vue";
import PedidosPendentes from "@/views/Sacerdote/PedidosPedentes.vue";
import PedidosAprovados from "@/views/Sacerdote/PedidosAprovados.vue";
import TodosBatizados from "@/views/Sacerdote/TodosBatizados.vue";
import AnaliseBatptismo from "@/views/Sacerdote/AnaliseBatptismo.vue";
import PainelCasamento from "@/views/Sacerdote/casamento/PainelCasamento.vue";

const routes = [
  // 🌐 Rotas públicas
  { path: "/", name: "Home", component: Home },
  { path: "/login", name: "Login", component: Login },
  { path: "/register", name: "Register", component: Register },
  { path: "/forgot-password", name: "ForgotPassword", component: ForgotPassword },
  { path: "/reset-password", name: "ResetPassword", component: ResetPassword },
  { path: "/google-success", name: "GoogleSuccess", component: GoogleSuccess },

  // 🔒 Rotas com layout fixo (painel)
  {
    path: "/dashboard",
    component: DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      { path: "", name: "DashboardHome", component: Dashboard },
      { path: "avisos/registo", name: "AvisosRegisto", component: Avisos },
      { path: "avisos", name: "AvisosList", component: AvisosList },
      { path: "ministerio", name: "Ministerio", component: Ministerio },
      { path: "userMinister", name: "UserMinister", component: UserMinisterList },
      { path: "eventos", name: "Eventos", component: Eventos },
      { path: "eventos/create", name: "CreateEvent", component: CreateEvent },
      { path: "doacoes", name: "Doacao", component: Doacao },
      { path: "financeiro", name: "FinancialHistory", component: FinancialHistory },
      { path: "casamento/register", name: "CasamentoRegister", component: CasamentoRegister },
      { path: "batismo/register", name: "BatismoRegister", component: BatismoRegister },
      { path: "perfil", name: "PerfilUser", component: PerfilUser },
      { path: "missas", name: "Missas", component: Missas },
      { path: "leituras", name: "Leituras", component: Leituras },
      { path: "aniversariantes", name: "AniversarianteList", component: AniversarianteList },

      // 👑 Admin
      {
        path: "admin/usuarios",
        name: "AdminUsuarios",
        component: AdminUsuarios,
        meta: { requiresAdmin: true },
      },

      // ✝️ Sacerdote
      { path: "sacerdote/painel", name: "PainelSacerdote", component: PainelSacerdote },
      { path: "sacerdote/batismos/aprovados", name: "PedidosAprovados", component: PedidosAprovados },
      { path: "sacerdote/batismos/pendentes", name: "PedidosPendentes", component: PedidosPendentes },
      { path: "sacerdote/batismos/em-analise", name: "AnaliseBatptismo", component: AnaliseBatptismo },
      { path: "sacerdote/batismos/todos", name: "TodosBatizados", component: TodosBatizados },
      { path: "sacerdote/certificados", name: "CertificadoBaptismo", component: CertificadoBaptismo },
      { path: "sacerdote/casamentos", name: "PainelCasamento", component: PainelCasamento },
      { path: "sacerdote/oracoes", name: "OracoesView", component: OracoesView },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// 🧩 Middleware de autenticação
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  const user = JSON.parse(localStorage.getItem("user"));

  if (to.meta.requiresAuth && !token) {
    next("/login");
  } else if (to.meta.requiresAdmin && user?.role !== "admin") {
    next("/dashboard");
  } else {
    next();
  }
});

export default router;
