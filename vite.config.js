import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    build: {
        outDir: 'public/build',  // Laravel espera os arquivos aqui
    },
    server: {
        host: '0.0.0.0',  // Permite conexões externas, importante para Render
        port: 80,         // Porta de comunicação no Render
    },
    preview: {
        allowedHosts: ['psjbf.onrender.com'],  // Adiciona o domínio aqui para permitir acesso
    },
});
