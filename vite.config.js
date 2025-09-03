import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: true,          // ⬅️ This makes Vite listen on all interfaces (0.0.0.0)
        port: 5173,
        hmr: {
            protocol: 'ws',     // Use WebSocket, works well in most local/remote setups
            host: undefined,    // ⬅️ Let Vite auto-detect the request IP
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            external: [
                'datatables.net-dt',
            ]
        }
    }
});
