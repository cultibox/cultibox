#!/bin/bash


set -e
user_culti="cultipi"
group_culti="cultipi"
home="/home/cultipi"
if [ "$1" != "" ]; then
    auto=$1
else 
    auto=""
fi


echo "-----------------------------------------------------------------"
echo "              Cultibox load database script                    "
echo "-----------------------------------------------------------------"
echo ""

if [ -f $home/cultibox/backup_cultibox.sql ]; then
    # Test of the connection:
    /usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --batch --port=3891 cultibox -e "SHOW TABLES;">/dev/null 2>&1
    if [ $? -eq 0 ]; then
        if [ "$auto" != "auto" ]; then
            yn=""
            while [ "$yn" != "Yes" ] && [ "$yn" != "Y" ] && [ "$yn" != "y" ]; do
                echo "Do you want to continue? (Y/N)"
                read yn
                if [ "$yn" == "No" ] || [ "$yn" == "N" ] || [ "$yn" == "n" ]; then
                    exit 0
                fi
            done
        fi

        echo "  * Loading $home/cultibox/backup_cultibox.sql file..."
        /usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < $home/cultibox/backup_cultibox.sql
        if [ $? -ne 0 ]; then
            echo "===== Error accessing cultibox database, exiting... ===="
            echo "... NOK"
            exit 1
        else
            echo "... OK";
            exit 0
        fi
    fi
else
    echo "  * Missing $home/cultibox/backup_cultibox.sql file..."
    echo "...NOK"
    exit 1
fi
exit 0
