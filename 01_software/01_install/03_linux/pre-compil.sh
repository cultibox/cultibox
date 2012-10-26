#!/bin/bash

set -e 
VERSION=`cat ../../VERSION`

case "$1" in
      "ubuntu64" )
           dir=`dirname $0`
           cd $dir
    
           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" debreate-package/cultibox_amd64.dbp
           sed -i "s/Version=.*/Version=`echo $VERSION`/" debreate-package/cultibox_amd64.dbp 

           tar xzvf xampp-linux-lite-1.7.7.tar.gz -C ubuntu-precise64/
           cp -R ../../02_src/joomla ubuntu-precise64/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ubuntu-precise64/lampp/etc/
           cp conf-lampp/php.ini ubuntu-precise64/lampp/etc/
           cp conf-lampp/config.inc.php ubuntu-precise64/lampp/phpmyadmin/
           cp conf-lampp/httpd-xampp.conf ubuntu-precise64/lampp/etc/extra/
           cp conf-lampp/my.cnf ubuntu-precise64/lampp/etc/
           cp debreate-package/takecontrol.png ubuntu-precise64/lampp/
           cp -R ../../01_install/01_src/03_sd ubuntu-precise64/lampp/sd
           cp -R ../../01_install/01_src/02_sql ubuntu-precise64/lampp/sql_install                 
           cp -R daemon ubuntu-precise64/lampp/
           find ./ubuntu-precise64/lampp -name ".svn"|xargs rm -Rf
           debreate debreate-package/cultibox_amd64.dbp
      ;; 
      "ubuntu32")
           dir=`dirname $0`
           cd $dir

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" debreate-package/cultibox_i386.dbp
           sed -i "s/Version=.*/Version=`echo $VERSION`/" debreate-package/cultibox_i386.dbp 

           tar xzvf xampp-linux-lite-1.7.7.tar.gz -C ubuntu-precise32/
           cp -R ../../02_src/joomla ubuntu-precise32/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ubuntu-precise32/lampp/etc/
           cp conf-lampp/php.ini ubuntu-precise32/lampp/etc/
           cp conf-lampp/config.inc.php ubuntu-precise32/lampp/phpmyadmin/
           cp conf-lampp/httpd-xampp.conf ubuntu-precise32/lampp/etc/extra/
           cp conf-lampp/my.cnf ubuntu-precise32/lampp/etc/
           cp debreate-package/takecontrol.png ubuntu-precise32/lampp/
           cp -R ../../01_install/01_src/03_sd ubuntu-precise32/lampp/sd
           cp -R ../../01_install/01_src/02_sql ubuntu-precise32/lampp/sql_install
           cp -R daemon ubuntu-precise32/lampp/
           find ./ubuntu-precise32/lampp -name ".svn"|xargs rm -Rf
           debreate debreate-package/cultibox_i386.dbp
      ;;
      
      "clean")
            rm -Rf ubuntu-precise64/lampp
            rm -Rf ubuntu-precise32/lampp
      ;;
      *)
            echo "usage: $0"
            echo "                      ubuntu-precise64"
            echo "                      ubuntu-precise32"
            echo "                      clean"
      ;;
esac
