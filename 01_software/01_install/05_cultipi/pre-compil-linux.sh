#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

function usage {
            echo "usage: $0"
            echo "                      cultipi <version> ?jenkins?"
            echo "                      cultinet <version> ?jenkins?"
            echo "                      cultipi <version> ?jenkins?"
            echo "                      clean"
            exit 1
}


#Print usage informations if a parameter is missing
if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

VERSION=$2

# Remove svn up when using jenkins
if [ "$3" == "" ]; then
    (cd ../../../ && svn up)
fi


case "$1" in
      "cultipi")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultipi
           cp -R ./conf-package/DEBIAN-cultipi ../01_src/01_xampp/cultipi/DEBIAN

           mkdir -p ../01_src/01_xampp/cultipi/opt/cultipi
           mkdir -p ../01_src/01_xampp/cultipi/etc/init.d
           mkdir -p ../01_src/01_xampp/cultipi/etc/cultipi

           cp -R ../../../04_CultiPi/01_Software/cultiPi ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/01_Software/lib ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/01_Software/serverAcqSensor ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/01_Software/serverI2C ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/01_Software/serverLog ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/01_Software/serverPlugUpdate ../01_src/01_xampp/cultipi/opt/cultipi/
           cp -R ../../../04_CultiPi/02_conf/00_defaultConf  ../01_src/01_xampp/cultipi/etc/cultipi/
           cp -R ../../../04_CultiPi/02_conf/conf.xml  ../01_src/01_xampp/cultipi/etc/cultipi/

           cp ../../../04_CultiPi/01_Software/cultipi_service/etc/init.d/cultipi ../01_src/01_xampp/cultipi/etc/init.d/cultipi

           sed -i "s/Version: .*/Version: `echo $VERSION`-debian/g" ../01_src/01_xampp/cultipi/DEBIAN/control
          
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultipi
           
           mv cultipi.deb ../../05_cultipi/Output/cultipi_`echo $VERSION`.deb
      ;;
      "cultinet")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultinet
           cp -R ./conf-package/DEBIAN-cultinet ../01_src/01_xampp/cultinet/DEBIAN

           mkdir -p ../01_src/01_xampp/cultinet/var/www

           cp -R ../../../04_CultiPi/01_Software/cultinet ../01_src/01_xampp/cultinet/var/www

           sed -i "s/Version: .*/Version: `echo $VERSION`-debian/g" ../01_src/01_xampp/cultinet/DEBIAN/control

           cd ./../01_src/01_xampp/ && dpkg-deb --build cultinet

           mv cultinet.deb ../../05_cultipi/Output/cultinet_`echo $VERSION`.deb
      ;;
      "cultibox")
           (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl && tclsh ./parse_wiki.tcl && pdflatex documentation.tex)
           rm -Rf ../01_src/01_xampp/*
           mkdir -p ../01_src/01_xampp/cultibox/var/www
           cp -R ./conf-package/DEBIAN-cultibox ../01_src/01_xampp/cultibox/DEBIAN

           cp -R ../../02_src/cultibox ../01_src/01_xampp/cultibox/var/www/cultibox
           cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/var/www/cultibox/main/docs/documentation_cultibox.pdf
           cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/var/www/cultibox/VERSION.txt

           cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/var/www/cultibox/LICENSE.txt
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install
           cat > ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/my-extra.cnf << "EOF" 
[client]
user="root"
password="cultibox"
EOF
           sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-armhf' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/update_sql.sql
           cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_de.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_it.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_es.sql

           sed -i "s/Version: .*/Version: `echo $VERSION`-debian/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/libs/lib_configuration.php


           find ./../01_src/01_xampp/cultibox/opt/lampp -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox

           mv cultibox.deb ../../03_linux/Output/cultibox-armhf_`echo $VERSION`.deb
      ;;  
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
