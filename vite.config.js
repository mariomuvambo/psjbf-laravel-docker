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
    host: '0.0.0.0',
    port: 80,
  },
});
