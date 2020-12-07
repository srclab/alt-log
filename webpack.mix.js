const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .options({
        terser: {
            extractComments: false,
        }
    })
    .setResourceRoot('/vendor/alt-log/')
    .setPublicPath('public')
    .sass('resources/assets/sass/app.sass', 'css/app.css')
    .js('resources/assets/js/app.js', 'js/app.js')
    .copy('public', '../../../public/vendor/alt-log');
