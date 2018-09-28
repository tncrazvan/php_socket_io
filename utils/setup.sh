#!/bin/bash
apt-get update
rm /var/cache/apt/archives/lock
apt-get install php7.0 -y
apt-get install git -y
apt-get install mysql-server -y
mysql_secure_installation
apt-get install php7.0-mysql -y
apt-get install php7.0-odbc -y
apt-get install php7.0-gd -y
apt-get install php7.0-dom -y
apt-get install php7.0-mysql -y
apt-get install php7.0-mbstring -y
rm $1/php_socket_io -fr
git clone https://github.com/tncrazvan/php_socket_io.git $1/php_socket_io
mkdir $1/php_socket_io/utils/logs
chmod 777 $1/php_socket_io/* -R
mysql < $1/php_socket_io/utils/database/dump/localrep.sql -u root -p
cp $1/php_socket_io/utils/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart
apt-get install apache2 -y
apt-get install libapache2-mod-php7.0 -y
rm /var/www/html/glorep -fr
git clone https://github.com/glorep/glorep.git /var/www/html/glorep
mkdir /var/www/html/glorep/sites/default/files
mkdir /var/www/html/glorep/sites/default/files/collabrep
mkdir /var/www/html/glorep/sites/default/files/collabrep/cache
cp /var/www/html/glorep/sites/default/default.settings.php /var/www/html/glorep/sites/default/settings.php
chmod 777 /var/www/html/glorep/* -R
service apache2 restart
cp $1/php_socket_io/utils/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart
