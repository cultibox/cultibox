<?php 

    require_once('../../libs/config.php');

    $tmp_plg_conf   = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/plg";
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/serverPlugUpdate/";

    $tmp_prg_conf   = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/prg";

    switch(php_uname('s')) {
        case 'Windows NT':
            $plgPath = str_replace("/","\\",$tmp_plg_conf);
            $confPath = str_replace("/","\\",$current_conf . "/plg");
            exec("xcopy $plgPath $confPath",$ret,$err);
            break;
        default : 
            exec("sudo cp -R $tmp_plg_conf $current_conf",$ret,$err);
            break;
    }
    

    if($err != 0) {
        echo json_encode($err);
    } else {
        switch(php_uname('s')) {
            case 'Windows NT':
                $prgPath = str_replace("/","\\",$tmp_prg_conf);
                $confPath = str_replace("/","\\",$current_conf . "/prg");
                exec("xcopy $prgPath $confPath",$ret,$err);
                break;
            default : 
                exec("sudo cp -R $tmp_prg_conf $current_conf",$ret,$err);
                break;
        }
        
        echo json_encode($err);
    }

    switch(php_uname('s')) {
        case 'Windows NT':
            break;
        default : 
            exec("sudo chown -R cultipi:cultipi $current_conf",$ret,$err);

            //Restart service:
            exec("sudo /etc/init.d/cultipi force-reload >/dev/null",$ret,$err);
            break;
    }
    

?>
