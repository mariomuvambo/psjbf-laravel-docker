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
        host: '0.0.0.0',  // Força o servidor a escutar em todas as interfaces
        port: 80,          // Defina a porta como 80
        strictPort: true,  // Garante que a porta 80 seja usada
    },
});
