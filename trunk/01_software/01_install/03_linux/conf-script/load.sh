#!/bin/bash

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`

if [ -f $home/.cultibox/backup_cultibox ]; then
    /opt/lampp/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox -e "DROP DATABASE cultibox;"
    /opt/lampp/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox -e "CREATE DATABASE cultibox;"
    /opt/lampp/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox cultibox < $home/.cultibox/backup_cultibox

    if [ -f /opt/lampp/sql_install/update_sql.sql ]; then
        /opt/lampp/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox < /opt/lampp/sql_install/update_sql.sql 2>/dev/null
    fi    
fi
