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
        outDir: 'public/build',  // Laravel espera os arquivos aqui
    },
    server: {
        host: '0.0.0.0',
        port: 80,
    },
    preview: {
        host: '0.0.0.0',
        port: 80,
        open: true,  // abre o navegador automaticamente
        allowedHosts: ['psjbf.onrender.com'], // Configuração para permitir o acesso do Render
    },
    base: '/', // Isso ajuda a garantir que o Vue funcione corretamente nas rotas
});
