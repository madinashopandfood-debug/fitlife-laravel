import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// NOTE: The admin panel currently loads Tailwind CSS via CDN directly in
// resources/views/admin/layouts/app.blade.php, so this Vite build is NOT
// required for the app to run. It's included only as a starting point in
// case you later want to move to a compiled asset pipeline
// (npm install && npm run build, then swap the CDN <script> tag for
// @vite(['resources/css/app.css', 'resources/js/app.js']) in the layout).
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
