import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/components.css', 'resources/css/portal.css', 'resources/css/login.css', 'resources/css/register.css', 'resources/css/admin.css', 'resources/css/doctor.css', 'resources/js/app.js', 'resources/js/admin.js', 'resources/js/pacientes.js', 'resources/js/doctor.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
