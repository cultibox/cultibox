<?php 

    require_once('../../libs/config.php');

    $path_output             = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi";
    $path_output_linux       = $GLOBALS['CULTIPI_CONF_PATH'] . "/01_defaultConf_RPi/*";
    $path_input_linux_cp    = $GLOBALS['CULTIPI_CONF_TEMP_PATH'] . "/*";
    
    switch(php_uname('s')) {
        case 'Windows NT':
            $path = str_replace("/","\\",$GLOBALS['CULTIPI_CONF_TEMP_PATH']);
            $confPath = str_replace("/","\\",$path_output);
            exec("xcopy /Y /E $path $confPath",$ret,$err);
            if ($err != 0) 
            {
                echo json_encode($err);
            }
            break;
        default : 
            if(($path_output!="")&&(is_dir($path_output))) {
                exec("sudo cp -R $path_input_linux_cp $path_output/",$ret,$err);
                if ($err != 0) 
                {
                    echo json_encode($err);
                }
            }
            break;
    }

    switch(php_uname('s')) {
        case 'Windows NT':
            break;
        default : 
            exec("sudo chown -R cultipi:cultipi $path_output",$ret,$err);

            //Restart service:
            exec("sudo /etc/init.d/cultipi force-reload >/dev/null",$ret,$err);
            break;
    }
    
    echo json_encode($err);
?>
