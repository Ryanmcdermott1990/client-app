// When upgrading from Webpack 4 -> 5 you will need to add the NodePolyFillPlugin 
// This is due to the fact that Webpack no longer natively supports polyfills so it needs to be configured manually
// You will also need to set legacyNodePolyFills to false to avoid any conflict with the legacy version
const mix = require('laravel-mix');
require('laravel-mix-svelte');
const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');

mix.options({
    legacyNodePolyfills: false
    });    

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .svelte({
        dev: !mix.inProduction()
    })
    // In the config, create a new instance of the NodePolyfillPlugin 
    .webpackConfig({

        plugins: [
            new NodePolyfillPlugin(),
        ],

        // Resolve this promise with browserify-fs
        resolve: {
            fallback: {
                fs: require.resolve('browserify-fs'),
            }
        },

        output: { chunkFilename: 'js/[name]. js? id = [chunkhash]' },
    })
    
    .version();


