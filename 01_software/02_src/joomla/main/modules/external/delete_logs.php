<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}


if((isset($_GET['type_reset']))&&(!empty($_GET['type_reset']))) {
    $type_reset=$_GET['type_reset'];
}


if((isset($type))&&(!empty($type))&&(isset($type_reset))&&(!empty($type_reset))) {
    if(strcmp($type_reset,"all")==0) {
        if(reset_log($type)) {
                /*
                $main_info[]=__('VALID_DELETE_LOGS');
                $pop_up_message=popup_message(__('VALID_DELETE_LOGS'));
                set_historic_value(__('VALID_DELETE_LOGS')." (".__('LOGS_PAGE').")","histo_info",$main_error);
                //Reset power from the reset button
if(!empty($reset_log_power)) {
    if(reset_log("power",$main_error)) {
        $main_info[]=__('VALID_DELETE_LOGS');
        $pop_up_message=$pop_up_message.popup_message(__('VALID_DELETE_LOGS'));
        set_historic_value(__('VALID_DELETE_LOGS')." (".__('LOGS_PAGE').")","histo_info",$main_error);
    }
}
                */
                echo "1";
        } else {
                echo "-1";
        }
    } else {
        if((isset($_GET['start']))&&(!empty($_GET['start']))&&(isset($_GET['end']))&&(!empty($_GET['end']))) {
            $start=$_GET['start'];
            $end=$_GET['end'];
            if(reset_log($type,$start,$end)) {
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
