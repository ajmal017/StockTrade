Prerequisites:

sudo apt install php7.2-mbstring php5.6-mbstring php7.0-mbstring php7.1-mbstring php7.3-mbstring
sudo apt install php7.2-dom php5.6-dom php7.0-dom php7.1-dom php7.3-dom
sudo apt install php7.2-bcmath php5.6-bcmath php7.0-bcmath php7.1-bcmath php7.3-bcmath
sudo apt install php5.6-curl php7.0-curl php7.1-curl php7.2-curl php7.3-curl

Compiling vue-2:
# install npm
npm install gulp
npm install laravel-elixir
npm install laravel-elixir-vue-2
npm install webpack
npm install laravel-elixir-webpack-official --save-dev
npm install
npm run dev

# Upgrade from gulp to laravel-mix.
npm install laravel-mix --save-dev

# Try to build with javascript source code, not uglify or minified javascript?
# webpack-merge required by this configuration: https://survivejs.com/webpack/building/source-maps/
npm install webpack-merge;
npm install ajv;
npm install webpack-merge;

# I added a new file, webpack.config.js and webpack.parts.js
npm run dev
