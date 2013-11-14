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

echo "  * Exporting your current databae..."
/opt/lampp/bin/mysqldump --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $home/.cultibox/backup_cultibox.bak.new
if [ $? -eq 0 ]; then
    echo "... cultibox: OK"
else 
    rm $home/.cultibox/backup_cultibox.bak.new
    echo "==== Error during the backup of the cultibox database, exiting ===="
    echo "... NOK"
    exit 1
fi

/opt/lampp/bin/mysqldump --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox_joomla > $home/.cultibox/backup_joomla.bak.new
if [ $? -eq 0 ]; then
    echo "... joomla: OK"
else
    rm $home/.cultibox/backup_joomla.bak.new
    echo "==== Error during the backup of the joomla database, exiting ===="
    echo "... NOK"
    exit 1
fi

#Saving the database in the directory $home/.cultibox:
echo "  * Saving previous Cultibox backup database..."
if [ -f $home/.cultibox/backup_cultibox.bak ]; then
    mv $home/.cultibox/backup_cultibox.bak $home/.cultibox/backup_cultibox.bak.old
    echo "... OK"
fi
echo "  * Saving previous Joomla backups database..."
if [ -f $home/.cultibox/backup_joomla.bak ]; then
    mv $home/.cultibox/backup_joomla.bak $home/.cultibox/backup_joomla.bak.old
    echo "... OK"
fi
mv $home/.cultibox/backup_cultibox.bak.new $home/.cultibox/backup_cultibox.bak
mv $home/.cultibox/backup_joomla.bak.new $home/.cultibox/backup_joomla.bak
chown $user_culti:$group_culti $home/.cultibox/*
exit 0
