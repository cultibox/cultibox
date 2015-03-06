<?php 

    require_once('../../libs/config.php');

    $ret = array();
    $err="";

    $tmp_plg_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/plg";
    $current_plg_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/serverPlugUpdate/plg";

    $tmp_prg_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/prg";
    $current_prg_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "01_defaultConf_RPi/serverPlugUpdate/prg";

    exec("diff -r $tmp_plg_conf $current_plg_conf",$ret,$err);

    if($err!=0) {
        echo json_encode($err);
    } else {
        exec("diff -r $tmp_prg_conf $current_prg_conf",$ret,$err);
        echo json_encode($err);
    }
 
?>
