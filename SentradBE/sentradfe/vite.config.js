import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: '../public/build',  // Sesuaikan path sesuai dengan struktur proyek Laravel Anda
    manifest: true,
    rollupOptions: {
      input: 'src/main.js',  // Sesuaikan path input sesuai dengan struktur proyek Vue Anda
    },
  },
});

