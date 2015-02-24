<?php

exec("sudo /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces",$output,$err);
exec("sudo /sbin/modprobe -r rt2800usb",$output,$err);
sleep(2);
exec("sudo /sbin/modprobe rt2800usb",$output,$err);
sleep(7);
exec("sudo /etc/rc.local",$output,$err);
exec("sudo /bin/mv /var/cache/lighttpd/compress/cultibox /tmp/",$output,$err);

?>
