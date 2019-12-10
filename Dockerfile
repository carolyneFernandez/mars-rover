FROM php:7.3.12-apache

RUN a2enmod rewrite
RUN a2enmod headers