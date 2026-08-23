import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
            //css
            'resources/css/app.css',
            "node_modules/glightbox/dist/css/glightbox.min.css",
            'node_modules/quill/dist/quill.bubble.css',
            'node_modules/quill/dist/quill.core.css',
            'node_modules/quill/dist/quill.snow.css',
            'node_modules/jsvectormap/dist/css/jsvectormap.min.css',
            
            //js
            'resources/js/app.js',
            'resources/js/pages/app-calendar.js',
            'resources/js/pages/charts-apex.js',
            'resources/js/pages/dashboard.js',
            'resources/js/pages/form-editor.js',
            'resources/js/pages/form-inputmask.js',
            'resources/js/pages/gallery.js',
            'resources/js/pages/maps-vector.js'
        ],
            refresh: true,
        }),
        tailwindcss()
    ],
});