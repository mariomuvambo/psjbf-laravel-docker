import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

process.env.VITE_APP_URL = process.env.ASSET_URL || process.env.APP_URL;

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
  },
});
