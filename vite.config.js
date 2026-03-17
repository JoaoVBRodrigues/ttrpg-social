import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const port = Number(env.VITE_DEV_SERVER_PORT ?? 5173);

    return {
        server: {
            host: '0.0.0.0',
            port,
            strictPort: true,
            watch: {
                usePolling: true,
            },
            hmr: {
                host: env.VITE_HMR_HOST ?? 'localhost',
                port,
            },
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
    };
});
