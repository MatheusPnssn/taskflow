import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import inertia from '@inertiajs/vite'

export default defineConfig({
    publicDir: false,
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: true,
        cors: true,
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        inertia({
            ssr: {
                entry: 'resources/js/ssr.js',
                port: 13714,
                host: '127.0.0.1',
                cluster: true,
            },
        }),
    ],
    ssr: {
        noExternal: ['@inertiajs/vue3'],
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        cssCodeSplit: false,
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            input: 'resources/js/app.js',
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                    if (
                        id.includes('resources/views/components/') ||
                        id.includes('resources/views/templates/') ||
                        id.includes('resources/views/popups/') ||
                        id.includes('resources/views/partials/') ||
                        id.includes('resources/views/pages/')
                    ) {
                        return 'shared-ui';
                    }
                },
            },
        },
    },
})