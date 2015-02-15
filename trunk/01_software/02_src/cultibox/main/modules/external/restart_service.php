<?php


$iface=array();

if(is_file("/tmp/interfaces")) {
    exec("sudo /bin/cp /etc/network/interfaces /etc/network/interfaces.SAVE");
    exec("sudo /bin/mv /tmp/interfaces /etc/network/interfaces");
    exec("sudo /bin/chmod 644 /etc/network/interfaces*");
    exec("sudo /sbin/ifup -a --no-act >/dev/null 2>&1 ; echo \"$?\"",$output,$err);
    if((count($output)==1)&&(strcmp($output[0],"0")==0)) {
        exec("sudo /etc/init.d/isc-dhcp-server stop",$output,$err);
        exec("sudo /etc/init.d/dnsmasq stop",$output,$err);
        exec("sudo /sbin/iptables -t nat --delete PREROUTING  1",$output,$err);
        sleep(2);
        exec("sudo /sbin/ifconfig wlan0 down",$output,$err);
        exec("sudo /sbin/iwconfig wlan0 mode Managed",$output,$err);
        exec("sudo /sbin/ifconfig wlan0 up",$output,$err);
        exec("sudo /usr/sbin/invoke-rc.d networking force-reload",$output,$err);
        echo json_encode("1");
    } else {
        exec("sudo /bin/mv /etc/network/interfaces.SAVE /etc/network/interfaces");
        echo json_encode("0");
    }
} else {
    echo json_encode("0");
}

?>
