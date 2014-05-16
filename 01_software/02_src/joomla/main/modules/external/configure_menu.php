<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['cost']))&&(!empty($_GET['cost']))&&(isset($_GET['wifi']))&&(!empty($_GET['wifi']))) {
    $cost=$_GET['cost'];
    $wifi=$_GET['wifi'];
    $main_error=array();
    configure_menu($cost,$wifi,$main_error);
}



?>
