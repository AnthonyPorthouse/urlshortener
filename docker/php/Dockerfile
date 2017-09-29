FROM php:fpm

MAINTAINER Anthony Porthouse <anthony@porthou.se>

RUN docker-php-ext-install -j$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) pdo_mysql