#!/bin/bash
color_bg='\033[45m'     # Purple
color_reset='\033[0m'       # Text Reset

mkdir tmp
echo -e "\n$color_bg\n\tReading configuration file ftp.cfg\n$color_reset"
source conf/ftp.cfg

echo -e "\n$color_bg\n\tCleaning dev environment\n$color_reset"
rm -r ../app/cache/dev/*
rm -r ../app/cache/prod/*
rm -r ../web/bundles/*

echo -e "\n$color_bg\n\tGeneration prod cache\n$color_reset"
php ../app/console cache:clear --env=prod

echo -e "\n$color_bg\n\tInstalling assets\n$color_reset"
php ../app/console assets:install ../web 

echo -e "\n$color_bg\n\tGenerating assetic prod\n$color_reset"
php ../app/console assetic:dump --env=prod

echo -e "\n$color_bg\n\tCopying files\n$color_reset"
cp -r ../src ./tmp
cp -r ../web ./tmp
cp -r ../app ./tmp

echo -e "\n$color_bg\n\tAdding specific connfiguration\n$color_reset"
cp ./conf/parameters.yml ./tmp/app/config
cp ./conf/.htaccess ./tmp/web

echo -e "\n$color_bg\n\tRemoving dev files\n$color_reset"
rm ./tmp/web/app_dev.php
rm ./tmp/web/config.php
rm ./tmp/app/logs/*
rm -r ./tmp/app/cache/*

echo -e "\n$color_bg\n\tSending src to $server_host$server_path/src as $server_user\n$color_reset"
ncftpput -R -v -u "$server_user" -p "$server_password" -P $server_port $server_host $server_path/src ./tmp/src/*

echo -e "\n$color_bg\n\tSending web to $server_host$server_path/web as $server_user\n$color_reset"
ncftpput -R -v -u "$server_user" -p "$server_password" -P $server_port $server_host $server_path/web ./tmp/web/*

echo -e "\n$color_bg\n\tSending app to $server_host$server_path/app as $server_user\n$color_reset"
ncftpput -R -v -u "$server_user" -p "$server_password" -P $server_port $server_host $server_path/app ./tmp/app/*

#echo "rm -r $server_path/app/cache/prod/*"

rm -r tmp
