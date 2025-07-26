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
    outDir: 'dist', // Aqui você vai configurar a pasta de saída para 'dist'
    rollupOptions: {
      output: {
        manualChunks: {
          // Exemplo de configuração de chunk manual
          vendor: ['vue', 'axios'], // Isso pode ser útil para dependências grandes
        },
      },
    },
    chunkSizeWarningLimit: 1000, // Ajuste o limite de chunk para evitar a advertência
  },
});
