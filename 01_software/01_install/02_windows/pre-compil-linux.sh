#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

echo "Parametres : $0 $1 $2 $3"

SRC_DIR=../../02_src/joomla
DEST_DIR=../../01_install/01_src/01_xampp

function usage {
    echo "usage: $0"
    echo "                      windows|windows-xp <version> ?jenkins|byWindows?"
    echo "                      clean"
    exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi


(cd ../../../ && git pull && cd 01_software/02_src/cultibox/main/cultibox.wiki/ && git pull && cd ../../../../../04_CultiPi/01_Software/01_cultiPi/ && git pull)
if [ "$3" == "byWindows" ] ; then
    (cd ../../../wiki && git pull)
fi

echo "Remove previous files"
if [ -f ./install_script_current.iss ]; then
   rm -Rf ./install_script_current.iss
fi
rm -Rf ../01_src/01_xampp/*


VERSION=$2

case "$1" in
    "windows"|"windows-xp")
        echo "Documentation generation"
        (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl  && pdflatex documentation.tex && pdflatex documentation.tex)

        if [ "$1" == "windows-xp" ]; then
            cp ./install_script_xp.iss ./install_script_current.iss
        else
            cp ./install_script.iss ./install_script_current.iss
        fi
            
        sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss

        mkdir ../01_src/01_xampp/cultibox

        echo "Extraction de xampp"
        if [ "$1" == "windows" ]; then
           sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox-windows_{#MyAppVersion}/" ./install_script_current.iss
           tar zxvf xampp-windows-1.8.3.tar.gz -C ../01_src/01_xampp/cultibox
        else 
           sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox-windows-xp_{#MyAppVersion}/" ./install_script_current.iss
           tar zxvf xampp-windows-1.8.2.tar.gz -C ../01_src/01_xampp/cultibox
        fi

        echo "Copie des fichiers"
        cp -R ../../02_src/cultibox ../01_src/01_xampp/cultibox/htdocs/cultibox
        # On windows plateform, wee need to change right....
        if [ "$3" == "byWindows" ]; then
            chmod -R 777 ../01_src/01_xampp/cultibox
        fi
        mkdir -p         cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/htdocs/cultibox/main/docs
        cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/htdocs/cultibox/main/docs/documentation_cultibox.pdf
        cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/VERSION.txt
        cat ../../01_install/01_src/03_sd/version.txt > ../01_src/01_xampp/cultibox/VERSION_FIRM.txt
        cp conf-package/lgpl3.txt ../01_src/01_xampp/LICENSE.txt

        cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/apache/conf/
        cp conf-lampp/my.ini ../01_src/01_xampp/cultibox/mysql/bin/
        cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/php/
        cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/apache/conf/extra/
        cp conf-lampp/adminer-4.1.0.php  ../01_src/01_xampp/cultibox/htdocs/

        cp -R ../01_src/02_sql ../01_src/01_xampp/cultibox/sql_install
        cp conf-package/update_sql.bat ../01_src/01_xampp/cultibox/sql_install/

        # On windows plateform, wee need to change right....
        if [ "$3" == "byWindows" ]; then
            chmod -R 777 ../01_src/01_xampp/cultibox
        fi

        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_fr.sql
        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_en.sql
        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_de.sql
        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_it.sql
        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_es.sql

        sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-noarch' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/sql_install/update_sql.sql
        sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9][0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/htdocs/cultibox/main/libs/lib_configuration.php


        cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/

        #For XAMPP 1.8.3: to prevent a warning
        if [ "$1" == "windows" ]; then
            sed -i "/# Change here for bind listening/i\explicit_defaults_for_timestamp=TRUE\n" ../01_src/01_xampp/cultibox/mysql/bin/my.ini
        fi

        # On windows plateform, wee need to change right....
        if [ "$3" == "byWindows" ]; then
            chmod -R 777 ../01_src/01_xampp/cultibox
        fi

        if [ "$3" != "byWindows" ]; then
            wine "C:\Program Files (x86)\Inno Setup 5\ISCC.exe"  "install_script_current.iss"
            rm ./install_script_current.iss
        fi
           

    ;;
    "clean")
        rm -Rf ../01_src/01_xampp/* 2>/dev/null
        rm  install_script_current.iss 2>/dev/null
    ;;
    *)
        usage
    ;;
esac
