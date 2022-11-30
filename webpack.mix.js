const mix = require ('laravel-mix');
require ('laravel-mix-svelte');

mix.js ('resources/js/app.js', 'public/js')
    .sass ('resources/sass/app.scss', 'public/css')
    .svelte ({
        dev:! mix.inProduction ()
    })
    .webpackConfig ({
        output: {chunkFilename: 'js/[name]. js? id = [chunkhash]'},
    })
    .version ();


    