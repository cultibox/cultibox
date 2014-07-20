<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}


if((isset($_GET['type_reset']))&&(!empty($_GET['type_reset']))) {
    $type_reset=$_GET['type_reset'];
}



if((isset($type))&&(!empty($type))&&(isset($type_reset))&&(!empty($type_reset))) {
    if($type_reset == "all") {
        if(logs\reset_log($type)) {
                echo "1";
        } else {
                echo "-1";
        }
    } else {
        if((isset($_GET['start']))&&(!empty($_GET['start']))) {
            $start=$_GET['start'];
            if(logs\reset_log($type,$start,$start)) {
                echo "1";
            } else {
                echo "-1";
            }
        } else {
            echo "-1";
        }
    }
}


?>
