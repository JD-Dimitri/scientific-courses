import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// En développement, Vite renvoie tous les appels /api vers Symfony.
// Front et API partagent ainsi la même origine : le cookie de session fonctionne sans configuration CORS.
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
    },
  },
});
