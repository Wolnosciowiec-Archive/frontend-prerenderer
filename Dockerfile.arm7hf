FROM resin/armv7hf-debian:buster

ENV APP_VERSION=1

# application configuration
ENV FP_SKIPPED_HEADERS "content-length,host,connection,accept-encoding,x-frontend-prerenderer,user-agent"
ENV FP_ALLOWED_DOMAINS "facebook.com,web.facebook.com,www.facebook.com,www.web.facebook.com,application"
ENV FP_DELAY 0
ENV FP_TIMEOUT 0
ENV FP_WITH_IMAGES 0
ENV FP_DEBUG 0
ENV FP_RENDERER "chromium"
ENV FP_CHROMIUM_BINARY "chromium-browser"
ENV FP_WINDOW_SIZE "1920x1080"
ENV FP_OPEN_PROCESS_LIMIT 3
ENV FP_WAIT_FOR_PROCESS_TIME 4

RUN [ "cross-build-start" ]

RUN echo "deb http://deb.debian.org/debian stretch main contrib non-free" >> /etc/apt/sources.list
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install --assume-yes cron supervisor rsyslog git vim nano nginx chromium make npm \
                          unzip nginx procps php7.2 php7.2-opcache php7.2-sqlite3 php7.2-xml php7.2-cli php7.2-bz2 \
                          php7.2-readline php7.2-curl php7.2-fpm php7.2-json php7.2-mbstring php7.2-zip \
    && apt-get clean \
    && php7.2 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php7.2 composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && rm composer-setup.php \
    && chmod +x /usr/bin/composer

ADD docker/common/etc/crontab /etc/crontabs/node
ADD docker/common/entrypoint-prerenderer.sh /entrypoint-prerenderer.sh
ADD docker/common/etc/nginx/nginx.conf /etc/nginx/nginx.conf
ADD docker/common/etc/nginx/sites-enabled/default /etc/nginx/sites-enabled/default
ADD docker/common/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chmod +x entrypoint-prerenderer.sh

# add the whole application and pre-build
ADD . /www/frontend-prerenderer
RUN cd /www/frontend-prerenderer && make deploy migrate

RUN [ "cross-build-end" ]

VOLUME /www/frontend-prerenderer
EXPOSE 80
EXPOSE 9000

ENTRYPOINT ["/entrypoint-prerenderer.sh"]
