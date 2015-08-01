alias cpilog='tail -f /var/log/cultipi/cultipi.log'
alias cpislog='tail -f /var/log/cultipi/cultipi-service.log'
alias cpireload='/etc/init.d/cultipi force-reload'
alias cpimysql='mysql -u cultibox -pcultibox cultibox '
alias cpiupdate='bash -x /etc/cron.daily/cultipi --now --manual'

function cpiwget {
    echo "password:"
    read PASSWORD
    wget --user=root --password=$PASSWORD -O -
}

function cpisynconf {
    echo "password:"
    read PASSWORD
    wget --password=$PASSWORD --user=root http://localhost/cultibox/main/modules/external/sync_conf.php -O -
}

function cpicreateconf {
    echo "password:"
    read PASSWORD
    wget --password=$PASSWORD --user=root http://localhost/cultibox/main/modules/external/check_and_update_sd.php?sd_card=/etc/cultipi/conf_tmp -O -
}

function cpirsensor { 
    tclsh /opt/cultipi/cultiPi/get.tcl serverAcqSensor localhost "::sensor($1,value)"
}

function cpirplug {
    tclsh /opt/cultipi/cultiPi/get.tcl serverPlugUpdate localhost "::plug($1,value)"
}

function cpisplug {
    tclsh /opt/cultipi/cultiPi/set.tcl serverPlugUpdate localhost $*
}

function cpiversion {
    for PACKAGE in cultipi cultibox cultiraz cultitime cultidoc culticam culticonf
    do
        version=$(dpkg -s $PACKAGE 2>/dev/null|grep Version)
        echo "$PACKAGE : $version"
    done
}
