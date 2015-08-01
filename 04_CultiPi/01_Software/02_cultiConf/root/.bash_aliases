alias cpiwget='wget --password=cultipi --user=cultipi -O - -q'
alias cpisynconf='wget --password=cultipi --user=cultipi http://localhost/cultibox/main/modules/external/sync_conf.php'
alias cpicreateconf='wget --password=cultipi --user=cultipi http://localhost/cultibox/main/modules/external/check_and_update_sd.php?sd_card=/etc/cultipi/conf_tmp'
alias cpilog='tail -f /var/log/cultipi/cultipi.log'
alias cpislog='tail -f /var/log/cultipi/cultipi-service.log'
alias cpireload='/etc/init.d/cultipi force-reload'
alias cpimysql='mysql -u cultibox -pcultibox cultibox '
alias cpiupdate='bash -x /etc/cron.daily/cultipi --now'

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
