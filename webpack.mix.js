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


mix.js('resources/js/admin.js', 'public/js')
    .js('resources/js/checkers.js', 'public/js')
    .js('resources/js/checker.js', 'public/js')
    .js('resources/js/checkerlk.js', 'public/js')
    .js('resources/js/statistics.js', 'public/js')
    .js('resources/js/app.js', 'public/js');
    // .sass('resources/sass/app.scss', 'public/css');

mix.setPublicPath('./')
    .js('resources/js/extension/src/js/background.js', 'resources/js/extension/dist/js')
    .js('resources/js/extension/src/js/content.js', 'resources/js/extension/dist/js')
    .js('resources/js/extension/src/js/popup.js', 'resources/js/extension/dist/js')
