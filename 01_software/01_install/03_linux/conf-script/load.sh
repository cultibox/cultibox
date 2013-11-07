#!/bin/bash


set -e
user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`

echo "-----------------------------------------------------------------"
echo "              Cultibox load database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ -f $home/.cultibox/backup_cultibox.bak ]; then
    # To load a previous database dump: deletion of the current database, creation of the new database, import of the previous dump.
    echo "  * Deletion of the current database, creation of an empty database, import of your backup database..."
    /opt/lampp/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 -e "DROP DATABASE cultibox;"
    /opt/lampp/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 -e "CREATE DATABASE cultibox;"
    /opt/lampp/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < $home/.cultibox/backup_cultibox.bak
    echo "... OK"
else
    echo "  * Missing $home/.cultibox/backup_cultibox.bak file..."
    echo "...NOK"
fi
