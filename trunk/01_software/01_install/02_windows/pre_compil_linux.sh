#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir
(cd ../../../ && svn up)
SRC_DIR=../../02_src/joomla
DEST_DIR=../../01_install/01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      windows7 <version>"
            echo "                      windows7-admin <version>"
            echo "                      clean"
            exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

VERSION=$2

case "$1" in
      "windows7"|"windows7-admin" )
            
            rm -Rf ../01_src/01_xampp/*
            cp ./install_script.iss ./install_script_current.iss
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_en.sql

            sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`' WHERE \`configuration\`.\`id\` =1;/" ../01_src/02_sql/update_sql.sql

            if [ "$1" == "windows7" ]; then
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=CultiBox_{#MyAppVersion}-windows7/" ./install_script_current.iss
                tar zxvf xamp-lite-windows-1.8.1.tar.gz -C ../01_src/01_xampp/
            else
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=CultiBox_admin_{#MyAppVersion}-windows7/" ./install_script_current.iss
                tar zxvf xamp-lite-admin-windows-1.8.1.tar.gz -C ../01_src/01_xampp/
            fi
            cp -R ../../02_src/joomla ../01_src/01_xampp/htdocs/cultibox
            echo "### Don't delete this file ###" > ../01_src/01_xampp/VERSION.txt
            echo "### Ne pas supprimer ce fichier ### " >> ../01_src/01_xampp/VERSION.txt
            echo "" >> ../01_src/01_xampp/VERSION.txt
            cat ../../CHANGELOG >> ../01_src/01_xampp/VERSION.txt
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_current.iss"
            rm ./install_script_current.iss
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/* 2>/dev/null
            rm  install_script_current.iss 2>/dev/null
            rm  update_script_current_linux.iss 2>/dev/null
     ;;
      *)
            usage
      ;;
esac
