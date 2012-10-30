#!/bin/bash

set -e 
VERSION=`cat ../../VERSION`

case "$1" in
      "windows7" )
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_linux.iss
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
            tar zxvf xamp-lite-windows-1.7.7.tar.gz -C ../01_src/01_xampp/
            cp -R ../../02_src/joomla ../01_src/01_xampp/htdocs/cultibox
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_linux.iss"
      ;;
      "windows7-admin" )
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_linux.iss
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
            tar zxvf xamp-lite-windows-admin-1.7.7.tar.gz -C ../01_src/01_xampp/
            cp -R ../../02_src/joomla ../01_src/01_xampp/htdocs/cultibox
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_linux.iss"
      ;;
      "clean")
            sudo rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            echo "usage: $0"
            echo "                      windows7"
            echo "                      windows7-admin"
            echo "                      clean"
      ;;
esac
