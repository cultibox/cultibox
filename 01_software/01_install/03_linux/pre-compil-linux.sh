#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

SRC_DIR=../../02_src/cultibox
DEST_DIR=../../01_install/01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      ubuntu32|ubuntu64 <version> ?jenkins?"
            echo "                      apt-gen <arch>"
            echo "                      clean"
            exit 1
}


#Print usage informations if a parameter is missing
if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

VERSION=$2

# Remove git pull when using jenkins
if [ "$3" == "" ]; then
    (cd ../../../ && git pull)
fi


case "$1" in
      "ubuntu64")
           (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl && pdflatex documentation.tex && pdflatex documentation.tex)
           rm -Rf ../01_src/01_xampp/*
           mkdir ../01_src/01_xampp/cultibox
           cp -R ./conf-package/DEBIAN64 ../01_src/01_xampp/cultibox/DEBIAN
           mkdir ../01_src/01_xampp/cultibox/opt
           mkdir -p ../01_src/01_xampp/cultibox/usr/share/applications/
           cp ./conf-package/cultibox.desktop ../01_src/01_xampp/cultibox/usr/share/applications/
            
           tar zxvfp xampp-linux-1.8.3.4-amd64.tar.gz -C ../01_src/01_xampp/cultibox/opt/

           cp -R ../../02_src/cultibox ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox
           mkdir -p ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/docs
           cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/docs/documentation_cultibox.pdf
           cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/opt/lampp/VERSION.txt
           cat ../../01_install/01_src/03_sd/version.txt > ../01_src/01_xampp/cultibox/opt/lampp/VERSION_FIRM.txt

           cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/cultibox/opt/lampp/etc/
           cp conf-lampp/properties.ini ../01_src/01_xampp/cultibox/opt/lampp/
           cp conf-lampp/adminer-4.1.0.php  ../01_src/01_xampp/cultibox/opt/lampp/htdocs/


           
            cat > ../01_src/01_xampp/cultibox/opt/lampp/etc/my-extra.cnf << "EOF" 
[client]
user="root"
password="cultibox"
EOF
           cp -R conf-script ../01_src/01_xampp/cultibox/opt/lampp/run
           cp conf-package/cultibox.png ../01_src/01_xampp/cultibox/opt/lampp/
           cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/opt/lampp/LICENSE.txt
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/opt/lampp/sql_install
           sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-amd64' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/update_sql.sql
           cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp -R daemon ../01_src/01_xampp/cultibox/opt/lampp/

           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_de.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_it.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_es.sql
           sed -i "s/Version: .*/Version: `echo $VERSION`-ubuntu/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/Version=.*/Version=`echo $VERSION`/g" ../01_src/01_xampp/cultibox/usr/share/applications/cultibox.desktop

           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-amd64'/" ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/libs/lib_configuration.php

           find ./../01_src/01_xampp/cultibox/opt/lampp -name ".git"|xargs rm -Rf
           mv ../01_src/01_xampp/cultibox/opt/lampp ../01_src/01_xampp/cultibox/opt/cultibox

           chown -R root:root ./../01_src/01_xampp/*

           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox
           
           mv cultibox.deb ../../03_linux/Output/cultibox-ubuntu-amd64_`echo $VERSION`.deb
      ;;
      "ubuntu32")
            (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl && tclsh ./parse_wiki.tcl && pdflatex documentation.tex)
            rm -Rf ../01_src/01_xampp/*
            mkdir ../01_src/01_xampp/cultibox
            cp -R ./conf-package/DEBIAN ../01_src/01_xampp/cultibox/DEBIAN
            mkdir ../01_src/01_xampp/cultibox/opt
            mkdir -p ../01_src/01_xampp/cultibox/usr/share/applications/
            cp ./conf-package/cultibox.desktop ../01_src/01_xampp/cultibox/usr/share/applications/

            tar zxvfp xampp-linux-1.8.3.4-i386.tar.gz -C ../01_src/01_xampp/cultibox/opt/

            cp -R ../../02_src/cultibox ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox
            mkdir -p ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/docs
            cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/docs/documentation_cultibox.pdf
            cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/opt/lampp/VERSION.txt
            cat ../../01_install/01_src/03_sd/version.txt > ../01_src/01_xampp/cultibox/opt/lampp/VERSION_FIRM.txt

            cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/
            cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/opt/lampp/etc/
            cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/opt/lampp/etc/extra/
            cp conf-lampp/properties.ini ../01_src/01_xampp/cultibox/opt/lampp/

            cp -R conf-script ../01_src/01_xampp/cultibox/opt/lampp/run
            cp conf-lampp/my.cnf ../01_src/01_xampp/cultibox/opt/lampp/etc/
            cp conf-package/cultibox.png ../01_src/01_xampp/cultibox/opt/lampp/
            cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/opt/lampp/LICENSE.txt
            cat > ../01_src/01_xampp/cultibox/opt/lampp/etc/my-extra.cnf << "EOF" 
[client]
user="root"
password="cultibox"
EOF
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/opt/lampp/sql_install
           sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-i386' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/update_sql.sql
           cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/tmp/
           cp -R daemon ../01_src/01_xampp/cultibox/opt/lampp/

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_de.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_it.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/sql_install/cultibox_es.sql

           sed -i "s/Version: .*/Version: `echo $VERSION`-ubuntu/g" ../01_src/01_xampp/cultibox/DEBIAN/control
           sed -i "s/Version=.*/Version=`echo $VERSION`/g" ../01_src/01_xampp/cultibox/usr/share/applications/cultibox.desktop
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-i386'/" ../01_src/01_xampp/cultibox/opt/lampp/htdocs/cultibox/main/libs/lib_configuration.php


           find ./../01_src/01_xampp/cultibox/opt/lampp -name ".git"|xargs rm -Rf
           mv ../01_src/01_xampp/cultibox/opt/lampp ../01_src/01_xampp/cultibox/opt/cultibox
           chown -R root:root ./../01_src/01_xampp/*
           cd ./../01_src/01_xampp/ && dpkg-deb --build cultibox

           mv cultibox.deb ../../03_linux/Output/cultibox-ubuntu-i386_`echo $VERSION`.deb
        ;;
      "apt-gen")
           cultibox_ubuntu="`ls -t Output/cultibox-ubuntu-$2*|head -1`"
           cp $cultibox_ubuntu repository_$2/binary/

           cultibox_ubuntu="`ls -t Output/cultibox-ubuntu-$2*|head -1`"
           cp $cultibox_ubuntu repository_$2/binary/

           cd repository_$2
           dpkg-scanpackages binary /dev/null | gzip -9c > binary/Packages.gz

           rm binary/cultibox*.deb
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
