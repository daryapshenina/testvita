#!/bin/bash

start() {
	service memcached start
	service php5-fpm start
	chmod 0666 /var/run/php5-fpm.sock
	nginx



#	mongod --fork --dbpath /var/lib/mongodb/ --smallfiles --logpath /var/log/mongodb.log --logappend
	start-stop-daemon --start --pidfile /var/run/sshd.pid --exec /usr/sbin/sshd -- -p 22
	echo "READY"

mysqld

	service postgresql start

	sudo -u postgres psql -c "CREATE USER vitaweb WITH SUPERUSER PASSWORD 'iddqd225';"
	sudo -u postgres psql -c "CREATE DATABASE vitaweb;"

	export PGPASSWORD=iddqd225

    service mysql start



	fsmonitor -q -d /var/www "+//default.conf" bash -c "nginx -s reload &&  echo 'nginx reload with default.conf'"
}

case "${1}" in
	bash) 
		bash
		;;
	*)             
		start
		;;
esac