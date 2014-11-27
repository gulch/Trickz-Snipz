#!/bin/sh

# PERCONA 5.6
#MYSQL_REPO = "deb http://repo.percona.com/apt wheezy main"
#MYSQL_REPO_KEY = "apt-key adv --keyserver keys.gnupg.net --recv-keys 1C4CBDCDCD2EFD2A"
#MYSQL_PACKAGE = "percona-server-server-5.6 percona-server-client-5.6"

#MARIADB 10
MYSQL_REPO = "deb http://ftp.nluug.nl/db/mariadb/repo/10.1/debian wheezy main"
MYSQL_REPO_KEY = "apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db"
MYSQL_PACKAGE = "mariadb-server"

echo "Debian7 web dev components installation. Author: gulch <contact@gulch.in.ua>"
echo "Press any key to continue.."
read -s -n 1 any_key

echo "Adding TESTING Debian Repo"
echo "deb http://debian.volia.net/debian/ testing main contrib" > /etc/apt/sources.list.d/testing.list

echo "Adding mysql* repo"
echo "$MYSQL_REPO" > /etc/apt/sources.list.d/mysql.list

if [ $MYSQL_REPO_KEY -ne "" ]; then
    $MYSQL_REPO_KEY
fi

echo "Apt Updating:"
apt-get update

echo "Install all software:"
apt-get install "$MYSQL_PACKAGE" php5-fpm php5-mcrypt php5-cli php5-gd php5-imagick php5-mysqlnd nginx-extras nginx-common redis-server zip unzip -y

echo "Create WWW, LOG folders and set rights:"
mkdir /var/www
mkdir /var/log/php5-fpm
chmod -R 777 /var/log
chmod -R a-rwx,u+rwX,g+rX /var/www && chown www-data:www-data -R /var/www

echo "Set Timezone:"
dpkg-reconfigure tzdata

echo "Reconfigure EXIM4. Answer ==internet site; mail is sent and received directly using SMTP==:"
dpkg-reconfigure exim4-config

echo "Congratulation!"
exit 0