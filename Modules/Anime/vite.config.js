const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/ }));

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import purge from '@erbelion/vite-plugin-laravel-purgecss'


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
            refresh: [
                __dirname + '/Resources/views/**',
            ],

            // rebuild on changes
            watchBuild: [
                __dirname + '/Resources/views/**',
            ],
        }),
        purge({
            paths: [
                __dirname + '/Resources/views/**',
                __dirname + '/Resources/assets/js/**',
            ],
            // .tooltip, .tooltip-inner, .tooltip-arrow
            safelist: {
                standard: [/tooltip/, /popover/, /modal/, /fade/, /show/, /bs-tooltip/, /bs-popover/, /bs-modal/, /bs-fade/, /bs-show/],
                deep: [/tooltip/, /popover/, /modal/, /fade/, /show/, /bs-tooltip/, /bs-popover/, /bs-modal/, /bs-fade/, /bs-show/],
            }
        })
    ],
});
