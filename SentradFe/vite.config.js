import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  server: {
    proxy: {
      '/api': 'http://localhost:8000/SentradBe/api'
    }
  },
  plugins: [vue()],
  resolve: {
    alias: {
      'vue-leaflet': path.resolve(__dirname, 'node_modules/vue-leaflet'),
    },
  },
  build: {
    outDir: '../SentradBe/public/build', 
    emptyOutDir: true, 
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'SentradFe/src/main.js'),
    },
  },
});
