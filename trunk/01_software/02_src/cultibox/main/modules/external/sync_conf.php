<?php 

    require_once('../../libs/config.php');

    $path_ouput             = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";
    $path_ouput_linux_rm    = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/*";
    $path_ouput_linux_cp    = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/";
    $path_input_linux_cp    = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/*";
    
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
            exec("sudo rm -rf -R $path_ouput_linux",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
            }
            break;            
            exec("sudo cp -R $path_input_linux_cp $path_ouput_linux_cp",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
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
