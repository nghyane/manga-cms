const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/ }));

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    build: {
        outDir: '../../public/modules/anime',
        emptyOutDir: true,
        manifest: true,
    },
    plugins: [
        laravel({
            publicDirectory: '../../public',
            buildDirectory: 'modules/anime',
            input: [
                __dirname + '/Resources/assets/css/app.css',
                __dirname + '/Resources/assets/js/app.js'
            ],
            refresh: true,
        }),
    ],
});
