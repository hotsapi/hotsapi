let mix = require('laravel-mix');

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
    .js('resources/assets/js/app.js', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui.js', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui.js.map', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui-bundle.js', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui-bundle.js.map', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui-standalone-preset.js', 'public/js')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui-standalone-preset.js.map', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui.css', 'public/css')
    .copy('vendor/swagger-api/swagger-ui/dist/swagger-ui.css.map', 'public/css')
    .version();