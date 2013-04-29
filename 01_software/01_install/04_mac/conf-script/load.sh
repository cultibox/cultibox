#!/bin/bash

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`

if [ -f $HOME/.cultibox/backup_cultibox ]; then
    /Applications/cultibox/xamppfiles/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox -e "DROP DATABASE cultibox;"
    /Applications/cultibox/xamppfiles/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox -e "CREATE DATABASE cultibox;"
    /Applications/cultibox/xamppfiles/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox cultibox < $HOME/.cultibox/backup_cultibox

    if [ -f /Applications/cultibox/sql_install/update_sql.sql ]; then
        /Applications/cultibox/xamppfiles/bin/mysql -u root -h 127.0.0.1 --port=3891 -pcultibox < /Applications/cultibox/sql_install/update_sql.sql 2>/dev/null
    fi    
fi
