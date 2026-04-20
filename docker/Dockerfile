FROM ghcr.io/geekcodev/php:8.4-franken

ARG INSTALL_XDEBUG=false
ARG UID=1000
ARG GID=1000

RUN if [ "${INSTALL_XDEBUG}" = "true" ]; then \
      pecl install xdebug && docker-php-ext-enable xdebug; \
    fi

RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

COPY /docker/config/usr/local/etc/php/conf.d/40-custom.ini /usr/local/etc/php/conf.d/40-custom.ini
COPY /docker/config/usr/local/etc/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY /docker/scripts/docker-horizon-entrypoint.sh /usr/local/bin/docker-horizon-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-horizon-entrypoint.sh

RUN mkdir -p /data/caddy /config/caddy && chown -R www-data:www-data /data /config

RUN echo "umask 002" >> /etc/profile
ENV UMASK=002

WORKDIR /var/www/html

USER www-data:www-data

EXPOSE 8000
