const mix = require('laravel-mix');

// Compilar o JS do Vue.js
mix.js('resources/js/app.js', 'public/js')
   .vue()  // Adiciona o suporte ao Vue.js
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
   ]);
