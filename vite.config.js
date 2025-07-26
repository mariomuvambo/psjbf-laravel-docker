import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build', // Laravel espera que os arquivos de build fiquem aqui
    },
   server: {
    host: '0.0.0.0',  // Pode ser 'localhost' ou '127.0.0.1' em desenvolvimento
    port: 80, 
    strictPort: true,  
    open: true,  // Para abrir a página automaticamente
},
preview: {
    allowedHosts: ['psjbf.onrender.com'],  // Domínio correto para produção
},

});
