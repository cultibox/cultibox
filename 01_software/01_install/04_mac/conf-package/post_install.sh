#!/bin/bash

echo "Configuring your CultiBox environment:"
user_culti=`who|head -1|awk -F" " '{print $1}'`
group_culti=`who|head -1|awk -F" " '{print $1}'|xargs id -gn`

chown -R $user_culti:$group_culti /Applications/XAMPP
sed -i '' "s/User nobody/User $user_culti/" /Applications/XAMPP/xamppfiles/etc/httpd.conf
sed -i '' "s/Group nogroup/Group $group_culti/" /Applications/XAMPP/xamppfiles/etc/httpd.conf
chown -R nobody /Applications/XAMPP/xamppfiles/var/mysql
chmod -R 775 /Applications/XAMPP/xamppfiles/var/mysql


/Applications/XAMPP/xamppfiles/xampp restart
sleep 3
/Applications/XAMPP/xamppfiles/bin/mysqladmin -u root -h localhost password cultibox
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h localhost --port=3891 -pcultibox < /Applications/XAMPP/sql_install/user_cultibox.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h localhost --port=3891 -pcultibox < /Applications/XAMPP/sql_install/joomla.sql
lang=`echo $LANG|grep -i fr`
if [ "$lang" != "" ]; then
        /Applications/XAMPP/xamppfiles/bin/mysql -u root -h localhost --port=3891 -pcultibox < /Applications/XAMPP/sql_install/cultibox_fr.sql
else
        /Applications/XAMPP/xamppfiles/bin/mysql -u root -h localhost --port=3891 -pcultibox < /Applications/XAMPP/sql_install/cultibox_en.sql
fi
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h localhost --port=3891 -pcultibox < /Applications/XAMPP/sql_install/fake_log.sql

echo "Installing CultiBox as a service:"
echo "/Applications/XAMPP/xamppfiles/xampp start" > /etc/rc.local

echo "Restarting the CultiBox interface:"
/Applications/XAMPP/xamppfiles/xampp restart
