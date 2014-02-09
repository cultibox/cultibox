<?php

if (!isset($_SESSION)) {
   session_start();
}

/* Libraries requiered: 
        db_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
*/
require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');


// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
if((!isset($_SESSION['LANG']))||(empty($_SESSION['LANG']))) {
    $_SESSION['LANG']="fr_FR";
    $_SESSION['SHORTLANG'] = "fr";
}
__('LANG');


// ================= VARIABLES ================= //
if(isset($_POST['select_sensor'])) {
      $_SESSION['select_sensor']=$_POST['select_sensor'];
}

if(isset($_POST['startyear'])) {
      $_SESSION['startyear']=$_POST['startyear'];
}

if(isset($_POST['startmonth'])) {
      $_SESSION['startmonth']=$_POST['startmonth'];
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


if(isset($_POST['startmonth'])) {
    $with_sd=false;
    if((isset($sd_card))&&(!empty($sd_card))) {
        $with_sd=true;
    }
    $url="./display-logs-".$_SESSION['SHORTLANG'];
    header("Refresh: 2;url=$url");
}

//Display the waiting template
include('main/templates/waiting.html');

?>
