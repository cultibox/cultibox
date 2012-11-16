#!/bin/bash

set -e 
dir=`dirname $0`
cd $dir
(cd ../../../ && svn up)
VERSION=1.0.`svn info | grep Revision | tr -d 'Revison: '`
SRC_DIR=../../02_src/joomla
DEST_DIR=../../01_install/01_src/01_xampp


case "$1" in
      "windows7" )
            sudo rm -Rf ../01_src/01_xampp/*
            cp ./install_script.iss ./install_script_current.iss
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss
            sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=CultiBox_{#MyAppVersion}-windows7/" ./install_script_current.iss
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_en.sql
            tar zxvf xamp-lite-windows-1.7.7.tar.gz -C ../01_src/01_xampp/
            cp -R ../../02_src/joomla ../01_src/01_xampp/htdocs/cultibox
            echo "### Don't delete this file ###" > ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            echo "### Ne pas supprimer ce fichier ### " >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            echo "" >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            cat ../../CHANGELOG >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_current.iss"
            rm ./install_script_current.iss
      ;;
      "windows7-admin" )
            sudo rm -Rf ../01_src/01_xampp/*
            cp ./install_script.iss ./install_script_current.iss
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $VERSION`\"/" ./install_script_current.iss
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_fr.sql
            sed -i "s/'[0-9]\+\.[0-9]\+\.[0-9]\+'/'`echo $VERSION`'/" ../../01_install/01_src/02_sql/cultibox_en.sql
            sed -i "s/OutputBaseFilename=.*/OutputBaseFilename=CultiBox_admin_{#MyAppVersion}-windows7/" ./install_script_current.iss 
            tar zxvf xamp-lite-admin-windows-1.7.7.tar.gz -C ../01_src/01_xampp/
            cp -R ../../02_src/joomla ../01_src/01_xampp/htdocs/cultibox
            echo "### Don't delete this file ###" > ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            echo "### Ne pas supprimer ce fichier ### " >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            echo "" >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            cat ../../CHANGELOG >> ../01_src/01_xampp/VERSION_`echo $VERSION`.txt
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "install_script_current.iss"
            rm ./install_script_current.iss
      ;;
      "update" )
            if [ "$2" == "" ] || [ "$3" == "" ]; then
                    $0 
                    exit 0
            fi
            
            sudo rm -Rf ../01_src/01_xampp/*
            cp ./update_script_linux.iss ./update_script_current_linux.iss
            sed -i "s/#define MyAppVersion .*/#define MyAppVersion \"`echo $3`\"/" ./update_script_current_linux.iss
            sed -i "s/#define MyOldAppVersion .*/#define MyOldAppVersion \"`echo $2`\"/" ./update_script_current_linux.iss

            file_section=`grep -n "\[Files\]" ./update_script_current_linux.iss |awk -F":" '{print $1}'`
            run_section=`grep -n "\[Run\]" ./update_script_current_linux.iss |awk -F":" '{print $1}'`
            
            if [ "$file_section" == "" ] || [ "$run_section" == "" ]; then
                    echo "[ Error ] - Please check the update script (update_script_linux.iss): missing [Files] or [Run] section(s)"
                    exit 0
            fi

            file_section=`expr $file_section + 8`
            run_section=`expr $run_section - 1`


            if [ $run_section -gt $file_section ]; then
                sed -i "${file_section},${run_section}d" ./update_script_current_linux.iss
            fi
 
            mkdir -p $DEST_DIR/htdocs/cultibox
            copy_nb=0
            while read line; do
                    copy=`echo $line|awk -F":" '{print $1}'`
                    file=`echo $line|awk -F":" '{print $2}'`
                    dir_file=`dirname $file`

                    if [ "$copy" == "SRC" ]; then

                        bdir=`dirname $file`
                        mkdir -p $DEST_DIR/htdocs/cultibox/$bdir
                        cp $SRC_DIR/$file  $DEST_DIR/htdocs/cultibox/$bdir/
                        copy_nb=`expr $copy_nb + 1`

                        tmp_file=`echo "$file" | sed -e 's/.*/\/&/'` 
                        line_toadd=`echo "$tmp_file" | sed "s#/#\\\\\\\\\\\\\#g"`
                        tmp_dir=`echo "$dir_file" | sed -e 's/.*/\/&/'`
                        dir_toadd=`echo "$tmp_dir" | sed "s#/#\\\\\\\\\\\\\#g"`
                        sed -i "${file_section}i\Source: \"C:\\\users\\\yann\\\Desktop\\\Project\\\cultibox\\\01_software\\\02_src\\\joomla${line_toadd}\"; DestDir: \"{app}\\\xampp\\\htdocs\\\cultibox${dir_toadd}\\\\\"; CopyMode: alwaysoverwrite; Flags: ignoreversion" ./update_script_current_linux.iss
                        file_section=`expr $file_section + 1`

                        

                    fi
            done < ./update_file/update_windows_from_$2_to_$3

            echo "SRC files: $copy_nb";

            if [ ! -f ../01_src/02_sql/sql_update/update_sql_windows_from_$2_to_$3.sql ]; then
                touch ../01_src/01_xampp/update_sql_windows_from_$2_to_$3.sql
            else
                cp ../01_src/02_sql/sql_update/update_sql_windows_from_$2_to_$3.sql ../01_src/01_xampp/
            fi

            echo "### Don't delete this file ###" > ../01_src/01_xampp/VERSION_`echo $3`.txt
            echo "### Ne pas supprimer ce fichier ### " >> ../01_src/01_xampp/VERSION_`echo $3`.txt
            echo "" >> ../01_src/01_xampp/VERSION_`echo $3`.txt
            cat ../../CHANGELOG >> ../01_src/01_xampp/VERSION_`echo $3`.txt

            sed -i "${file_section}i\Source: \"C:\\\users\\\yann\\\Desktop\\\Project\\\cultibox\\\01_software\\\01_install\\\01_src\\\01_xampp\\\VERSION_${3}.txt\"; DestDir: \"{app}\\\xampp\"; Flags: ignoreversion" ./update_script_current_linux.iss
            echo "UPDATE \`configuration\` SET \`VERSION\` = '${3}' WHERE \`configuration\`.\`id\` =1;" >> ../01_src/01_xampp/update_sql_windows_from_$2_to_$3.sql
            wine "C:\Program Files (x86)\Inno Setup 5\iscc.exe"  "update_script_current_linux.iss"
      ;;
      "clean")
            sudo rm -Rf ../01_src/01_xampp/*
      ;;
      *)
            echo "usage: $0"
            echo "                      windows7"
            echo "                      windows7-admin"
            echo "                      update <old_version> <new_version>"
            echo "                      clean"
      ;;
esac
