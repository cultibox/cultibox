#!/bin/bash
set -e

user_culti="cultipi"
group_culti="cultipi"
home="/home/cultipi"

echo "-----------------------------------------------------------------"
echo "              Cultibox backup database script                    "
echo "-----------------------------------------------------------------"
echo ""

echo "  * Saving previous Cultibox backup database..."
if [ -f $home/cultibox/backup_cultibox.sql ]; then
    mv $home/cultibox/backup_cultibox.sql $home/cultibox/backup_cultibox.sql.old
    echo "... OK"
fi


echo "  * Exporting your current database..."
/usr/bin/mysqldump --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > $home/cultibox/backup_cultibox.sql
if [ $? -eq 0 ]; then
    echo "... cultibox: OK"
else 
    echo "==== Error during the backup of the cultibox database ===="
    if [ -f $home/cultibox/backup_cultibox.sql.old ]; then
        rm -f $home/cultibox/backup_cultibox.sql
        mv $home/cultibox/backup_cultibox.sql.old $home/cultibox/backup_cultibox.sql
    fi

    echo "... NOK"
    exit 1
fi

exit 0
