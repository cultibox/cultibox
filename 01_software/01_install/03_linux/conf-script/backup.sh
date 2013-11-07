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

#Saving the database in the directory $home/.cultibox:
echo "  * Saving your current database..."
if [ -f $home/.cultibox/backup_cultibox.bak ]; then
    echo "    --> A previous backup file was found: backup_cultibox.bak"
    echo "        This file will be move to backup_cultibox.bak.old"
    mv $home/.cultibox/backup_cultibox.bak $home/.cultibox/backup_cultibox.bak.old
fi
/opt/lampp/bin/mysqldump --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $home/.cultibox/backup_cultibox.bak
chown $user_culti:$group_culti $home/.cultibox/backup_cultibox.bak
echo "... OK"
