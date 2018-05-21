FROM php:7.1-apache

##Install bash
RUN apt-get install bash

RUN mkdir /var/magento/ && chmod ugo+rw /var/magento

WORKDIR /var/magento/
ADD app /var/www/html/

CMD /etc/init.d/apache2 restart && tail -f /dev/null

EXPOSE 80