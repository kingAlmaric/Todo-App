import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Optimisation de la construction
        minify: 'terser',
        cssMinify: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'axios'],
                    auth: [
                        'resources/views/auth/login.blade.php',
                        'resources/views/auth/register.blade.php'
                    ]
                }
            }
        },
        // Optimisations supplémentaires
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1000
    },
    server: {
        // Configuration du serveur de développement
        hmr: {
            overlay: false
        },
        watch: {
            usePolling: false
        }
    },
    optimizeDeps: {
        // Optimisation des dépendances
        include: ['vue', 'axios'],
        exclude: []
    },
    // Cache optimisé pour les pages d'authentification
    cacheDir: 'node_modules/.vite/auth-cache'
});
