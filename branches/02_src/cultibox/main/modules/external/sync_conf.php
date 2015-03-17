<?php 

    require_once('../../libs/config.php');

    
    
    $tmp_plg_conf   = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/plg";
    $current_conf   = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/serverPlugUpdate/";
    $tmp_prg_conf   = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cnf/prg";

    // TODO : Un seul copy doit permettre de tout faire (c'est ce que j'ai fait pour windows)
    $path_ouput             = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";
    $path_cultiPi           = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/cultiPi";
    $path_serverAcqSensor   = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverAcqSensor";
    $path_serverHisto       = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverHisto";
    $path_serverLog         = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverLog";
    $path_serverPlugUpdate  = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/serverPlugUpdate";
    
    switch(php_uname('s')) {
        case 'Windows NT':
            $path = str_replace("/","\\",$GLOBALS['CULTIPI_CONF_TEMP_PATH']);
            $confPath = str_replace("/","\\",$path_ouput);
            exec("xcopy /Y /E $path $confPath",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
            }
            break;
        default : 
            exec("sudo cp -R $path_cultiPi $path_ouput",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
                break;
            }
            exec("sudo cp -R $path_serverAcqSensor $path_ouput",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
                break;
            }
            exec("sudo cp -R $path_serverHisto $path_ouput",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
                break;
            }
            exec("sudo cp -R $path_serverLog $path_ouput",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
                break;
            }
            exec("sudo cp -R $path_serverPlugUpdate $path_ouput",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
                break;
            }
            break;
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
    
    echo json_encode($err);
?>
