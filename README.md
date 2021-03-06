# insalan.fr

[![Build Status](https://travis-ci.org/InsaLan/insalan.fr.svg?branch=master)](https://travis-ci.org/InsaLan/insalan.fr)
[![Maintainability](https://api.codeclimate.com/v1/badges/68707ca6cd1a2b332dc4/maintainability)](https://codeclimate.com/github/InsaLan/insalan.fr/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/68707ca6cd1a2b332dc4/test_coverage)](https://codeclimate.com/github/InsaLan/insalan.fr/test_coverage)

Website to handle esport tournament


## Installation

The project need the following packages and php-extensions :

Package | Comment
------- | -------
git |
php | php7.2
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

It is recommended to get `composer.phar` in your PATH : https://getcomposer.org/doc/00-intro.md

Install the remaining packages

```bash
sudo apt-get install php7.2 php7.2-curl php7.2-intl php7.2-mbstring php7.2-mysql php7.2-xml mariadb-server zip
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
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load
```

Install assets

```bash
php bin/console assets:install #dev
php bin/console assetic:dump #prod
```

Clear cache

```bash
php bin/console cache:clear #dev
php bin/console cache:clear --env=prod
```
If you have troubles with memory size allowed, do this
```bash
php -d memory_limit=-1 bin/console cache:clear #dev
```

Launch development server

```bash
php bin/console server:run #localhost only
php bin/console server:run 0.0.0.0 #available for everyone on port 8000
```
You can also use the php builtin development web server :
```bash
cd web
php -S localhost:8000 -t ../
```
Browse to http://localhost:8000/web/app_dev.php

### Windows

TODO
http://www.wampserver.com/ should be able to provide everything you need.


## Contributing

If you wish to contribute to the insalan.fr project, refer to [this](https://github.com/insalan/insalan.fr/blob/master/CONTRIBUTING.md) file.

## Database

List databases

```bash
php bin/console sonata:admin:list
```

Show database schema and links with other databases

```bas
php bin/console sonata:admin:explain sonata.admin.tournament.group
```
