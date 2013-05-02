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

cat << EOF
<h1><span style="font-family: arial, helvetica, sans-serif;">Cultibox App</span></h1>
<p style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Le logic<span style="font-family: arial, helvetica, sans-serif;">iel pour la gestion du clim</span>at de vos cultures.&nbsp;<br />Il est proposé&nbsp;<em style="font-weight: bold; font-style: normal;">gratuitement</em>&nbsp;afin de vous permettre de juger par vous-même sa simplicité d'utilisation.</p>
<ul>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Prise en main facile</li>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Programme de culture précis et intuitif</li>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Visualisation des relevés climatiques sur des courbes claires</li>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Compatible Windows (XP minimum), Linux Ubuntu et Mac Os X (Snow Leopard minimum)</li>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Version `echo $version`</li>
</ul>
<br />

<p style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;"> Téléchargement du logiciel:
<ul>
<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Windows 32-bits et 64-bits: <a href="download/software/cultibox-windows_`echo $version`.exe" title="Logiciel `echo $version`"><span style="font-family: arial, helvetica, sans-serif;">Téléchargement Windows Version `echo $version`</span></a><br />md5sum: `echo $md5win`</p>
<p><span style="font-family: arial, helvetica, sans-serif;"></span></li>

<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Linux Ubuntu 64bits: <a href="download/software/cultibox-ubuntu-amd64_`echo $version`.deb" title="Logiciel `echo $version`"><span style="font-family: arial, helvetica, sans-serif;">Téléchargement Linux Version `echo $version` 64bits</span></a><br />md5sum: `echo $md5ubuntu64`</p>
<p><span style="font-family: arial, helvetica, sans-serif;"></span></li>

<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Linux Ubuntu 32bits: <a href="download/software/cultibox-ubuntu-i386_`echo $version`.deb" title="Logiciel `echo $version`"><span style="font-family: arial, helvetica, sans-serif;">Téléchargement Linux Version `echo $version` 32bits</span></a><br />md5sum: `echo $md5ubuntu32`</p>
<p><span style="font-family: arial, helvetica, sans-serif;"></span></li>

<li style="font-family: Arial, Helvetica, sans-serif; font-size: small; color: #151515; line-height: normal; text-align: -webkit-auto;">Mac os X Snow Leopard:  <a href="download/software/cultibox-macosx_`echo $version`.pkg" title="Logiciel `echo $version`"><span style="font-family: arial, helvetica, sans-serif;">Téléchargement Mac Os X Snow Leopard Version `echo $version`</span></a><br />md5sum: `echo $md5mac`</p>
<p><span style="font-family: arial, helvetica, sans-serif;"></span></li>
</ul>
</p>
EOF

    set +x
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
        exit 0
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
