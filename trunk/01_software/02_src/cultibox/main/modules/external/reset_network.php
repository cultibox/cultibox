<?php

exec("sudo /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces",$output,$err);
exec("sudo /sbin/ifconfig wlan0 down",$output,$err);
exec("sudo /sbin/iwconfig wlan0 mode Ad-Hoc",$output,$err);
exec("sudo /sbin/ifconfig wlan0 up",$output,$err);
exec("sudo /usr/sbin/invoke-rc.d networking force-reload",$output,$err);
exec("sudo /etc/rc.local",$output,$err);
exec("sudo /bin/mv /var/cache/lighttpd/compress/cultibox /tmp/",$output,$err);

?>
