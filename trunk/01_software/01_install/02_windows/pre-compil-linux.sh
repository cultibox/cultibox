#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

echo "Parametres : $0 $1 !2 !3"

if [ "$3" == "" ]; then
    (cd ../../../ && svn up)
fi

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

VERSION=$2

case "$1" in
      "windows7"|"windows7-admin" )
            (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl  && pdflatex documentation.tex && pdflatex documentation.tex)
            rm -Rf ../01_src/01_xampp/*
            cp ./install_script.iss ./install_script_current.iss
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss

            cp -R ../01_src/02_sql ../01_src/01_xampp/
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/02_sql/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/02_sql/cultibox_en.sql

            sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-noarch' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/02_sql/update_sql.sql

            mkdir ../01_src/01_xampp/cultibox
            if [ "$1" == "windows7" ]; then
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox-windows_{#MyAppVersion}/" ./install_script_current.iss
                tar zxvf xampp-lite-windows-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox
            else
                sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=cultibox_admin-windows_{#MyAppVersion}/" ./install_script_current.iss
                tar zxvf xampp-lite-admin-windows-1.8.1.tar.gz -C ../01_src/01_xampp/cultibox
            fi
            cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/htdocs/cultibox
            cp conf-package/lgpl3.txt ../01_src/01_xampp/LICENSE.txt
            cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/htdocs/cultibox/main/docs/documentation_cultibox.pdf
            cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/VERSION.txt
            mkdir ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/cnf
            mkdir ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/bin
            mkdir ../01_src/01_xampp/cultibox/htdocs/cultibox/tmp/logs

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
