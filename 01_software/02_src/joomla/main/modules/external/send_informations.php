<?php
    if (!isset($_SESSION)) {
        session_start();
    }

    // Define language using post lang parameter
    $_SESSION['LANG'] = "fr_FR";
    switch($_POST['lang']) {
        case 'fr': 
            $_SESSION['LANG'] = "fr_FR";
            break;
        case 'en': 
            $_SESSION['LANG'] = "en_GB";
            break;
        case 'it': 
            $_SESSION['LANG'] = "it_IT";
            break;
        case 'de': 
            $_SESSION['LANG'] = "de_DE";
            break;
        case 'es': 
            $_SESSION['LANG'] = "es_ES";
            break;
    }

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
    
    // Define languaage
    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
    __('LANG');
    
    // Only one time per session
    if(!isset($_SESSION['INFO_SENT']) || empty($_SESSION['INFO_SENT'])) {
    
        // Get Log
        $sLog = get_configuration("log",$out);
        
        // get ID Computer
        $sIP = get_configuration("id_computer",$out);
        
        // Get Cultibox ID
        $sID = get_configuration("cbx_id",$out);

        // Get Firmware version
        $sFirm = get_configuration("firm_version",$out);
        
        $sBro=getenv("HTTP_USER_AGENT");

        $sVersion=get_configuration("VERSION",$main_error);
        $sDate=date("Y-m-d H:i:s"); 

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $GLOBALS['REMOTE_DATABASE'].'?date='.urlencode($sDate).'&log='.urlencode($sLog).'&ip='.urlencode($sIP).'&cbx_soft_version='.urlencode($sVersion).'&cbx_id='.urlencode($sID).'&cbx_firmware='.urlencode($sFirm).'&browser='.urlencode($sBro)
        ));

        // Send the request & save response to $resp
        $resp = curl_exec($curl);

        // Close request to clear up some resources
        curl_close($curl);
        
        $_SESSION['INFO_SENT'] = "True"

    }
    

?>
