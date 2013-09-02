#!/bin/bash

set -e

usage() {
cat<<EOF >&2
Usage: 
  $myname --action=<action> --version=<version> [--md5sum] [--website]


Note:
    "action" and "version" are mandatory
    version must be formatted like following: x.x.x (ex: 1.8.9)
    action could be: 'upload','make','makeupload'
    md5sum print md5sum package
    website print html code to be copied into the cultibox website
EOF
}


md5sum_package() {
    md5sum 01_install/03_linux/Output/cultibox-ubuntu-i386_$version.deb
    md5sum 01_install/03_linux/Output/cultibox-ubuntu-amd64_$version.deb
    md5sum 01_install/02_windows/Output/cultibox-windows_$version.exe
    md5sum 01_install/04_mac/Output/cultibox-macosx_$version.pkg
}


website_infos() {
    set -e
    version=$1
    md5win=`md5sum 01_install/02_windows/Output/cultibox-windows_$version.exe|awk -F" " '{print $1}'`
    md5ubuntu64=`md5sum 01_install/03_linux/Output/cultibox-ubuntu-amd64_$version.deb|awk -F" " '{print $1}'`
    md5ubuntu32=`md5sum 01_install/03_linux/Output/cultibox-ubuntu-i386_$version.deb|awk -F" " '{print $1}'`
    md5mac=`md5sum 01_install/04_mac/Output/cultibox-macosx_$version.pkg|awk -F" " '{print $1}'`
    html=`sed -e "s/XX\.XX\.XX/$version/g" ../04_website/telechargement.html`

    html=`echo -n "$html" | sed -e "s/MD5SUM_WIN/$md5win/"`
    html=`echo -n "$html" | sed -e "s/MD5SUM_LINUX32/$md5ubuntu32/"`
    html=`echo -n "$html" | sed -e "s/MD5SUM_LINUX64/$md5ubuntu64/"`
    html=`echo -n "$html" | sed -e "s/MD5SUM_MAC/$md5mac/"`

    echo -n "$html"
    echo ""
}



server="<server address>"
user="<user login>"
password="<user password>"
myname=`basename $0`

preargs=""
while [ $# -gt 0 ]; do
    case $1 in
        --*) preargs="$preargs $1"; shift;;
        *) break;;
    esac
done

arguments=`getopt -n $myname -o a,v -l action:,version:,md5sum,website -- $preargs`
[ $? = 0 ] || usage
eval set -- "$arguments"

while [ $# -gt 0 ]; do

                case $1 in
                    --version) shift; version=$1; shift;;
                    --action) shift; action=$1; shift;;
                    --md5sum) md5sum="true"; shift;;
                    --website) website="true"; shift;;
                    *) shift;;
                esac
done

if [ "$version" != "" ]; then
    if [ "$action" == "make" ] || [ "$action" == "makeupload" ]; then
        cd 01_install/02_windows/ && ./pre-compil-linux.sh windows7 $version
        cd ../03_linux/
    
        ./pre-compil-linux.sh ubuntu64 $version
        ./pre-compil-linux.sh ubuntu32 $version
        ./pre-compil-linux.sh ubuntu64-admin $version
        ./pre-compil-linux.sh ubuntu32-admin $version

        cd ../04_mac/
        ./pre-compil-linux.sh snow-leopard $version

        cd ../../

        if [ "$md5sum" == "true" ]; then
            md5sum_package
        fi
    fi

    if [ "$action" == "upload" ] || [ "$action" == "makeupload" ]; then
        lftp -c "open $server; user $user $password; cd download/software/version/; put CHANGELOG; bye"
        lftp -c "open $server; user $user $password; cd download/software/; put 01_install/02_windows/Output/cultibox-windows_$version.exe; bye"
        lftp -c "open $server; user $user $password; cd download/software/; put 01_install/03_linux/Output/cultibox-ubuntu-amd64_$version.deb; bye"
        lftp -c "open $server; user $user $password; cd download/software/; put 01_install/03_linux/Output/cultibox-ubuntu-i386_$version.deb; bye"
        lftp -c "open $server; user $user $password; cd download/software/; put 01_install/04_mac/Output/cultibox-macosx_$version.pkg; bye"

        if [ "$md5sum" == "true" ]; then
            md5sum_package
        fi
    fi

    if [ "$md5sum" == "true" ]; then
        md5sum_package
    fi

    if [ "$website" == "true" ]; then
        website_infos $version
    fi
    exit 0
fi
usage
