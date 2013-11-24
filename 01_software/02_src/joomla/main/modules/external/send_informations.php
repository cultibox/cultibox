<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if((isset($_GET['infos']))&&(!empty($_GET['infos']))) {
    $infos=$_GET['infos'];

    if(array_key_exists('log',$infos)) {
        $sLog=$infos['log'];
    } else {
        $sLog="";
    }


    if(array_key_exists('id_computer',$infos)) {
        $sIP=$infos['id_computer'];
    } else {
        $sIP="";
    }


    if(array_key_exists('cbx_id',$infos)) {
        $sID=$infos['cbx_id'];
    } else {
        $sID="";
    }


    if(array_key_exists('firm_version',$infos)) {
        $sFirm=$infos['firm_version'];
    } else {
        $sFirm="";
    }


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
}

?>
