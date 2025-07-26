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
    outDir: 'public/build', // A pasta de saída será 'public/build', como no Laravel
    rollupOptions: {
      output: {
        manualChunks: {
          // Aqui você pode configurar manualmente a divisão de chunks se necessário
          // Por exemplo, se você quiser dividir pacotes de dependências
          vendor: ['vue', 'axios'], // Exemplo: criar um chunk separado para dependências
        },
      },
    },
    chunkSizeWarningLimit: 1000, // Ajusta o limite de tamanho de chunk para evitar a advertência
  },
});
