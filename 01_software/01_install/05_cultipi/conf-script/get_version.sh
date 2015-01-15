#!/bin/bash

set -e

user_culti="cultipi"
group_culti="cultipi"

# Test of the connection:
/usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox -e "SELECT * FROM  configuration;" > /dev/null 2>&1
if [ $? -eq 0 ]; then
        result=`/usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 --batch cultibox -e "SELECT VERSION FROM configuration;"`
        for res in `echo $result`; do
            if [ "`echo $res|egrep [1-9].*\.[0-9].*\.[0-9].*-.*`" != "" ]; then
                echo $res
                exit 0
            fi
        done
fi
exit 1

