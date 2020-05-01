FROM webdevops/php-nginx-dev:7.4

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV XDEBUG_REMOTE_AUTOSTART=1
ENV XDEBUG_REMOTE_CONNECT_BACK=1
ENV XDEBUG_REMOTE_PORT=9000

COPY .docker/conf/nginx/vhost.conf /opt/docker/etc/nginx/vhost.conf
COPY .docker/conf/nginx/10-location-root.conf /opt/docker/etc/nginx/vhost.common.d/10-location-root.conf

COPY .docker/conf/supervisor/* /opt/docker/etc/supervisor.d/

WORKDIR /var/www/html

COPY . .


