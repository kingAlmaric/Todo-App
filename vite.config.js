import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: [
                // Ignore les fichiers blade.php
                /\.blade\.php$/,
            ]
        }
    },
    // Assure-toi que Vite ignore les fichiers PHP
    assetsInclude: ['**/*.php']
});