#!/bin/bash
apt-get install -y language-pack-en-base
#locale-gen "en_US.UTF-8"
#dpkg-reconfigure locales
LC_ALL=en.US.UTF-8
add-apt-repository ppa:ondrej/php-zts
apt-get update
apt-get install php7.0-zts php7.0-zts-dev
y

apt-get install git
y
cd ~
git clone https://github.com/krakjoe/pthreads.git
cd pthreads
phpize
./configure
make -j8
make install

mkdir -p /etc/php/7.0-zts/conf.d/
echo "extension=pthreads.so" > /etc/php/7.0-zts/conf.d/pthreads.ini
cd /etc/php/7.0-zts/cli/conf.d
sudo ln -s ../../conf.d/pthreads.ini

apt-get install mysql-server
y
mysql_secure_installation
n
n
y
y
y
y
apt-get install php7.0-zts-mysql
y
apt-get install php7.0-zts-odbc
y

cd ~
git clone https://github.com/tncrazvan/php_socket_io.git
sudo chmod 777 php_socket_io -R
cd ~/php_socket_io/utils
mysql < database/dump/localrep.sql -u root -p

cp mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
service mysql restart
apt-get install apache2
y
apt-get install libapache2-mod-php7.0
y
cd /var/www/html
rm ./* -fr
git clone https://github.com/tncrazvan/glorep.git /var/www/html

cd sites/default/
mkdir files
cp default.settings.php settings.php
chmod 777 ./* -R
cd /var/www/html
apt-get install php7.0-gd
y
apt-get install php7.0-mysql
y
apt-get install php7.0-mbstring
y
cd ~/php_socket_io
php init.php
service apache2 restart