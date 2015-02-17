#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

function usage {
            echo "usage: $0"
            echo "                      cultipi <version>|version ?jenkins?"
            echo "                      cultibox <version>|version ?jenkins?"
            echo "                      cultiraz <version>|version ?jenkins?"
            echo "                      cultitime <version>|version ?jenkins?"
            echo "                      culticonf <version>|version ?jenkins?"
            echo "                      apt-gen"
            echo "                      clean"
            exit 1
}


#Print usage informations if a parameter is missing
if [ "$2" == "" ] && [ "$1" != "clean" ] && [ "$1" != "apt-gen" ]; then
    usage
fi

if [ "$2" == "version" ]; then
    if [ "$1" == "cultipi" ]; then
        VERSION=`cat ../../../04_CultiPi/01_Software/01_cultiPi/VERSION`
    elif [ "$1" == "cultibox" ]; then
        VERSION=`head -1 ../../CHANGELOG |sed -r 's#^.*\((.*)\).*$#\1#'`
    elif [ "$1" == "cultiraz" ]; then
        VERSION=`cat ../../../04_CultiPi/01_Software/05_cultiRAZ/VERSION`
    elif [ "$1" == "cultitime" ]; then
        VERSION=`cat ../../../04_CultiPi/01_Software/07_cultiTime/VERSION`
    elif [ "$1" == "culticonf" ]; then
        VERSION=`cat ../../../04_CultiPi/01_Software/02_cultiConf/VERSION`
    fi
else
    VERSION=$2
fi


# Remove svn up when using jenkins
if [ "$3" == "" ]; then
    (cd ../../../ && svn up)
fi

revision=`svn info ../../..|grep "Révision "|awk -F ": " '{print $2}'`

