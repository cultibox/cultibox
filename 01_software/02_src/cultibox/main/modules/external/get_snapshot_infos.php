<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');

date_default_timezone_set('Europe/Paris');

$date_creation="";
if(is_file($GLOBALS['BASE_PATH']."tmp/webcam.jpg")) {
    $date_creation=__('TIME_CREATION_IMG','js')." ".date("H:i:s", filectime($GLOBALS['BASE_PATH']."tmp/webcam.jpg"));
}


echo json_encode($date_creation);

?>
