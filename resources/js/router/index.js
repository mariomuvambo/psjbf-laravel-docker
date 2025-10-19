import { createRouter, createWebHistory } from 'vue-router';
import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue"; // Importação da página Register
import PerfilUser from "../views/PerfilUser.vue";
import Dashboard from '../views/Dashboard.vue';
import Avisos from '../views/Avisos.vue';
import AvisosList from '../views/list/AvisosList.vue';
import Ministerio from '../views/Ministerio.vue';
import UserMinisterList from '../views/list/userMinisterList.vue';
import Eventos from '../views/Eventos/Eventos.vue';
import Doacao from '../views/Doacoes/Doacao.vue';
import CreateEvent from '../views/Eventos/CreateEvent.vue';
import FinancialHistory from '../views/Financial/FinancialHistory.vue';
import CasamentoRegister from '../views/Casamentos/CasamentoRegister.vue';
import BatismoRegister from '../views/Baptismo/BatismoRegister.vue';
import PainelSacerdote from '../views/Sacerdote/PainelSacerdote.vue';
import CertificadoBaptismo from '../views/Sacerdote/CertificadoBaptismo.vue';
import PedidosPedentes from '../views/Sacerdote/PedidosPedentes.vue';
import PedidosAprovados from '../views/Sacerdote/PedidosAprovados.vue';
import TodosBatizados from '../views/Sacerdote/TodosBatizados.vue';
import PainelCasamento from '../views/Sacerdote/casamento/PainelCasamento.vue';
import AdminUsuarios from '../views/Admin/AdminUsuarios.vue';
import OracoesView from '../views/Oracao/OracoesView.vue';
import AniversarianteList from '../views/Aniversariantes/AniversarianteList.vue';
import AnaliseBatptismo from '../views/Sacerdote/AnaliseBatptismo.vue';
import Missas from '../views/Missas/Missas.vue';
import Leituras from '../views/Missas/Leituras.vue';


const routes = [
    {
        path: '/',
        name: 'Home',
        component: () => import('../views/Home.vue'),
    },
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/register", // Nova rota para registro
        name: "Register", 
        component: Register,
    },
    {
        path: '/forgot-password',
        name: 'ForgotPassword',
        component: () => import('@/views/ForgotPassword.vue')
      },
      {
        path: '/reset-password',
        name: 'ResetPassword',
        component: () => import('@/views/ResetPassword.vue')
      },
      {
        path: '/google-success',
        name: 'GoogleSuccess',
        component: () => import('@/views/GoogleSuccess.vue'),
      }, 
        {
        path: "/admin/usuarios", // Nova rota para registro
        name: "admin/usuarios",
        component: AdminUsuarios, 
    },  
    {
        path: "/PerfilUser", // Nova rota para registro
        name: "PerfilUser",
        component: PerfilUser,
    },
     {
        path: "/Missas", 
        name: "Missas",
        component: Missas,
    },
    {
        path: "/Leituras", 
        name: "Leituras",
        component: Leituras,
    },
    {
        path: "/dashboard", // Nova rota para registro
        name: "dashboard",
        component: Dashboard,
    },
    {
        path: "/avisos/registo", // Nova rota para registro
        name: "avisos/registo",
        component: Avisos, 
    },
    {
        path: "/avisos", // Nova rota para registro
        name: "avisos",
        component: AvisosList, 
    },
    {
        path: "/Ministerio", // Nova rota para registro
        name: "Ministerio",
        component: Ministerio, 
    },
    {
        path: "/userMinister", //
        name: "userMinister",
        component: UserMinisterList, 
    },
    {
        path: "/aniversariantes", // Nova rota para registro
        name: "aniversariantes",
        component: AniversarianteList, 
    },
    {
        path: "/Eventos", // Nova rota para registro
        name: "Eventos",
        component: Eventos, 
    },
    {
        path: "/CreateEvent", // Nova rota para registro
        name: "CreateEvent",
        component: CreateEvent, 
    },
    {
        path: "/doacoes", // Nova 
        name: "doacoes",
        component: Doacao, 
    },
    {
        path: "/FinancialHistory", // Nova rota para registro
        name: "FinancialHistory",
        component: FinancialHistory, 
    },
    {
        path: "/casamento/Register", // Nova rota para registro
        name: "casamento/Register",
        component: CasamentoRegister, 
    },
     {
        path: "/Batismo/Register", // Nova rota para registro
        name: "Batismo/Register",
        component: BatismoRegister, 
    },
    {
        path: "/Painel/Sacerdote", // Nova rota para registro
        name: "Painel/Sacerdote",
        component: PainelSacerdote, 
    },
     {
        path: "/batismos/aprovados", // Nova rota para registro
        name: "/batismos/aprovados",
        component: PedidosAprovados, 
    },
    {
        path: "/batismos/pendentes", // Nova rota para registro
        name: "/batismos/pendentes",
        component: PedidosPedentes, 
    },
    {
        path: "/batismos/em-analise", // Nova rota para registro
        name: "/batismos/em-analise",
        component: AnaliseBatptismo, 
    },
    {
        path: "/certificados", // Nova rota para registro
        name: "certificados",
        component: CertificadoBaptismo, 
    },
     {
        path: "/batismos/todos", // Nova rota para registro
        name: "batismos/todos",
        component: TodosBatizados, 
    },
     {
        path: "/painel/casamento", // Nova rota para registro
        name: "/painel/casamento",
        component: PainelCasamento, 
    },
     {
        path: "/sacerdote/oracoes", // Nova rota para registro
        name: "/sacerdote/oracoes",
        component: OracoesView, 
    },
    

    
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});


// Middleware para verificar autenticação e permissões
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));
  
    if (to.meta.requiresAuth && !token) {
      next('/');
    } else if (to.meta.requiresAdmin && user?.role !== 'admin') {
      next('/dashboard');
    } else {
      next();
    }
  });


export default router;
