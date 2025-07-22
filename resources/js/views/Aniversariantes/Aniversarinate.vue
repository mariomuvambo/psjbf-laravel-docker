<template>
    <nav-dashboard />
     <div class="container mt-4">
       <h2 class="text-center mb-4">Eventos da Igreja</h2>
       <div class="row">
         <div class="col-md-6 col-lg-4 mb-4" v-for="evento in eventos" :key="evento.id">
           <div class="card">
             <img :src="evento.imagem" class="card-img-top" alt="Evento" />
             <div class="card-body">
               <h5 class="card-title">{{ evento.titulo }}</h5>
               <p class="card-text">{{ evento.descricao }}</p>
               <p class="text-muted"><strong>Data:</strong> {{ evento.data }}</p>
             </div>
           </div>
         </div>
       </div>
     </div>
   </template>
   
   <script>
 import NavDashboard from '../../components/NavDashboard.vue'; 
   export default {
   components: { NavDashboard },
     data() {
       return {
         eventos: [
           {
             id: 1,
             titulo: "Missa Dominical",
             data: "2025-03-17",
             descricao: "Celebração da missa dominical às 10h na Igreja Matriz.",
             imagem: "https://via.placeholder.com/300x200"
           },
           {
             id: 2,
             titulo: "Retiro Espiritual",
             data: "2025-03-23",
             descricao: "Um dia de retiro para aprofundamento da fé e meditação.",
             imagem: "https://via.placeholder.com/300x200"
           },
           {
             id: 3,
             titulo: "Procissão de Páscoa",
             data: "2025-04-01",
             descricao: "Evento especial para celebrar a ressurreição de Cristo.",
             imagem: "https://via.placeholder.com/300x200"
           }
         ]
       };
     }
   };
   </script>
   
   <style scoped>
   .card {
     border-radius: 10px;
     overflow: hidden;
     box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
   }
   .card img {
     height: 200px;
     object-fit: cover;
   }
   </style>
   