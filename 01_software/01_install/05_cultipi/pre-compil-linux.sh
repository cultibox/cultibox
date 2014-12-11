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
#if [ "$3" == "" ]; then
    #(cd ../../../ && svn up)
#fi


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
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
