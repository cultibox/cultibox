<?php 

    $tmp_plg_conf="/etc/cultipi/conf_tmp/cnf/plg";
    $current_conf="/etc/cultipi/01_defaultConf_RPi/serverPlugUpdate/";

    $tmp_prg_conf="/etc/cultipi/conf_tmp/cnf/prg";

    exec("sudo cp -R $tmp_plg_conf $current_conf",$ret,$err);

    if($err!=0) {
        echo json_encode($err);
    } else {
        exec("sudo cp -R $tmp_prg_conf $current_conf",$ret,$err);
        echo json_encode($err);
    }

    exec("sudo chown -R cultipi:cultipi $current_conf",$ret,$err);

    //Restart service:
    exec("sudo /etc/init.d/cultipi force-reload >/dev/null",$ret,$err);
?>
