#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

echo "Parametres : $0 $1 !2 !3"

SRC_DIR=../../02_src/joomla
DEST_DIR=../../01_install/01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      windows7 <version> ?jenkins?"
            echo "                      windows7-admin <version> ?jenkins?"
            echo "                      clean"
            exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

if [ "$3" == "" ]; then
    (cd ../../../ && svn up)
    (cd ../../../wiki && svn up)
fi

VERSION=$2

case "$1" in
      "windows"|"windows-xp")
           (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl  && pdflatex documentation.tex && pdflatex documentation.tex)
           rm -Rf ../01_src/01_xampp/*
           cp ./install_script.iss ./install_script_current.iss
           sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss

           mkdir ../01_src/01_xampp/cultibox

           if [ "$1" == "windows" ]; then
               sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox-windows_{#MyAppVersion}/" ./install_script_current.iss
               tar zxvf xampp-windows-1.8.3.tar.gz -C ../01_src/01_xampp/cultibox
           else 
               sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox-windows-xp_{#MyAppVersion}/" ./install_script_current.iss
               tar zxvf xampp-windows-1.8.2.tar.gz -C ../01_src/01_xampp/cultibox
           fi

           cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/htdocs/cultibox
           cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/htdocs/cultibox/main/docs/documentation_cultibox.pdf
           cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/VERSION.txt
           cp conf-package/lgpl3.txt ../01_src/01_xampp/LICENSE.txt


           cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/apache/conf/
           cp conf-lampp/my.ini ../01_src/01_xampp/cultibox/mysql/bin/
           cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/php/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/apache/conf/extra/

           cp -R ../01_src/02_sql ../01_src/01_xampp/cultibox/sql_install
           cp conf-package/update_sql.bat ../01_src/01_xampp/cultibox/sql_install/

           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_fr.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_en.sql
           sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-noarch' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/sql_install/update_sql.sql
    
           cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/
           cp -R ../../01_install/01_src/03_sd/bin ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.ico ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/cultibox.html ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/
           cp -R ../../01_install/01_src/03_sd/cnf ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/
           cp -R ../../01_install/01_src/03_sd/logs ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/

           #For XAMPP 1.8.3: to prevent a warning
           if [ "$1" == "windows" ]; then
                sed -i "/# Change here for bind listening/i\explicit_defaults_for_timestamp=TRUE\n" ../01_src/01_xampp/cultibox/mysql/bin/my.ini
           fi

           wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_current.iss"
           rm ./install_script_current.iss
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/* 2>/dev/null
            rm  install_script_current.iss 2>/dev/null
     ;;
      *)
            usage
      ;;
esac
