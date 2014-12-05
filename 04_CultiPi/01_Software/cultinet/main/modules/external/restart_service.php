<?php


$iface_eth0=array();
$iface_wlan0=array();

if(is_file("/tmp/interfaces")) {
    exec("sudo /bin/cp /etc/network/interfaces /etc/network/interfaces.SAVE");
    exec("sudo /bin/mv /tmp/interfaces /etc/network/interfaces");
    exec("sudo /bin/chmod 644 /etc/network/interfaces*");
    exec("sudo /sbin/ifup -a --no-act >/dev/null 2>&1 ; echo \"$?\"",$output,$err);
    if((count($output)==1)&&(strcmp($output[0],"0")==0)) {
        exec("sudo /etc/init.d/networking restart");
        sleep(3);
        exec("ip addr show eth0 | awk '/inet/ {print $2}' | cut -d/ -f1",$iface_eth0,$err);
        exec("ip addr show wlan0 | awk '/inet/ {print $2}' | cut -d/ -f1",$iface_wlan0,$err);
    } else {
        exec("sudo /bin/mv /etc/network/interfaces.SAVE /etc/network/interfaces");
    }
}

echo json_encode($iface_eth0);
echo json_encode($iface_wlan0);

?>
