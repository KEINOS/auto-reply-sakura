# Docker container for Unit Test. This Dockerfile will be called
# from docker-compose in the parent directory.
FROM php:5.6.40-cli-alpine

COPY ./config/php.ini-sakura-production /usr/local/etc/php/php.ini
COPY ./docker/entrypoint.sh /entrypoint.sh
COPY ./ /app

RUN \
    apk --no-cache add \
        # Needs to install composer package
        git \
        && \
    docker-php-ext-install pcntl && \
    # Install and setup composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=bin --filename=composer --quiet && \
    rm composer-setup.php && \
    composer --version && \
    mkdir -p ~/.composer && \
    wget https://composer.github.io/releases.pub -O ~/.composer/keys.tags.pub && \
    wget https://composer.github.io/snapshots.pub -O ~/.composer/keys.dev.pub && \
    composer diagnose && \
    echo 'set -x' > /root/.profile && \
    cd /app && \
    composer install --no-interaction --no-suggest

WORKDIR /app

ENTRYPOINT [ "/entrypoint.sh" ]