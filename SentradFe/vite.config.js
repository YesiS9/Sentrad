import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  server: {
    proxy: {
      '/api': 'sentrad-production-2d25.up.railway.app'
    }
  },
  plugins: [vue()],
  resolve: {
    alias: {
      'vue-leaflet': path.resolve(__dirname, 'node_modules/vue-leaflet'),
    },
  },
  build: {
      outDir: 'dist', 
      emptyOutDir: true, 
      manifest: true,
      rollupOptions: {
        input: path.resolve(__dirname, 'index.html'), // Gunakan index.html
      },
    },
  
});
