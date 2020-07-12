FROM debian:9

RUN (apt-get update && apt-get install mariadb-server git zip apt-transport-https lsb-release ca-certificates wget -y -q && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list && apt-get update && apt-get install php7.2-xml  php7.2 php7.2-curl php7.2-intl php7.2-mbstring php7.2-mysql php7.2-gd -y -q && rm -rf /var/lib/apt/lists/* )
WORKDIR /var/www/insalan

ADD . .

COPY app/config/parameters.yml.dist app/config/parameters.yml
RUN sed -i "s/database_password: ~/database_password: password/g" app/config/parameters.yml
ADD https://getcomposer.org/composer-stable.phar composer.phar
ADD composer.json .
ADD composer.lock .
ADD app/config app/config

RUN php composer.phar install

RUN service mysql start && mysql -e "CREATE USER 'insalan'@'localhost' IDENTIFIED BY 'password'; GRANT ALL PRIVILEGES ON *.* TO 'insalan'@'localhost'; FLUSH PRIVILEGES;" && php bin/console doctrine:database:create && ./load_fixtures.sh && php bin/console assets:install && php bin/console assetic:dump

EXPOSE 8000

ENTRYPOINT service mysql start && php bin/console server:run 0.0.0.0

