#!/bin/bash


set -e
user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`
error=0


echo "-----------------------------------------------------------------"
echo "              Cultibox load database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ -f $home/.cultibox/backup_cultibox.bak ]; then
    # To load a previous database dump: deletion of the current database, creation of the new database, import of the previous dump.
    echo "  * Cultibox: deletion of the current database, creation of an empty database, import of your backup database..."
    # Test of the connection:
    /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox -e "SHOW TABLES;" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        rm -Rf /opt/cultibox/var/mysql/cultibox
        /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 -e "CREATE DATABASE cultibox;"
        /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < $home/.cultibox/backup_cultibox.bak
    else
        echo "===== Error accessing cultibox database, exiting... ===="
        echo "... NOK"
        exit 1
    fi
    echo "... OK"
else
    echo "  * Missing $home/.cultibox/backup_cultibox.bak file..."
    error=1
    echo "...NOK"
fi


if [ -f $home/.cultibox/backup_joomla.bak ]; then
    # To load a previous database dump: deletion of the current database, creation of the new database, import of the previous dump.
    echo "  * Joomla: deletion of the current database, creation of an empty database, import of your backup database..."
    # Test of the connection:
    /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox_joomla -e "SHOW TABLES;" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        rm -Rf /opt/cultibox/var/mysql/cultibox_joomla
        /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 -e "CREATE DATABASE cultibox_joomla;"
        /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox_joomla < $home/.cultibox/backup_joomla.bak
    else
        echo "===== Error accessing joomla database, exiting... ===="
        echo "... NOK"
        exit 1
    fi
    echo "... OK"
else
    echo "  * Missing $home/.cultibox/backup_joomla.bak file..."
    echo "...NOK"
    if [ $error -eq 1 ]; then
        exit 2
    fi
fi
exit 0

