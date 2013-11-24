<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['cost']))&&(!empty($_GET['cost']))&&(isset($_GET['historic']))&&(!empty($_GET['historic']))) {
    $cost=$_GET['cost'];
    $historic=$_GET['historic'];

    $main_error=array();
    configure_menu($cost,$historic,$main_error);
}



?>
