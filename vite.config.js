import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/images/foto_login_flamenco.svg',
                'resources/images/foto_login_flamenco_con_fondo.svg'
            ],
            refresh: true,
        }),
    ],
});
