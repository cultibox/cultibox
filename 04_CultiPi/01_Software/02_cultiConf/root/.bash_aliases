alias wget_php='wget -O - -q'
alias cpilog='tail -f /var/log/cultipi/cultipi.log'
alias cpislog='tail -f /var/log/cultipi/cultipi-service.log'
alias cpireload='/etc/init.d/cultipi force-reload'
alias cpimysql='mysql -u cultibox -pcultibox cultibox '
alias cpiupdate='bash -x /etc/cron.daily/cultipi now'

function rsensor { 
    tclsh /opt/cultipi/cultiPi/get.tcl serverAcqSensor localhost "::sensor($1,value)"
}

function rplug {
    tclsh /opt/cultipi/cultiPi/get.tcl serverPlugUpdate localhost "::plug($1,value)"
}

function splug {
    tclsh /opt/cultipi/cultiPi/set.tcl serverPlugUpdate localhost $*
}

