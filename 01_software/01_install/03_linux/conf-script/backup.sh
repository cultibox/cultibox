#!/bin/bash

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`
if [ ! -d $home/.cultibox ]; then
    mkdir $home/.cultibox
    chown $user_culti:$group_culti $home/.cultibox
fi
/opt/lampp/bin/mysqldump -u root -h localhost --port=3891 -pcultibox cultibox > $home/.cultibox/backup_cultibox
chown $user_culti:$group_culti $home/.cultibox/backup_cultibox
