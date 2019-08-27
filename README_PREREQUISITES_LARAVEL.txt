Ubuntu:

Install mbstring dependency for all available php versions:


sudo apt install php7.2-mbstring php5.6-mbstring php7.0-mbstring php7.1-mbstring php7.3-mbstring
sudo apt install php7.2-dom php5.6-dom php7.0-dom php7.1-dom php7.3-dom

sudo apt install php7.2-bcmath php5.6-bcmath php7.0-bcmath php7.1-bcmath php7.3-bcmath

sudo apt install php5.6-curl php7.0-curl php7.1-curl php7.2-curl php7.3-curl


Configuration:

.bash_profile

!/bin/bash
PATH=$PATH:$HOME/.composer/vendor/bin

export PATH



composer global require "laravel/installer"


cd /home/j/laravel;

laravel new new-project

cd new-project

composer install #s'assurer que les dépendances sont installé correctement.

cp .env.example .env -pr

php artisan key:generate

php artisan serve
