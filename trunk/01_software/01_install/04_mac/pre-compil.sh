#!/bin/bash

set -e 
case "$1" in
      "snow-leopard" )
           dir=`dirname $0`
           cd $dir
           tar pzxvf xampp-mac-lite-1.7.7.tar.gz -C snow-leopard/
           cp -R ../../02_src/joomla snow-leopard/XAMPP/htdocs/cultibox
           #cp conf-lampp/config.inc.php snow-leopard/XAMPP/xamppfiles/phpmyadmin/
           cp conf-lampp/httpd.conf snow-leopard/XAMPP/etc/
           cp conf-lampp/php.ini snow-leopard/XAMPP/etc/
           cp packagemaker/takecontrol.png snow-leopard/XAMPP/
           cp -R ../../01_install/01_src/03_sd snow-leopard/XAMPP/sd
           cp -R ../../01_install/01_src/02_sql snow-leopard/XAMPP/sql_install                 
           find ./snow-leopard/XAMPP/ -name ".svn"|xargs rm -Rf
           cd snow-leopard && tar pzcvf cultibox.mac.tar.gz XAMPP
      ;; 
      "clean")
            rm -Rf snow-leopard/XAMPP

      ;;
      *)
            echo "usage: $0"
            echo "                      snow-leopard"
            echo "                      clean"
      ;;
esac
