const { mix } = require('laravel-mix');

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
mix.js('resources/assets/js/libs.js', 'public/js')
    .js('resources/assets/js/app.js', 'public/js')
    .js('resources/assets/js/admin.js', 'public/js')
    .sass('resources/assets/sass/core.scss', 'public/css')
    .styles(['resources/assets/css/admin.css'], 'public/css/admin.css')
    .styles([
        'resources/assets/css/animate.css',
        'resources/assets/css/theme.css'
    ], 'public/css/app.css');