case "$1" in
      "cultipi")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultipi
           cp -R ./conf-package/DEBIAN-cultipi ../01_src/01_xampp/cultipi/DEBIAN

           mkdir -p ../01_src/01_xampp/cultipi/opt/cultipi
           mkdir -p ../01_src/01_xampp/cultipi/etc/init.d
           mkdir -p ../01_src/01_xampp/cultipi/etc/default
           mkdir -p ../01_src/01_xampp/cultipi/etc/cultipi

           cp -R ../../../04_CultiPi/01_Software/01_cultiPi/* ../01_src/01_xampp/cultipi/opt/cultipi/
           rm -f ../01_src/01_xampp/cultipi/opt/cultipi/VERSION
           cp -R ../../../04_CultiPi/02_conf/01_defaultConf_RPi  ../01_src/01_xampp/cultipi/etc/cultipi/
           cp -R ../../../04_CultiPi/02_conf/conf.xml  ../01_src/01_xampp/cultipi/etc/cultipi/

           cp ../../../04_CultiPi/01_Software/04_cultipi_service/etc/init.d/cultipi ../01_src/01_xampp/cultipi/etc/init.d/cultipi
           cp ../../../04_CultiPi/01_Software/04_cultipi_service/etc/default/culticron ../01_src/01_xampp/cultipi/etc/default/

           sed -i "s/Version: .*/Version: `echo $VERSION`-r`echo $revision`/g" ../01_src/01_xampp/cultipi/DEBIAN/control
           find ./../01_src/01_xampp/cultipi/ -name ".svn"|xargs rm -Rf 
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultipi
           
           mv cultipi.deb ../../05_cultipi/Output/cultipi-armhf_`echo $VERSION`-r`echo $revision`.deb
      ;;
      "cultibox")
           (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl && tclsh ./parse_wiki.tcl && pdflatex documentation.tex)
           rm -Rf ../01_src/01_xampp/*
           mkdir -p ../01_src/01_xampp/cultibox/var/www
           cp -R ./conf-package/DEBIAN-cultibox ../01_src/01_xampp/cultibox/DEBIAN

           cp -R ../../02_src/cultibox ../01_src/01_xampp/cultibox/var/www/cultibox
           cp ../../../02_documentation/02_userdoc/documentation.pdf Output/documentation_cultibox.pdf
           cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/var/www/cultibox/VERSION.txt

           cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/var/www/cultibox/LICENSE.txt
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install
           cat > ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/my-extra.cnf << "EOF" 
[client]
user="root"
password="cultibox"
EOF
           sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-armhf' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/update_sql.sql
           cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/var/www/cultibox/tmp/
           cp -R conf-script ../01_src/01_xampp/cultibox/var/www/cultibox/run

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_de.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_it.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/sql_install/cultibox_es.sql

           sed -i "s/Version: .*/Version: `echo $VERSION`-r`echo $revision`/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-armhf'/" ../01_src/01_xampp/cultibox/var/www/cultibox/main/libs/lib_configuration.php
           sed -i "s/^$GLOBALS.*\"cultibox\"/\$GLOBALS['MODE']=\"cultipi\"/g" ../01_src/01_xampp/cultibox/var/www/cultibox/main/libs/config.php 


           find ./../01_src/01_xampp/cultibox/ -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox

           mv cultibox.deb ../../05_cultipi/Output/cultibox-armhf_`echo $VERSION`-r`echo $revision`.deb
      ;;  
      "cultiraz")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultiraz
           cp -R ./conf-package/DEBIAN-cultiraz ../01_src/01_xampp/cultiraz/DEBIAN

           mkdir -p ../01_src/01_xampp/cultiraz/opt/cultiraz
           mkdir -p ../01_src/01_xampp/cultiraz/etc/init.d

           cp -R ../../../04_CultiPi/01_Software/05_cultiRAZ/* ../01_src/01_xampp/cultiraz/opt/cultiraz/
           rm -f ../01_src/01_xampp/cultiraz/opt/cultiraz/VERSION

           cp ../../../04_CultiPi/01_Software/06_cultiRAZ_service/etc/init.d/cultiraz ../01_src/01_xampp/cultiraz/etc/init.d/cultiraz

           sed -i "s/Version: .*/Version: `echo $VERSION`-r`echo $revision`/g" ../01_src/01_xampp/cultiraz/DEBIAN/control
           find ./../01_src/01_xampp/cultiraz/ -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultiraz

           mv cultiraz.deb ../../05_cultipi/Output/cultiraz-armhf_`echo $VERSION`-r`echo $revision`.deb
      ;;
      "cultitime")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultitime
           cp -R ./conf-package/DEBIAN-cultitime ../01_src/01_xampp/cultitime/DEBIAN

           mkdir -p ../01_src/01_xampp/cultitime/opt/cultitime
           mkdir -p ../01_src/01_xampp/cultitime/etc/init.d

           cp -R ../../../04_CultiPi/01_Software/07_cultiTime/* ../01_src/01_xampp/cultitime/opt/cultitime/
           rm -f ../01_src/01_xampp/cultitime/opt/cultitime/VERSION

           cp ../../../04_CultiPi/01_Software/08_cultiTime_service/etc/init.d/cultitime ../01_src/01_xampp/cultitime/etc/init.d/cultitime

           sed -i "s/Version: .*/Version: `echo $VERSION`-r`echo $revision`/g" ../01_src/01_xampp/cultitime/DEBIAN/control
           find ./../01_src/01_xampp/cultitime/ -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultitime

           mv cultitime.deb ../../05_cultipi/Output/cultitime-armhf_`echo $VERSION`-r`echo $revision`.deb
      ;;
      "culticonf")
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/culticonf
           mkdir -p ../01_src/01_xampp/culticonf/etc/cron.daily
           mkdir -p ../01_src/01_xampp/culticonf/etc/cron.hourly
           mkdir -p ../01_src/01_xampp/culticonf/etc/logrotate.d

           cp -R ./conf-package/DEBIAN-culticonf ../01_src/01_xampp/culticonf/DEBIAN

           cp ../../../04_CultiPi/01_Software/02_culticonf/etc/logrotate.d/cultipi ../01_src/01_xampp/cultipi/etc/logrotate.d/
           cp ../../../04_CultiPi/01_Software/02_culticonf/etc/cron.daily/cultipi ../01_src/01_xampp/cultipi/etc/cron.daily/ 
           cp ../../../04_CultiPi/01_Software/02_culticonf/etc/cron.hourly/cultipi ../01_src/01_xampp/cultipi/etc/cron.hourly/

           sed -i "s/Version: .*/Version: `echo $VERSION`-r`echo $revision`/g" ../01_src/01_xampp/culticonf/DEBIAN/control
           find ./../01_src/01_xampp/culticonf/ -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && dpkg-deb --build culticonf

           mv culticonf.deb ../../05_cultipi/Output/culticonf-armhf_`echo $VERSION`-r`echo $revision`.deb
      ;;
      "apt-gen")
           cultipi="`ls -t Output/cultipi*|head -1`"
           cp $cultipi repository/binary/
          
           cultibox="`ls -t Output/cultibox*|head -1`"
           cp $cultibox repository/binary/

           cultiraz="`ls -t Output/cultiraz*|head -1`"
           cp $cultiraz repository/binary/

           cultitime="`ls -t Output/cultitime*|head -1`"
           cp $cultitime repository/binary/

           culticonf="`ls -t Output/culticonf*|head -1`"
           cp $culticonf repository/binary/

           cd repository
           dpkg-scanpackages binary /dev/null | gzip -9c > binary/Packages.gz

           rm binary/culti*.deb
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
