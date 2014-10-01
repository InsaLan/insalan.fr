insalan.fr
==========

Website to handle esport tournament

Installation
------------

Download vendors
Considering you have composer.phar installed and in your PATH :

```bash
composer.phar install
```

If not set with composer.phar, configure symfony2 : 

```bash
cp app/config/parameters.yml.dist app/config/parameters.yml
vim app/config/parameters.yml
```

Create database & load fixtures

```bash
php app/console doctrine:database:create
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load
```

Install assets

```bash
php app/console assets:install //dev
php app/console assetic:dump //prod
```

Clear cache

```bash
php app/console cache:clear //dev
php app/console cache:clear --env=prod
```

Launch development server

```bash
php app/console server:run //localhost only
php app/console server:run 0.0.0.0:9001 //available for everyone 
```
(you can also use the php builtin development web server : `cd web && php -S localhost:9001`)
