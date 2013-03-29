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
$main_error=array();
$lang=get_configuration("LANG",$main_error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


// ================= VARIABLES ================= //
$error=array();


if(isset($_POST['startday'])) {
      $_SESSION['startday']=$_POST['startday'];
}

if(isset($_POST['select_power'])) {
      $_SESSION['select_power']=$_POST['select_power'];
}

if(isset($_POST['select_plug'])) {
      $_SESSION['select_plug']=$_POST['select_plug'];
}

if(isset($_POST['select_sensor'])) {
      $_SESSION['select_sensor']=$_POST['select_sensor'];
}

if(isset($_POST['startyear'])) {
      $_SESSION['startyear']=$_POST['startyear'];
}

if(isset($_POST['startmonth'])) {
      $_SESSION['startmonth']=$_POST['startmonth'];
}


if(isset($_POST['import_log'])) {
    $_SESSION['import_log']=$_POST['import_log'];
}


if(isset($_POST['reset_sd_card'])) {
    $_SESSION['reset_sd_card']=$_POST['reset_sd_card'];
}

if(isset($_POST['selected_hdd'])) {
    $_SESSION['selected_hdd']=$_POST['selected_hdd'];
}

if(isset($_POST['log_search'])) {
    $_SESSION['log_search']=$_POST['log_search'];
}


if(isset($_POST['anchor'])) {
    $anchor=$_POST['anchor'];
}


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


if((isset($_POST['selected_hdd']))&&(!empty($_POST['selected_hdd']))&&(isset($_POST['reset_sd_card']))&&(!empty($_POST['reset_sd_card']))) {
    $_SESSION['submenu']="card_interface";
    header("Refresh: 5;url=./configuration");
} elseif(((isset($_SESSION['import_log']))&&($_SESSION['import_log']))) {
    if((isset($anchor))&&(!empty($anchor))) {
        header("Refresh: 5;url=./display-logs#$anchor");
    } else { 
        header("Refresh: 5;url=./display-logs");
    }
} 

//Display the waiting template
include('main/templates/waiting.html');

?>
