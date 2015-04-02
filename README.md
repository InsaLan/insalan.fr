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

Create database & load fixtures (php-mbstring needed)

```bash
php app/console doctrine:database:create
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load
```

Install assets

```bash
php app/console assets:install #dev
php app/console assetic:dump #prod
```

Clear cache

```bash
php app/console cache:clear #dev
php app/console cache:clear --env=prod
```

Launch development server

```bash
php app/console server:run #localhost only
php app/console server:run 0.0.0.0:9001 #available for everyone 
```
(you can also use the php builtin development web server : `cd web && php -S localhost:9001`)

Deploy on shared web hosting services
-------------------------------------

You can use the deploy-ftp script to deploy on a mutualised website.
You must also have ncftp on your client (yum install ncftp/apt-get install ncftp)

1) Configure your deployment

```bash
cd deploy/conf

#Configure ftp
cp ftp.cfg.dist ftp.cfg
vim ftp.cfg

#Configure .htaccess
cp .htaccess.dist .htaccess
vim .htaccess

#Configure parameters.yml
cp parameters.yml.dist parameters.yml
vim parameters.yml
```

2) Deploy

Deploy takes approximately 5 minutes.

```bash
cd deploy
./deploy-ftp.sh
```

3) Remove app/cache/prod content
Use filezilla to clear cache, ie remove app/cache/prod folder content

Backup DB before deploy, update it locally and send it after deploy if you changed the schema.

4) Contributing file

If you wish to contribute to the insalan.fr project, refer to [this](https://github.com/insalan/insalan.fr/CONTRIBUTING.md) file.
