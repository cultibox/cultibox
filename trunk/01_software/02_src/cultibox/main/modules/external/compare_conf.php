<?php 

    require_once('../../libs/config.php');

    $ret = array();
    $err="";

    $tmp_conf       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverPlugUpdate";
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/serverPlugUpdate";

    exec("diff -r $tmp_conf $current_conf",$ret,$err);

    echo json_encode($err);
?>
