FROM php:7.4-apache
RUN apt-get update && apt-get install -y \
    redis-tools \
    && pecl install redis \
    && docker-php-ext-enable redis
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY src/ /var/www/html
EXPOSE 6379
