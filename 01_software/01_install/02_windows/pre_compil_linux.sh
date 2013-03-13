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

            cp -R ../01_src/02_sql ../01_src/01_xampp/
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/02_sql/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/02_sql/cultibox_en.sql

            sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-noarch' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/02_sql/update_sql.sql

            mkdir ../01_src/01_xampp/cultibox
            if [ "$1" == "windows7" ]; then
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=Cultibox_{#MyAppVersion}-windows7/" ./install_script_current.iss
                tar zxvf xamp-lite-windows-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox
            else
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=Cultibox_admin_{#MyAppVersion}-windows7/" ./install_script_current.iss
                tar zxvf xamp-lite-admin-windows-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox
            fi
            cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/htdocs/cultibox
            cat ../../CHANGELOG >> ../01_src/01_xampp/cultibox/VERSION.txt
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
