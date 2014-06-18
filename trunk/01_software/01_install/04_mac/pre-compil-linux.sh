#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir

SRC_DIR=../../02_src/joomla
DEST_DIR=../01_src/01_xampp

function usage {
            echo "usage: $0"
            echo "                      osx <version> ?jenkins?"
            echo "                      clean"
            exit 1
}


if [ "$2" == "" ] && [ "$1" != "clean" ]; then
    usage
fi

# Remove svn up when using jenkins
if [ "$3" == "" ]; then
    (cd ../../../ && svn up)
fi

VERSION=$2
WORK_DIR=/Users/yann/Desktop
SERVER=macosx

case "$1" in
      "osx" )
            (cd ../../../02_documentation/02_userdoc/ && tclsh ./parse_wiki.tcl && pdflatex documentation.tex && pdflatex documentation.tex)
            rm -Rf ../01_src/01_xampp/*
            tar zxvfp xampp-mac-1.8.3.tar.gz -C ../01_src/01_xampp/
            mv ../01_src/01_xampp/XAMPP ../01_src/01_xampp/cultibox
            cp -R ../../02_src/joomla ../01_src/01_xampp/cultibox/xamppfiles/htdocs/cultibox
            cp ../../../02_documentation/02_userdoc/documentation.pdf ../01_src/01_xampp/cultibox/xamppfiles/htdocs/cultibox/main/docs/documentation_cultibox.pdf

            cp conf-lampp/httpd.conf ../01_src/01_xampp/cultibox/xamppfiles/etc/
            cp conf-lampp/php.ini ../01_src/01_xampp/cultibox/xamppfiles/etc/
            cp conf-lampp/httpd-xampp.conf ../01_src/01_xampp/cultibox/xamppfiles/etc/extra/
            cp conf-lampp/my.cnf ../01_src/01_xampp/cultibox/xamppfiles/etc/

  cat > ../01_src/01_xampp/cultibox/xamppfiles/etc/my-extra.cnf << "EOF" 
[client]
user="root"
password="cultibox"
EOF

            cp -R ../../01_install/01_src/02_sql ../01_src/01_xampp/cultibox/sql_install
            mkdir ../01_src/01_xampp/cultibox/package
            cp conf-package/takecontrol.png ../01_src/01_xampp/cultibox/package
            cp conf-package/lgpl3.txt ../01_src/01_xampp/cultibox/LICENSE.txt
            cp conf-package/post* ../01_src/01_xampp/cultibox/package/
            cp conf-package/pre* ../01_src/01_xampp/cultibox/package/
            cp conf-package/VolumeCheck ../01_src/01_xampp/cultibox/package/
            cp conf-package/InstallationCheck ../01_src/01_xampp/cultibox/package/
            cp conf-package/cultibox_mysql.plist  ../01_src/01_xampp/cultibox/package/
            cp conf-package/cultibox_apache.plist  ../01_src/01_xampp/cultibox/package/
            cp conf-package/uninstall  ../01_src/01_xampp/cultibox/package/
            cp -R conf-package/cultibox.app ../01_src/01_xampp/cultibox/
            cat ../../CHANGELOG > ../01_src/01_xampp/cultibox/VERSION.txt
            cat ../../01_install/01_src/03_sd/version.txt > ../01_src/01_xampp/cultibox/VERSION_FIRM.txt

            mkdir ../01_src/01_xampp/cultibox/run
            cp conf-script/* ../01_src/01_xampp/cultibox/run/
            cp -R ../../01_install/01_src/03_sd/* ../01_src/01_xampp/cultibox/xamppfiles/htdocs/cultibox/tmp/

            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_en.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_de.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_it.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`-noarch'/" ../01_src/01_xampp/cultibox/sql_install/cultibox_es.sql
            sed -i "s/\`VERSION\` = '.*/\`VERSION\` = '`echo $VERSION`-noarch' WHERE \`configuration\`.\`id\` =1;/" ../01_src/01_xampp/cultibox/sql_install/update_sql.sql
            sed -i "s/public \$host = '127.0.0.1';/public \$host = 'localhost';/g" ../01_src/01_xampp/cultibox/xamppfiles/htdocs/cultibox/configuration.php

            find ../01_src/01_xampp/cultibox/ -name ".svn"|xargs rm -Rf
            set +e
            ssh root@$SERVER "if [ -d /Applications/cultibox ]; then rm -Rf /Applications/cultibox; fi"
            rsync -av ../01_src/01_xampp/cultibox root@$SERVER:/Applications/
            ssh root@$SERVER "chown -R root:wheel /Applications/cultibox"
            ssh root@$SERVER "if [ -d $WORK_DIR/cultibox.pmdoc ]; then rm -Rf $WORK_DIR/cultibox.pmdoc ; fi"

            cp -R cultibox.pmdoc ../01_src/01_xampp/
            sed -i "s#<version>.*</version>#<version>`echo $VERSION`</version>#" ../01_src/01_xampp/cultibox.pmdoc/01cultibox.xml
            sed -i "s#<build>.*</build>#<build>`echo $WORK_DIR`/cultibox-macosx_`echo $VERSION`.pkg</build#" ../01_src/01_xampp/cultibox.pmdoc/index.xml
            ssh root@$SERVER "if [ -d $WORK_DIR/cultibox.pmdoc ]; then rm -Rf $WORK_DIR/cultibox.pmdoc; fi"
            rsync -av ../01_src/01_xampp/cultibox.pmdoc root@$SERVER:$WORK_DIR/
            exit 0
            ssh root@$SERVER "cd $WORK_DIR && /usr/bin/packagemaker --title cultibox -o cultibox-macosx_$VERSION.pkg --doc cultibox.pmdoc -v"
            scp root@$SERVER:$WORK_DIR/cultibox-macosx_$2.pkg ./Output/
            set -e
      ;; 
      "clean")
            rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            usage
      ;;
esac
