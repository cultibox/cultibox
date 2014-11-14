#!/bin/bash

set -e

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`

echo "-----------------------------------------------------------------"
echo "              Cultibox backup database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ ! -d $HOME/cultibox ]; then
    echo "  * Creating $HOME/cultibox directory to store backup files"
    mkdir $HOME/cultibox
    chown $user_culti:$group_culti $HOME/cultibox
else 
    echo "  * $HOME/cultibox already exists and will be used to store backup files"
fi

echo "  * Saving previous Cultibox backup database..."
if [ -f $HOME/cultibox/backup_cultibox.sql ]; then
    mv $HOME/cultibox/backup_cultibox.sql $HOME/cultibox/backup_cultibox.sql.old
    echo "... OK"
fi

echo "  * Exporting your current database..."
/Applications/cultibox/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $HOME/cultibox/backup_cultibox.sql
if [ $? -eq 0 ]; then
    echo "... cultibox: OK"
else 
    echo "==== Error during the backup of the cultibox database ===="
    if [ -f $HOME/cultibox/backup_cultibox.sql.old ]; then
        rm -f  $HOME/cultibox/backup_cultibox.sql
        mv $HOME/cultibox/backup_cultibox.sql.old $HOME/cultibox/backup_cultibox.sql
    fi
    echo "... NOK"
    exit 1
fi

chown $user_culti:$group_culti $HOME/cultibox/*
exit 0
