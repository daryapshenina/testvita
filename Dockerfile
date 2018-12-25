#Установка контейнера:
#cd %dockerfile_dir%
#sudo docker build -t gibdd .

#CID_GIBDD=$(docker run -d -p 8080:80 -p 221:22 -v /var/www/gibdd/test:/var/www gibdd) && docker inspect -format '{{ .NetworkSettings.IPAddress }}' ${CID_GIBDD}

#Получить IP адрес контейнера
#docker inspect -format '{{ .NetworkSettings.IPAddress }}' ${CID_GIBDD}

#Запуск контейнера после остановки:
#docker start ${CID_GIBDD}

FROM ubuntu:12.04

#Выставляем для пользователя root пароль 1
RUN echo 'root:1' |chpasswd

#От какокого пользователя работает PHP-FPM 
ENV WWW_USER altarix

ENV DEBIAN_FRONTEND noninteractive
#RUN sed -i -e 's/archive.ubuntu.com\|security.ubuntu.com/old-releases.ubuntu.com/g' /etc/apt/sources.list

# Устанавливаем всё необходимое, после чего чистим кеш репозиториев для уменьшения размера образа
RUN apt-get update -qq
RUN apt-get install -qqy python-software-properties software-properties-common
RUN add-apt-repository -y ppa:chris-lea/node.js
RUN add-apt-repository -y ppa:nginx/stable

# Для установки mongo

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6
RUN echo "deb [ arch=amd64 ] http://repo.mongodb.org/apt/ubuntu precise/mongodb-org/3.4 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.4.list


RUN apt-get update -qq
RUN apt-get upgrade -qq
RUN apt-get install -qqy build-essential libaio1
RUN apt-get install -qqy nodejs
RUN apt-get install -qqy nginx
RUN apt-get install -qqy nginx
RUN apt-get install -qqy sphinxsearch
RUN apt-get install -qqy mongodb-org
RUN apt-get install -qqy mc nano curl wget
RUN apt-get install -qqy subversion git mercurial


#START PHP
	RUN apt-get install -qqy \
		php5-common \
		php5-fpm php5-cli\
		php5-dev php-pear \
		php5-xdebug \
		php-apc \
		php5-mcrypt \
		php5-gd \
		php5-pgsql \
		php5-json \
		php5-curl \
		php5-mysql \

		php5-imagick

	# Всё что касается memcache
	RUN apt-get install -qqy memcached php5-memcache php5-memcached

	#Включаем отладку в xdebug
	RUN echo \
		'xdebug.default_enable=1 \n\
xdebug.var_display_max_children=-1 \n\
xdebug.var_display_max_data=-1 \n\
xdebug.var_display_max_depth=-1 \n\
xdebug.var_display_max_depth=-1 \n\
xdebug.remote_connect_back=0 \n\
xdebug.remote_log=/tmp/xdebug_remote.log \n\
xdebug.remote_host=172.17.42.1 \n\
xdebug.remote_port=9000 \n\
xdebug.idekey=DEBUG \n\
xdebug.remote_autostart=1 \n\
xdebug.remote_enable=1' >> /etc/php5/conf.d/20-xdebug.ini

	#Ставим и настраиваем Oracle client и php oci8-2.0.11
	ADD bundle /tmp
	RUN dpkg -i /tmp/oracle-instantclient11.2-*.deb
	RUN ln -s /usr/lib/oracle/11.2/client64/bin/sqlplus /usr/bin/sqlplus
	ENV ORACLE_HOME /usr/lib/oracle/11.2/client64
	ENV SQLPATH $ORACLE_HOME"/lib/"
	ENV LD_LIBRARY_PATH $SQLPATH
	ENV NLS_LANG "AMERICAN_AMERICA.AL32UTF8"
	ENV PATH $PATH:$HOME/bin:$ORACLE_HOME/bin
	RUN echo \
		'export ORACLE_HOME="/usr/lib/oracle/11.2/client64" \n\
		export SQLPATH=$ORACLE_HOME"/lib/" \n\
		export LD_LIBRARY_PATH=$ORACLE_HOME"/lib/" \n\
		export NLS_LANG="AMERICAN_AMERICA.AL32UTF8" \n\
		PATH=$PATH:$HOME/bin:$ORACLE_HOME/bin \n\
		export PATH' >> /etc/bash.bashrc
	RUN echo 'instantclient,/usr/lib/oracle/11.2/client64/lib' | pecl install oci8-2.0.11
	RUN echo \
		'extension=oci8.so' >> /etc/php5/conf.d/oci8.ini

	RUN pecl install mongo
	RUN echo \
		'extension=mongo.so' >> /etc/php5/conf.d/mongo.ini
#END PHP


RUN mkdir -p /var/commands/mongo
RUN mkdir -p /data/db

