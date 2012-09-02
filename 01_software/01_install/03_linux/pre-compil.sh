#!/bin/bash

set -e 
case "$1" in
      "ubuntu-precise64" )
           dir=`dirname $0`
           cd $dir
           tar xzvf xampp-linux-lite-1.7.7.tar.gz -C ubuntu-precise64/
           rm ubuntu-precise64/lampp/share/man
           rm ubuntu-precise64/lampp/share/openssl/man
           rm ubuntu-precise64/lampp/lib/terminfo
           cp -R ../../02_src/joomla ubuntu-precise64/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ubuntu-precise64/lampp/etc/
           cp conf-lampp/php.ini ubuntu-precise64/lampp/etc/
           cp conf-lampp/config.inc.php ubuntu-precise64/lampp/phpmyadmin/
           cp conf-lampp/httpd-xampp.conf ubuntu-precise64/lampp/etc/extra/
           cp debreate-package/takecontrol.png ubuntu-precise64/lampp/
           cp -R ../../01_install/01_src/03_sd ubuntu-precise64/lampp/sd
           cp -R ../../01_install/01_src/02_sql ubuntu-precise64/lampp/sql_install                 
           cp -R daemon ubuntu-precise64/lampp/
           find ./ubuntu-precise64/lampp -name ".svn"|xargs rm -Rf
      ;; 
      "clean")
            rm -Rf ubuntu-precise64/lampp

      ;;
      *)
            echo "usage: $0"
            echo "                      ubuntu-precise64"
            echo "                      clean"
      ;;
esac
