#!/bin/bash

set -e 

dir=`pwd`
cd ../../../ && svn up
VERSION=1.0.`svn info | grep Revision | tr -d 'Revison: '`

if [ "`dirname $0`" == "." ]; then
    cd $dir
else
    cd `dirname $0`
fi

rm -Rf ../01_src/01_xampp/*

case "$1" in
      "ubuntu64"|"ubuntu64-admin" )
           cp debreate-package/cultibox_amd64.dbp ./cultibox_amd64_current.dbp

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_fr.sql
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

           if [ "$1" == "ubuntu64-admin" ]; then
                tar xzvfp xampp-linux-admin-1.8.1.tar.gz -C ../01_src/01_xampp/ 
           else
                tar xzvfp xampp-linux-1.8.1.tar.gz -C ../01_src/01_xampp/
           fi
           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install
           cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/emmeteur.hex ../01_src/01_xampp/lampp/htdocs/cultibox/tmp/

           cp -R daemon ../01_src/01_xampp/lampp/

           if [ "$1" == "ubuntu64-admin" ]; then
                cp conf-lampp/config.inc.php ../01_src/01_xampp/lampp/phpmyadmin/
           fi

           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && tar zcvfp cultibox-linux.tar.gz ./lampp && cd - 
           debreate ./cultibox_amd64_current.dbp

           rm ./cultibox_amd64_current.dbp
      ;;
      "ubuntu32"|"ubuntu32-admin")
           cp debreate-package/cultibox_i386.dbp ./cultibox_i386_current.dbp

           #replacement of the old version number by the new one in VERSION file
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_en.sql
           sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_fr.sql
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

           if [ "$1" == "ubuntu32-admin" ]; then
                tar xzvfp xampp-linux-admin-1.8.1.tar.gz -C ../01_src/01_xampp/
           else
                tar xzvfp xampp-linux-1.8.1.tar.gz -C ../01_src/01_xampp/
           fi

           cp -R ../../02_src/joomla ../01_src/01_xampp/lampp/htdocs/cultibox
           cp conf-lampp/httpd.conf ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/php.ini ../01_src/01_xampp/lampp/etc/
           cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/lampp/etc/extra/
           cp conf-lampp/my.cnf ../01_src/01_xampp/lampp/etc/
           cp debreate-package/takecontrol.png ../01_src/01_xampp/lampp/
           cp -R ../../01_install/01_src/03_sd ../01_src/01_xampp/lampp/sd
           cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/lampp/sql_install
           cp ../../01_install/01_src/03_sd/firm.hex ../01_src/01_xampp/lampp/htdocs/cultibox/tmp/
           cp ../../01_install/01_src/03_sd/emmeteur.hex ../01_src/01_xampp/lampp/htdocs/cultibox/tmp/
           cp -R daemon ../01_src/01_xampp/lampp/

           if [ "$1" == "ubuntu64-admin" ]; then
                cp conf-lampp/config.inc.php ../01_src/01_xampp/lampp/phpmyadmin/
           fi

           find ./../01_src/01_xampp/lampp -name ".svn"|xargs rm -Rf
           cd ./../01_src/01_xampp/ && tar zcvfp cultibox-linux.tar.gz ./lampp && cd -
           debreate ./cultibox_i386_current.dbp

           rm ./cultibox_i386_current.dbp
      ;;
      "clean")
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
