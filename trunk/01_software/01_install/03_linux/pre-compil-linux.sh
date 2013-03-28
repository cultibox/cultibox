#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir
(cd ../../../ && svn up)
(cd ../../../wiki/ && svn up)
SRC_DIR=../../02_src/joomla
DEST_DIR=../../01_install/01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      ubuntu32|ubuntu64 <version>"
            echo "                      ubuntu32-admin|ubuntu64-admin <version>"
            echo "                      clean"
            exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

VERSION=$2


case "$1" in
      "ubuntu64"|"ubuntu64-admin" )
            rm -Rf ../01_src/01_xampp/*
            mkdir ../01_src/01_xampp/cultibox
            cp -R ./conf-package/DEBIAN64 ../01_src/01_xampp/cultibox/DEBIAN
            mkdir ../01_src/01_xampp/cultibox/opt
            mkdir -p ../01_src/01_xampp/cultibox/usr/share/applications/
            cp ./conf-package/cultibox.desktop ../01_src/01_xampp/cultibox/usr/share/applications/
            

            if [ "$1" == "ubuntu64" ]; then
                tar zxvfp xampp-linux-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox/opt/
            else
                tar zxvfp xampp-linux-admin-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox/opt/
            fi

            cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox
            cp -R ../../../wiki/* ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/modules/wiki/
            cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/opt/lampp/VERSION

           cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-script/* ../01_src/01_xampp/cultibox/opt/lampp/
           cp conf-package/cultibox.png ../01_src/01_xampp/cultibox/opt/lampp/
           cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/opt/lampp/LICENSE.txt
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/cultibox/opt/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/opt/lampp/sql_install
           cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/emetteur.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/sht.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.ico ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.html ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
            
           cp -R daemon ../01_src/01_xampp/cultibox/opt/lampp/

           if [ "$1" == "ubuntu64-admin" ]; then
                cp conf-lampp/config.inc.php ../01_src/01_xampp/cultibox/opt/lampp/phpmyadmin/
           fi

           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_en.sql
           sed -i "s/Version: .*/Version: `echo $VERSION`-ubuntu/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/Version=.*/Version=`echo $VERSION`/g" ../01_src/01_xampp/cultibox/usr/share/applications/cultibox.desktop

           find ./../01_src/01_xampp/cultibox/opt/lampp -name ".svn"|xargs rm -Rf
           mv ../01_src/01_xampp/cultibox/opt/lampp ../01_src/01_xampp/cultibox/opt/cultibox

           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox
           
           if [ "$1" == "ubuntu64" ]; then
                mv cultibox.deb ../../03_linux/Output/cultibox-ubuntu-amd64_`echo $VERSION`.deb
           else
                mv cultibox.deb ../../03_linux/Output/cultibox-admin-ubuntu-amd64_`echo $VERSION`.deb
           fi 
      ;;
      "ubuntu32"|"ubuntu32-admin")
            rm -Rf ../01_src/01_xampp/*
            mkdir ../01_src/01_xampp/cultibox
            cp -R ./conf-package/DEBIAN ../01_src/01_xampp/cultibox/DEBIAN
            mkdir ../01_src/01_xampp/cultibox/opt
            mkdir -p ../01_src/01_xampp/cultibox/usr/share/applications/
            cp ./conf-package/cultibox.desktop ../01_src/01_xampp/cultibox/usr/share/applications/

            if [ "$1" == "ubuntu32" ]; then
                tar zxvfp xampp-linux-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox/opt/
            else
                tar zxvfp xampp-linux-admin-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox/opt/
            fi

            cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox
            cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/opt/lampp/VERSION

           cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/extra/
           cp conf-script/* ../01_src/01_xampp/cultibox/opt/lampp/
           cp conf-lampp/my.cnf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-package/cultibox.png ../01_src/01_xampp/cultibox/opt/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/cultibox/opt/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/opt/lampp/sql_install
           cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/emetteur.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/sht.hex ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.ico ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.html ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/

           cp -R daemon ../01_src/01_xampp/cultibox/opt/lampp/

           if [ "$1" == "ubuntu32-admin" ]; then
                cp conf-lampp/config.inc.php ../01_src/01_xampp/cultibox/opt/lampp/phpmyadmin/
           fi

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_en.sql

           sed -i "s/Version: .*/Version: `echo $VERSION`-ubuntu/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/Version=.*/Version=`echo $VERSION`/g" ../01_src/01_xampp/cultibox/usr/share/applications/cultibox.desktop

           find ./../01_src/01_xampp/cultibox/opt/lampp -name ".svn"|xargs rm -Rf
           mv ../01_src/01_xampp/cultibox/opt/lampp ../01_src/01_xampp/cultibox/opt/cultibox
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox

           if [ "$1" == "ubuntu32" ]; then
                mv cultibox.deb ../../03_linux/Output/cultibox-ubuntu-i386_`echo $VERSION`.deb
           else
                mv cultibox.deb ../../03_linux/Output/cultibox-admin-ubuntu-i386_`echo $VERSION`.deb
           fi
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
