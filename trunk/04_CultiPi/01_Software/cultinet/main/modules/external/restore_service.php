<?php


if(is_file("/etc/network/interfaces.SAVE")) {
    exec("sudo /bin/mv /etc/network/interfaces.SAVE /etc/network/interfaces");
    exec("sudo /etc/init.d/networking restart");
    sleep(3);
}

?>
