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
.sass('resources/sass/app.scss', 'public/css',{
    precision: 5
})
.sass('resources/sass/stocktrade-main.scss', 'public/css')
.copyDirectory('resources/img','public/img')
.copyDirectory('resources/plugins','public/plugins')
.sourceMaps()
.js('resources/js/stocktrade.js', 'public/api/v1/js')
.js('resources/js/docs.js', 'public/api/v1/js')
.js('resources/js/app.js', 'public/js')
.extract([
    'vue',
    'axios',
    'bootstrap',
    'jquery'
]);

if (mix.inProduction()) {
    mix.version();
}
