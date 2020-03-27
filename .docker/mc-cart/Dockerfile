FROM debian 

RUN apt-get update
RUN apt-get install -y --no-install-recommends debconf apt-transport-https apt-utils \
    build-essential locales acl mailutils libpq-dev libicu-dev libxml2-dev zlib1g-dev

RUN apt install -y php7.3 php7.3-cli php7.3-common
RUN apt-cache search php7.3
RUN apt-cache search --names-only ^php 
RUN apt install -y php-pear php7.3-curl php7.3-dev php7.3-gd php7.3-mbstring \
    php7.3-zip php7.3-pgsql php7.3-xml php7.3-fpm php7.3-recode php7.3-tidy \
    php7.3-xmlrpc php7.3-intl

RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf
#RUN mkdir /var/www/public
### Config
COPY vhost.conf /etc/apache2/sites-available
COPY php.ini /etc/php/7.3/php.ini
COPY php-fpm-pool.conf /etc/php/7.3/pool.d/www.conf
RUN a2dissite 000-default.conf && \
    service apache2 stop && \
    a2ensite vhost.conf && \
    service apache2 restart

RUN apt-get -y install composer

RUN service php7.3-fpm restart

EXPOSE 80

CMD apachectl -D FOREGROUND