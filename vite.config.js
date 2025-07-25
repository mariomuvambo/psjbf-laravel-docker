import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    // Plugin para integração com Laravel, processando CSS e JS
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,  // Isso recarrega automaticamente o navegador durante o desenvolvimento
    }),
    vue(),  // Plugin do Vue para compilar os arquivos Vue
  ],
  build: {
    // Define o diretório de saída para os arquivos compilados
    outDir: 'public',  // Certifique-se de que os arquivos de build sejam colocados na pasta public
    manifest: true,  // Gera o manifesto de build para compatibilidade com o Laravel
    rollupOptions: {
      output: {
        // Garante que o Vue seja compilado corretamente
        chunkFileNames: 'js/[name].[hash].js',
        entryFileNames: 'js/[name].js',
      },
    },
  },
});
