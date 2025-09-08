import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Optimize for production
        minify: 'terser',
        sourcemap: false,
        rollupOptions: {
            output: {
                // Manual chunks for better caching
                manualChunks: {
                    vendor: ['bootstrap', 'alpinejs'],
                    tinymce: ['tinymce']
                },
                // Consistent naming for service worker
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
                        return `assets/images/[name]-[hash][extname]`;
                    }
                    if (/woff2?|eot|ttf|otf/i.test(ext)) {
                        return `assets/fonts/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                }
            }
        },
        // Chunk size warning limit
        chunkSizeWarningLimit: 1000
    },
    server: {
        // Development server settings
        hmr: {
            host: 'localhost',
        },
    }
});
