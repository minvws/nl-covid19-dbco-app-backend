FROM ubuntu:18.04

ENV DEBIAN_FRONTEND="noninteractive"
ENV TZ="Europe/Amsterdam"

# Install packages and extensions
RUN apt-get -qq update && \
    apt-get -y install software-properties-common && \
    add-apt-repository ppa:ondrej/php && \
    apt-get -qq update && \
    apt-get -y install php7.4 php7.4-cli php7.4-json php7.4-zip php7.4-opcache php7.4-pgsql php7.4-mysql \
                       php7.4-curl php7.4-xml php7.4-mbstring curl git wget vim unzip libzip-dev libpq-dev libicu-dev \
                       apt-transport-https ca-certificates gnupg libaio1 \
                       vim python3 python3-pip && \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite && \
    a2enmod headers && \
    a2enmod proxy && \
    a2enmod proxy_http && \
    a2enmod rewrite deflate

# Install Composer and prestissimo globally for increased performance
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer self-update

# Copy assets and sources
COPY shared/assets/api/default.conf /etc/apache2/sites-available/000-default.conf
COPY shared/assets/api/entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh
COPY shared/assets/api/api.ini /usr/local/etc/php/conf.d/api.ini
COPY shared/assets/api/security.conf /etc/apache2/conf-available/security.conf
COPY shared/assets/api/apache2.conf /etc/apache2/apache2.conf
COPY shared/assets/api/ports.conf /etc/apache2/ports.conf
COPY shared/assets/api/mpm_prefork.conf /etc/apache2/mods-available/mpm_prefork.conf
COPY shared/assets/api/mpm_prefork_default.conf /etc/apache2/mods-available/mpm_prefork_default.conf

COPY healthauthority-api/assets/cs_pkcs11_R2.cfg /etc/utimaco/cs_pkcs11_R2.cfg
COPY healthauthority-api/assets/libcs_pkcs11_R2.so /usr/lib/utimaco/libcs_pkcs11_R2.so
ENV PKCS_MODULE=/usr/lib/utimaco/libcs_pkcs11_R2.so

COPY shared/library /shared/library
COPY shared/packages /shared/packages
COPY healthauthority-api/src /src

RUN cd /src && composer install && \
    pip3 install python-pkcs11 && \
    usermod -u 1000 www-data

ENV APACHE_CONFDIR /etc/apache2
ENV APACHE_ENVVARS $APACHE_CONFDIR/envvars

RUN set -eux; \
    \
# generically convert lines like
#   export APACHE_RUN_USER=www-data
# into
#   : ${APACHE_RUN_USER:=www-data}
#   export APACHE_RUN_USER
# so that they can be overridden at runtime ("-e APACHE_RUN_USER=...")
	sed -ri 's/^export ([^=]+)=(.*)$/: ${\1:=\2}\nexport \1/' "$APACHE_ENVVARS"; \
	\
# setup directories and permissions
	. "$APACHE_ENVVARS"; \
	for dir in \
		"$APACHE_LOCK_DIR" \
		"$APACHE_RUN_DIR" \
		"$APACHE_LOG_DIR" \
	; do \
		rm -rvf "$dir"; \
		mkdir -p "$dir"; \
		chown "$APACHE_RUN_USER:$APACHE_RUN_GROUP" "$dir"; \
# allow running as an arbitrary user (https://github.com/docker-library/php/issues/743)
		chmod 777 "$dir"; \
	done; \
	\
# logs should go to stdout / stderr
    ln -sfT /dev/stderr "$APACHE_LOG_DIR/error.log"; \
    ln -sfT /dev/stdout "$APACHE_LOG_DIR/access.log"; \
    ln -sfT /dev/stdout "$APACHE_LOG_DIR/other_vhosts_access.log"; \
    chown -R --no-dereference "$APACHE_RUN_USER:$APACHE_RUN_GROUP" "$APACHE_LOG_DIR"; \
    ln -sfT /dev/stdout "/var/log/syslog"; \
    chown -R --no-dereference "$APACHE_RUN_USER:$APACHE_RUN_GROUP" /src/var/cache

# Environment
ENV PATH $PATH:/root/.composer/vendor/bin

WORKDIR /src

EXPOSE 8080/tcp

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2ctl", "-D", "FOREGROUND"]
