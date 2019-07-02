#!/bin/bash

# Update packages
yum -y update

# Install basic packages
yum -y install \
nano \
bzip2 \
git \
nginx \
php7-pear \
php71-bcmath \
php71-cli \
php71-common \
php71-fpm \
php71-gd \
php71-imap \
php71-json \
php71-mbstring \
php71-mcrypt \
php71-mysqlnd \
php71-opcache \
php71-pdo \
php71-pecl-igbinary \
php71-pecl-imagick \
php71-pecl-redis \
php71-process \
php71-soap \
php71-xml \
php71-xmlrpc \
php71-xdebug \
unzip \
zip
# gcc \
# gcj \
# gcc-java\
# libgcj \
# wget \
# libgcj-devel\
# gcc-c++

# # Install MongoDB PHP extension
# yum -y install gcc openssl-devel php71-devel
# pecl7 install mongodb
# cat > /etc/php.d/50-mongodb.ini <<_EOF_
# ; Enable MongoDB extension module
# extension = mongodb.so
# _EOF_
# yum -y history undo last
# pecl7 clear-cache
# rm -rfv /tmp/pear

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
ln -s /usr/local/bin/composer /usr/bin/composer
rm -rfv ~/.composer

# # Install Supervisor
# yum -y install python27-pip
# pip install supervisor
# mkdir /var/log/supervisor
# cat > /etc/logrotate.d/supervisor <<_EOF_
# /var/log/supervisor/*.log {
#     missingok
#     weekly
#     notifempty
#     nocompress
# }
# _EOF_
# mkdir /etc/supervisord.d
# echo_supervisord_conf > /etc/supervisord.conf
# sed -i \
# -e 's#logfile=/tmp/supervisord\.log#logfile=/var/log/supervisor/supervisord.log#' \
# -e 's#pidfile=/tmp/supervisord.pid#pidfile=/var/run/supervisord.pid#' \
# -e 's#/tmp/supervisor\.sock#/var/tmp/supervisor.sock#' \
# -e 's#^;\[include\]#[include]#' \
# -e 's#^;files = relative/directory/\*\.ini#files=/etc/supervisord.d/*.ini#' \
# /etc/supervisord.conf
# curl -O https://raw.githubusercontent.com/Supervisor/initscripts/master/redhat-init-mingalevme
# sed -i -e 's#^PREFIX=/usr#PREFIX=/usr/local#' redhat-init-mingalevme
# install -v -C -D -m 0755 redhat-init-mingalevme /etc/rc.d/init.d/supervisord
# chkconfig --add supervisord
# rm -vf redhat-init-mingalevme
# rm -rfv ~/.cache

# # Install Node.js
# curl -sL https://rpm.nodesource.com/setup_8.x | bash -
# yum -y install nodejs

# # Install Amazon Cloudwatch Logs agent
# yum -y install awslogs

# # Install New Relic agent
# yum -y install http://yum.newrelic.com/pub/newrelic/el5/x86_64/newrelic-repo-5-3.noarch.rpm
# yum -y install newrelic-php5
# rm -fv /tmp/nrinstall-*.tar

yum install -y wget
yum localinstall -y https://www.linuxglobal.com/static/blog/pdftk-2.02-1.el7.x86_64.rpm


# wget https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/pdftk-2.02-1.el6.x86_64.rpm
# yum install libgcj
# rpm -i pdftk-2.02-1.*.rpm
