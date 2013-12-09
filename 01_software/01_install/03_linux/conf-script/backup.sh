#!/bin/bash
set -e

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`

echo "-----------------------------------------------------------------"
echo "              Cultibox backup database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ ! -d $home/.cultibox ]; then
    echo "  * Creating $home/.cultibox directory to store backup files"
    mkdir $home/.cultibox
    chown $user_culti:$group_culti $home/.cultibox
else 
    echo "  * $home/.cultibox already exists and will be used to store backup files"
fi

echo "  * Saving previous Cultibox backup database..."
if [ -f $home/.cultibox/backup_cultibox.sql ]; then
    mv $home/.cultibox/backup_cultibox.sql $home/.cultibox/backup_cultibox.sql.old
    echo "... OK"
fi


echo "  * Exporting your current databae..."
/opt/cultibox/bin/mysqldump --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $home/.cultibox/backup_cultibox.sql
if [ $? -eq 0 ]; then
    echo "... cultibox: OK"
else 
    echo "==== Error during the backup of the cultibox database ===="
    if [ -f $home/.cultibox/backup_cultibox.sql.old ]; then
        rm -f $home/.cultibox/backup_cultibox.sql
        mv $home/.cultibox/backup_cultibox.sql.old $home/.cultibox/backup_cultibox.sql
    fi

    echo "... NOK"
    exit 1
fi

chown $user_culti:$group_culti $home/.cultibox/*
exit 0
