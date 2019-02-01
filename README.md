# insalan.fr

[![Build Status](https://travis-ci.org/InsaLan/insalan.fr.svg?branch=master)](https://travis-ci.org/InsaLan/insalan.fr)
[![Maintainability](https://api.codeclimate.com/v1/badges/68707ca6cd1a2b332dc4/maintainability)](https://codeclimate.com/github/InsaLan/insalan.fr/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/68707ca6cd1a2b332dc4/test_coverage)](https://codeclimate.com/github/InsaLan/insalan.fr/test_coverage)

Website to handle esport tournament


## Installation

The project need the following package and php-extension :

Package | Comment
------- | -------
git |
php | php7.0
composer |
mariadb-server | if use with local database
zip | not necessary (only for unzip during install)

PHP extension | Comment
------------- | -------
php-curl |
php-intl | date translation
php-mbstring | doctrine:create:schema:create
php-mysql |
php-xml |


### Linux


#### Base packages

Update your local packages

```bash
sudo apt-get update && apt-get upgrade
```

Install git and clone the repository

```bash
sudo apt-get install git
git clone https://github.com/InsaLan/insalan.fr
```

It is recommended to get composer.phar in your PATH : https://getcomposer.org/doc/00-intro.md

Install the other packages

```bash
sudo apt-get install php php-curl php-intl php-mbstring php-mysql php-xml mariadb-server zip
```

Proceed with the install and accept default settings

```bash
cd insalan.fr
composer.phar install
```

At this point, you should be able to run the web server but any pages you try to access will return an error since the database is not setup.


#### mariadb

Log in mariadb as root

```bash
sudo mysql -u root -p
```

Create database, user and grant access

```mariadb
CREATE DATABASE insalan;
CREATE USER insalan@'localhost';
GRANT ALL PRIVILEGES ON insalan.* to insalan@'localhost';
```


#### Fill database and run

Load fixtures (php-mbstring needed)

```bash
php app/console doctrine:schema:create #have to be killed
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
php app/console server:run 0.0.0.0 #available for everyone
```
(you can also use the php builtin development web server : `cd web && php -S localhost:9001`)


### Windows

TODO
http://www.wampserver.com/ should be able to provide everything you need.


## Deploy on shared web hosting services

You can use the deploy-ftp script to deploy on a mutualised website.
You must also have ncftp on your client (yum install ncftp/apt-get install ncftp)

1. Configure your deployment

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

1. Deploy

Deploy takes approximately 5 minutes.

```bash
cd deploy
./deploy-ftp.sh
```

1. Remove app/cache/prod content

Use filezilla to clear cache, ie remove app/cache/prod folder content

Backup DB before deploy, update it locally and send it after deploy if you changed the schema.


## Contributing

If you wish to contribute to the insalan.fr project, refer to [this](https://github.com/insalan/insalan.fr/blob/master/CONTRIBUTING.md) file.
