<?php

    // Include libraries
    if (file_exists('../../libs/db_get_common.php') === TRUE)
    {
        // Script call by Ajax
        require_once('../../libs/config.php');
        require_once('../../libs/db_get_common.php');
        require_once('../../libs/db_set_common.php');
        require_once('../../libs/utilfunc.php');
        require_once('../../libs/utilfunc_sd_card.php');
        require_once('../../libs/debug.php');
    }
    
    $out = array();
    
    // Get Log
    $sLog = get_informations("log",$out);
        
    // Get Cultibox ID
    $sID = get_informations("cbx_id",$out);

    // Get Firmware version
    $sFirm = get_informations("firm_version",$out);
   


    if((strcmp("$sLog","")!=0)||((strcmp("$sID","")!=0)&&($sID!=0))||(strcmp("$sFirm","")!=0)) {
        $sIP= php_uname("a");
        $sBro=getenv("HTTP_USER_AGENT");

        $sVersion=get_configuration("VERSION",$out);
        $sDate=date("Y-m-d H:i:s"); 

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $GLOBALS['REMOTE_DATABASE']
                .'?date=' . urlencode($sDate)
                .'&log='.urlencode($sLog)
                .'&ip='.urlencode($sIP)
                .'&cbx_soft_version='.urlencode($sVersion)
                .'&cbx_id='.urlencode($sID)
                .'&cbx_firmware='.urlencode($sFirm)
                .'&browser='.urlencode($sBro)
        ));


        if($GLOBALS['DEBUG_TRACE']) {
            echo "Infos:\n=======\n";
            echo "\ndate=" .$sDate;
            echo "\nlog=".$sLog;
            echo "\nip=".$sIP;
            echo "\ncbx_soft_version=".$sVersion;
            echo "\ncbx_id=".$sID;
            echo "\ncbx_firmware=".$sFirm;
            echo "\nbrowser=".$sBro;
        }
         
        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);
        echo json_encode("1");
    } else {
        echo json_encode("0");
    }

?>
