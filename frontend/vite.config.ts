import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import { VitePWA } from 'vite-plugin-pwa'

// https://vite.dev/config/
export default defineConfig({ server: { proxy: {
  '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true },
  '/sanctum': { target: 'http://127.0.0.1:8000', changeOrigin: true },
} }, plugins: [react(), tailwindcss(), VitePWA({
  registerType: 'autoUpdate', includeAssets: ['branding/las-pinas-seal.png', 'icons/icon-192.png', 'icons/icon-512.png'],
  manifest: { name: 'Better Las Piñas', short_name: 'Better LP', description: 'Community reporting and progress tracking for Las Piñas.', theme_color: '#176B45', background_color: '#F4F8F3', display: 'standalone', orientation: 'portrait-primary', start_url: '/', scope: '/', lang: 'en-PH', icons: [{ src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' }, { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' }, { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' }] },
  workbox: { navigateFallback: '/index.html', globPatterns: ['**/*.{js,css,html,svg,png,woff2}'], runtimeCaching: [] },
})] })
