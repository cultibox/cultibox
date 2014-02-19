<?php

require_once('../../libs/db_common.php');

if((isset($_GET['ip']))&&(!empty($_GET['ip']))) {
    $ip=$_GET['ip'];
}

$main_error="";

if((isset($ip))&&(!empty($ip))) {
    //Check that ip has a right format:
    if(filter_var($ip, FILTER_VALIDATE_IP)) {
        insert_configuration("WIFI_IP","$ip",$main_error);
    }
}

?>