COPY bundle/mongo/*.js /var/commands/mongo/

RUN cp /tmp/mongod.conf /etc/mongod.conf
RUN cp /tmp/*.ora /usr/lib/oracle/11.2/client64/bin/
RUN cp /tmp/*.ini /usr/local/etc
RUN ln -s /usr/local/etc/odbc.ini /etc/odbc.ini
RUN ln -s /usr/local/etc/odbcinst.ini /etc/odbcinst.ini
RUN echo "user ${WWW_USER};" > /etc/nginx/nginx.conf && cat /tmp/nginx.conf >> /etc/nginx/nginx.conf
RUN echo '/etc/ld.so.conf.d/oracle.conf' >> /etc/ld.so.conf.d/oracle.conf
RUN ldconfig
RUN dpkg -i /tmp/td-agent_2.3.4-0_amd64.deb

#SSH
RUN apt-get -qqy install openssh-server
RUN mkdir -p /var/run/sshd
RUN sed -ri 's/UsePAM yes/#UsePAM yes/g' /etc/ssh/sshd_config
RUN sed -ri 's/#UsePAM no/UsePAM no/g' /etc/ssh/sshd_config
#RUN sed -ri 's/PermitRootLogin without-password/PermitRootLogin yes/g' /etc/ssh/sshd_config

#Создаём ключ хоста, чтобы не было проблем с обновлением ключа при подключении
RUN service ssh start; service ssh stop

RUN useradd ${WWW_USER} -s /bin/bash -m

RUN sed -ri "s/www-data/${WWW_USER}/" /etc/php5/fpm/pool.d/www.conf
#RUN sed -ri "s/listen = 127.0.0.1:9000/listen =\/var\/run\/php5-fpm.sock/" /etc/php5/fpm/pool.d/www.conf

RUN sed -i 's/memory_limit = 128M/memory_limit = 512M/g' /etc/php5/fpm/php.ini  

#ADD nginx.conf /etc/nginx/nginx.conf
#RUN sed -ri "s/user www-data/user ${WWW_USER}/" /etc/nginx/nginx.conf
RUN rm /etc/nginx/sites-available/default
RUN ln -s /var/www/default.conf /etc/nginx/sites-available/default
RUN cat /tmp/profile >> /etc/profile

#START CLEAN 
	RUN apt-get clean -y && rm -rf \
		/var/lib/apt/lists/* \
		/tmp/* \
		/var/tmp/*
#END CLEAN

RUN npm install -g fsmonitor

# fixed by sotnikovds <sotnikovds@altarix.ru>
COPY bundle/sphinxsearch_2.2.11.deb /sphinxsearch_2.2.11.deb
RUN apt-get update && apt-get install -y unixodbc sphinxsearch
RUN dpkg -i /sphinxsearch_2.2.11.deb

COPY bundle/sphinx.conf /etc/sphinxsearch/sphinx.conf

RUN mkdir -p /usr/lib/oracle/11.2/client64/network/admin

COPY bundle/odbc.ini        /usr/local/etc
COPY bundle/odbcinst.ini    /usr/local/etc
COPY bundle/sqlnet.ora      /usr/lib/oracle/11.2/client64/network
COPY bundle/listener.ora    /usr/lib/oracle/11.2/client64/network
COPY bundle/tnsnames.ora    /usr/lib/oracle/11.2/client64/network/admin


#Устанавливаем PostgreSQL (from sotnikovds <sotnikovds@altarix.ru>)

ARG pgversion=9.6

RUN apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys B97B0AFCAA1A47F044F244A07FCC7D46ACCC4CF8
RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ precise-pgdg main" > /etc/apt/sources.list.d/pgdg.list

#RUN apt-get update && apt-get install -y --allow-unauthenticated \
# python-software-properties software-properties-common postgresql-9.3 postgresql-client-9.3 postgresql-contrib-9.3


RUN apt-get update
RUN apt-get install -y mysql-server
RUN apt-get install -y phpmyadmin
# && apt-get upgrade -y
RUN apt-get install -y postgresql-$pgversion
RUN apt-get install -y postgresql-contrib-$pgversion
RUN apt-get install -y sudo \
				less \
				nano




# Adjust PostgreSQL configuration so that remote connections to the
# database are possible.
RUN echo "host all  all    0.0.0.0/0  md5" >> /etc/postgresql/$pgversion/main/pg_hba.conf

# And add ``listen_addresses`` to ``/etc/postgresql/9.3/main/postgresql.conf``
RUN echo "listen_addresses='*'" >> /etc/postgresql/$pgversion/main/postgresql.conf


ADD wwwdev.sh /usr/bin/wwwdev
RUN chmod +x /usr/bin/wwwdev

EXPOSE 54321

EXPOSE 27017
VOLUME /var/www
EXPOSE 80 22 9001

ENTRYPOINT ["wwwdev"]
