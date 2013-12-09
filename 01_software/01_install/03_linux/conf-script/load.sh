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

if [ -f $home/.cultibox/backup_cultibox.sql ]; then
    # Test of the connection:
    /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox -e "SHOW TABLES;" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        yn=""
        while [ "$yn" != "Yes" ] && [ "$yn" != "Y" ] && [ "$yn" != "y" ]; do
            echo "Do you want to continue? (Y/N)"
            read yn
            if [ "$yn" == "No" ] || [ "$yn" == "N" ] || [ "$yn" == "n" ]; then
                exit 0
            fi
        done
        echo "  * Loading $home/.cultibox/backup_cultibox.sql file..."
        /opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < $home/.cultibox/backup_cultibox.sql
    else
        echo "===== Error accessing cultibox database, exiting... ===="
        echo "... NOK"
        exit 1
    fi
    echo "... OK"
else
    echo "  * Missing $home/.cultibox/backup_cultibox.sql file..."
    error=1
    echo "...NOK"
fi

exit 0
