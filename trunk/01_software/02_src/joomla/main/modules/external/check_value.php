<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
} else {
    return 0;
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
} else {
    return 0;
}


switch($type) {
    case 'short_time': if(!check_format_time("$value:00")) {
                            echo "error";
                        }
                        break;
    case 'alarm_value': if(!(check_numeric_value("$value"))||(!check_alarm_value("$value"))) {
                            echo "error";
                        }
                        break;
}

echo "1";

?>
