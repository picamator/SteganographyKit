FROM php:5.6-cli
ARG host_ip

RUN apt-get update

# graphviz
RUN apt-get -y install graphviz

# git
RUN apt-get -y install git

# composer
RUN php -r "file_put_contents('composer-setup.php', file_get_contents('https://getcomposer.org/installer'));"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === 'aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# ssh, source https://docs.docker.com/engine/examples/running_ssh_service/ with correction https://github.com/docker/docker/issues/23621#issuecomment-226575258
RUN apt-get -y install openssh-server

RUN mkdir /var/run/sshd
RUN echo 'root:screencast' | chpasswd
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN sed -i 's/PermitRootLogin without-password/PermitRootLogin yes/' /etc/ssh/sshd_config

# SSH login fix. Otherwise user is kicked off after login
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd

ENV NOTVISIBLE "in users profile"
RUN echo "export VISIBLE=now" >> /etc/profile

# xdebug
RUN apt-get -y install wget

RUN mkdir -p /usr/lib/php/20151012

RUN wget -O ~/xdebug-2.4.1.tgz http://xdebug.org/files/xdebug-2.4.1.tgz
RUN tar -xvzf ~/xdebug-2.4.1.tgz
RUN rm ~/xdebug-2.4.1.tgz
RUN cd xdebug-2.4.1 && phpize
RUN cd xdebug-2.4.1 && ./configure
RUN cd xdebug-2.4.1 && make
RUN cd xdebug-2.4.1 && cp modules/xdebug.so /usr/lib/php/20151012
RUN rm -rf xdebug-2.4.1

# xdebug config cli
ADD ./dev/docker/php/config/20-xdebug.ini /usr/local/etc/php/conf.d/
RUN echo "xdebug.remote_host = $host_ip" >> /usr/local/etc/php/conf.d/20-xdebug.ini

# php
RUN apt-get -y install libcurl4-gnutls-dev
RUN docker-php-ext-install -j$(nproc) curl

RUN apt-get install -y zlib1g-dev
RUN docker-php-ext-install -j$(nproc) zip

RUN apt-get install -y libxslt-dev
RUN docker-php-ext-install -j$(nproc) xsl

RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libpng12-dev
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install -j$(nproc) gd

# php.ini
ADD  ./dev/docker/php/config/php.ini /usr/local/etc/php/

# volume
RUN mkdir /SteganographyKit
VOLUME /SteganographyKit

# workdir
WORKDIR /SteganographyKit

# expose ports
EXPOSE 22
CMD ["/usr/sbin/sshd", "-D"]
