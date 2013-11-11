#!/bin/bash

set -e

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`

echo "-----------------------------------------------------------------"
echo "              Cultibox backup database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ ! -d $HOME/.cultibox ]; then
    echo "  * Creating $HOME/.cultibox directory to store backup files"
    mkdir $HOME/.cultibox
    chown $user_culti:$group_culti $HOME/.cultibox
else 
    echo "  * $HOME/.cultibox already exists and will be used to store backup files"
fi

echo "  * Exporting your current databae..."
/Applications/cultibox/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $HOME/.cultibox/backup_cultibox.bak.new
if [ $? -eq 0 ]; then
    mv $HOME/.cultibox/backup_cultibox.bak.new $HOME/.cultibox/backup_cultibox.bak
    echo "... cultibox: OK"
else 
    rm $HOME/.cultibox/backup_cultibox.bak.new
    echo "==== Error during the backup of the cultibox database, exiting"
    echo "... NOK"
    exit 1
fi

/Applications/cultibox/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox_joomla > $HOME/.cultibox/backup_joomla.bak.new
if [ $? -eq 0 ]; then
    mv $HOME/.cultibox/backup_joomla.bak.new $HOME/.cultibox/backup_joomla.bak
    echo "... joomla: OK"
else
    rm $HOME/.cultibox/backup_joomla.bak.new
    echo "==== Error during the backup of the joomla database, exiting"
    echo "... NOK"
    exit 1
fi

#Saving the database in the directory $HOME/.cultibox:
echo "  * Saving previous Cultibox backup database..."
if [ -f $HOME/.cultibox/backup_cultibox.bak ]; then
    mv $HOME/.cultibox/backup_cultibox.bak $HOME/.cultibox/backup_cultibox.bak.old
    echo "... OK"
fi

echo "  * Saving previous Joomla backups database..."
if [ -f $HOME/.cultibox/backup_joomla.bak ]; then
    mv $HOME/.cultibox/backup_joomla.bak $HOME/.cultibox/backup_joomla.bak.old
    echo "... OK"
fi

chown $user_culti:$group_culti $HOME/.cultibox/*
exit 0
