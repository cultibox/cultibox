#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir
(cd ../../../ && svn up)
SRC_DIR=../../02_src/joomla
DEST_DIR=../01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      snow-leopard <version>"
            echo "                      clean"
            exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

VERSION=$2

case "$1" in
      "snow-leopard" )
            rm -Rf ../01_src/01_xampp/*
            tar zxvfp xampp-mac-lite-1.7.3.tar.gz -C ../01_src/01_xampp/
            cp -R ../../02_src/joomla ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox
            cat ../../CHANGELOG > ../01_src/01_xampp/XAMPP/VERSION.txt

            cp conf-lampp/httpd.conf ../01_src/01_xampp/XAMPP/xamppfiles/etc/
            cp conf-lampp/php.ini ../01_src/01_xampp/XAMPP/xamppfiles/etc/
            cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/XAMPP/xamppfiles/etc/extra/
            cp conf-lampp/my.cnf ../01_src/01_xampp/XAMPP/xamppfiles/etc/
            cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/XAMPP/sd
            cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/XAMPP/sql_install
            mkdir ../01_src/01_xampp/XAMPP/package
            cp conf-package/takecontrol.png ../01_src/01_xampp/XAMPP/package
            cp conf-package/lgpl3.txt ../01_src/01_xampp/XAMPP/LICENSE.txt
            cp conf-package/*.sh ../01_src/01_xampp/XAMPP/package/
            cat ../../CHANGELOG > ../01_src/01_xampp/XAMPP/VERSION.txt

            #cp conf-script/* ../01_src/01_xampp/cultibox/opt/lampp/
            cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox/tmp/
            cp ../../01_install/01_src/03_sd/emetteur.hex ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox/tmp/
            cp ../../01_install/01_src/03_sd/sht.hex ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox/tmp/
            cp ../../01_install/01_src/03_sd/cultibox.ico ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox/tmp/
            cp ../../01_install/01_src/03_sd/cultibox.html ../01_src/01_xampp/XAMPP/xamppfiles/htdocs/cultibox/tmp/

           #cp -R daemon ../01_src/01_xampp/cultibox/opt/lampp/

           #if [ "$1" == "ubuntu64-admin" ]; then
           #     cp conf-lampp/config.inc.php ../01_src/01_xampp/cultibox/opt/lampp/phpmyadmin/
           #fi


            find ../01_src/01_xampp/XAMPP/ -name ".svn"|xargs rm -Rf
            cd ../01_src/01_xampp/ && tar pzcvf cultibox.mac.tar.gz XAMPP
            mv cultibox.mac.tar.gz ../../04_mac/Output/
            
      ;; 
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
