#!/bin/bash

set -e

user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`
home=`egrep "^$user_culti" /etc/passwd|awk -F":" '{print $6}'`

# Test of the connection:
/Applications/cultibox/xamppfiles/bin/mysql --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox -e "SELECT * FROM  configuration;" > /dev/null 2>&1
if [ $? -eq 0 ]; then
        result=`/Applications/cultibox/xamppfiles/bin/mysql --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 --batch cultibox -e "SELECT VERSION FROM configuration;"`
        for res in `echo $result`; do
            if [ "`echo $res|egrep [1-9].*\.[1-9].*\.[1-9].*-.*`" != "" ]; then
                echo $res
                exit 0
            fi
        done
else
        exit 1
fi
exit 0
