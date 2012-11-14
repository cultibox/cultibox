#!/bin/bash

set -e 
VERSION=`cat ../../VERSION`

case "$1" in
      "ubuntu64-admin" )
           dir=`dirname $0`
           cd $dir

           rm -Rf ../01_src/01_xampp/lampp
           rm -Rf ../01_src/01_xampp/lampp
   
           cp debreate-package/cultibox_amd64-admin.dbp ./cultibox_amd64-admin_current.dbp 
           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" ./cultibox_amd64-admin_current.dbp

           begin=`grep -n "<<CHANGELOG>>" ./cultibox_amd64-admin_current.dbp|awk -F ":" '{print $1}'`
           sed -i "/<<CHANGELOG>>/,/<<\/CHANGELOG>>/d" ./cultibox_amd64-admin_current.dbp
           sed -i "`echo $begin`i\<<CHANGELOG>>" ./cultibox_amd64-admin_current.dbp
           begin=`expr $begin + 1`
           sed -i "`echo $begin`i\<<DEST>>DEFAULT<</DEST>>" ./cultibox_amd64-admin_current.dbp
           begin=`expr $begin + 1`
           while read line  
           do
                sed -i "`echo $begin`i\ `echo $line`" ./cultibox_amd64-admin_current.dbp
                begin=`expr $begin + 1`
           done < ../../CHANGELOG
           sed -i "`echo $begin`i\<</CHANGELOG>>" ./cultibox_amd64-admin_current.dbp

           tar xzvf xampp-linux-lite-admin-1.7.7.tar.gz -C ../01_src/01_xampp/
           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/config.inc.php ../01_src/01_xampp/lampp/phpmyadmin/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install                 
           cp -R daemon ../01_src/01_xampp/lampp/
           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           debreate ./cultibox_amd64-admin_current.dbp

           rm ./cultibox_amd64-admin_current.dbp
      ;; 
      "ubuntu64" )
           dir=`dirname $0`
           cd $dir
    
           rm -Rf ../01_src/01_xampp/lampp
           rm -Rf ../01_src/01_xampp/lampp

           cp debreate-package/cultibox_amd64.dbp ./cultibox_amd64_current.dbp

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" ./cultibox_amd64_current.dbp

           begin=`grep -n "<<CHANGELOG>>" ./cultibox_amd64_current.dbp|awk -F ":" '{print $1}'`
           sed -i "/<<CHANGELOG>>/,/<<\/CHANGELOG>>/d" ./cultibox_amd64_current.dbp
           sed -i "`echo $begin`i\<<CHANGELOG>>" ./cultibox_amd64_current.dbp
           begin=`expr $begin + 1`
           sed -i "`echo $begin`i\<<DEST>>DEFAULT<</DEST>>" ./cultibox_amd64_current.dbp
           begin=`expr $begin + 1`
           while read line  
           do
                sed -i "`echo $begin`i\ `echo $line`" ./cultibox_amd64_current.dbp
                begin=`expr $begin + 1`
           done < ../../CHANGELOG
           sed -i "`echo $begin`i\<</CHANGELOG>>" ./cultibox_amd64_current.dbp

           tar xzvf xampp-linux-lite-1.7.7.tar.gz -C ../01_src/01_xampp/
           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install
           cp -R daemon ../01_src/01_xampp/lampp/
           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           debreate ./cultibox_amd64_current.dbp

           rm ./cultibox_amd64_current.dbp
      ;;
      "ubuntu32-admin")
           dir=`dirname $0`
           cd $dir

           rm -Rf ../01_src/01_xampp/lampp
           rm -Rf ../01_src/01_xampp/lampp

           cp debreate-package/cultibox_i386.dbp ./cultibox_i386-admin_current.dbp

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" ./cultibox_i386-admin_current.dbp

           begin=`grep -n "<<CHANGELOG>>" ./cultibox_i386-admin_current.dbp|awk -F ":" '{print $1}'`
           sed -i "/<<CHANGELOG>>/,/<<\/CHANGELOG>>/d" ./cultibox_i386-admin_current.dbp
           sed -i "`echo $begin`i\<<CHANGELOG>>" ./cultibox_i386-admin_current.dbp
           begin=`expr $begin + 1`
           sed -i "`echo $begin`i\<<DEST>>DEFAULT<</DEST>>" ./cultibox_i386-admin_current.dbp
           begin=`expr $begin + 1`
           while read line  
           do  
                sed -i "`echo $begin`i\ `echo $line`" ./cultibox_i386-admin_current.dbp 
                begin=`expr $begin + 1`
           done < ../../CHANGELOG 
           sed -i "`echo $begin`i\<</CHANGELOG>>" ./cultibox_i386-admin_current.dbp

           tar xzvf xampp-linux-lite-admin-1.7.7.tar.gz -C ../01_src/01_xampp/
           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/config.inc.php ../01_src/01_xampp/lampp/phpmyadmin/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install
           cp -R daemon ../01_src/01_xampp/lampp/
           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           debreate ./cultibox_i386-admin_current.dbp

           rm ./cultibox_i386-admin_current.dbp
      ;;
      "ubuntu32")
           dir=`dirname $0`
           cd $dir

           rm -Rf ../01_src/01_xampp/lampp
           rm -Rf ../01_src/01_xampp/lampp

           cp debreate-package/cultibox_i386.dbp ./cultibox_i386_current.dbp

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox.sql
           sed -i "s/^Version: .*-ubuntu/Version: `echo $VERSION`-ubuntu/" ./cultibox_i386_current.dbp

           begin=`grep -n "<<CHANGELOG>>" ./cultibox_i386_current.dbp|awk -F ":" '{print $1}'`
           sed -i "/<<CHANGELOG>>/,/<<\/CHANGELOG>>/d" ./cultibox_i386_current.dbp
           sed -i "`echo $begin`i\<<CHANGELOG>>" ./cultibox_i386_current.dbp
           begin=`expr $begin + 1`
           sed -i "`echo $begin`i\<<DEST>>DEFAULT<</DEST>>" ./cultibox_i386_current.dbp
           begin=`expr $begin + 1`
           while read line  
           do
                sed -i "`echo $begin`i\ `echo $line`" ./cultibox_i386_current.dbp
                begin=`expr $begin + 1`
           done < ../../CHANGELOG
           sed -i "`echo $begin`i\<</CHANGELOG>>" ./cultibox_i386_current.dbp

           tar xzvf xampp-linux-lite-1.7.7.tar.gz -C ../01_src/01_xampp/
           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install
           cp -R daemon ../01_src/01_xampp/lampp/
           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           debreate ./cultibox_i386_current.dbp

           rm ./cultibox_i386_current.dbp
      ;;
      "clean")
            rm -Rf ../01_src/01_xampp/lampp
            rm -Rf ../01_src/01_xampp/lampp
      ;;
      *)
            echo "usage: $0"
            echo "                      ubuntu64"
            echo "                      ubuntu64-admin"
            echo "                      ubuntu32"
            echo "                      ubuntu32-admin"
            echo "                      clean"
      ;;
esac
