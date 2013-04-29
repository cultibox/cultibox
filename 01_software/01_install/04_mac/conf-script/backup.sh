#!/bin/bash

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`

if [ ! -d $HOME/.cultibox ]; then
    mkdir $HOME/.cultibox
    chown $user_culti:$group_culti $HOME/.cultibox
fi
/Applications/cultibox/xamppfiles/bin/mysqldump -u root -h 127.0.0.1 --port=3891 -pcultibox cultibox > $HOME/.cultibox/backup_cultibox
chown $user_culti:$group_culti $HOME/.cultibox/backup_cultibox
