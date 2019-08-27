const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
|--------------------------------------------------------------------------
| Elixir Asset Management
|--------------------------------------------------------------------------
|
| Elixir provides a clean, fluent API for defining some basic Gulp tasks
| for your Laravel application. By default, we are compiling the Sass
| file for your application as well as publishing vendor resources.
|
*/

module.exports = {
  mode: "development", // "production" | "development" | "none"
  // Chosen mode tells webpack to use its built-in optimizations accordingly.
  //entry: "./app/entry", // string | object | array
  // defaults to ./src
  devtool: "source-map", // enum
  // enhance debugging by adding meta info for the browser devtools
  // source-map most detailed at the expense of build speed.
  target: "web", // enum
};

elixir((mix) => {
    mix.sass('app.scss')
    .sass('stocktrade-main.scss')
    .webpack('app.js')
    .webpack( 'stocktrade.js' ,'./public/api/v1/js')
    .webpack( 'docs.js' ,'./public/api/v1/js');

});

