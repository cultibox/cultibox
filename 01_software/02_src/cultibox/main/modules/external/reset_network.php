<?php

exec("sudo /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces",$output,$err);
exec("sudo /sbin/shutdown -r now",$output,$err);
sleep(5);

?>
