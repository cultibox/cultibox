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
$quick_load=false;


//Manage to use a quick load or not and get some variables to pass them to the real log page
if(isset($_POST['startday'])) {
      $_SESSION['startday']=$_POST['startday'];
      if(!empty($_SESSION['startday'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_power'])) {
      $_SESSION['select_power']=$_POST['select_power'];
      if(!empty($_SESSION['select_power'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_plug'])) {
      $_SESSION['select_plug']=$_POST['select_plug'];
      if(!empty($_SESSION['select_plug'])) {
        $quick_load=true;
      }
}

if(isset($_POST['select_sensor'])) {
      $_SESSION['select_sensor']=$_POST['select_sensor'];
      if(!empty($_SESSION['select_sensor'])) {
        $quick_load=true;
      }
}

if(isset($_POST['startyear'])) {
      $_SESSION['startyear']=$_POST['startyear'];
      if(!empty($_SESSION['startyear'])) {
        $quick_load=true;
      }
}

if(isset($_POST['startmonth'])) {
      $_SESSION['startmonth']=$_POST['startmonth'];
      if(!empty($_SESSION['startmonth'])) {
        $quick_load=true;
      }
}


if(isset($_POST['import_log'])) {
    $_SESSION['import_log']=$_POST['import_log'];
    if(!empty($_SESSION['import_log'])) {
        $quick_load=false;
    }
}


if(isset($_POST['reset_sd_card'])) {
    $_SESSION['reset_sd_card']=$_POST['reset_sd_card'];
}

if(isset($_POST['selected_hdd'])) {
    $_SESSION['selected_hdd']=$_POST['selected_hdd'];
}


if($quick_load) {
    $_SESSION['quick_load']="True";
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
        header("Refresh: 5;url=./view-logs#$anchor");
    } else { 
        header("Refresh: 5;url=./view-logs");
    }
} elseif((!isset($sd_card))||(empty($sd_card))||($quick_load)||((isset($_SESSION['loaded']))&&($_SESSION['loaded']))) {      
   if((isset($anchor))&&(!empty($anchor))) {
        header("Location: ./view-logs#$anchor");
    } else {
        header( 'Location: ./view-logs' ) ;
    }
} else {
    if((isset($anchor))&&(!empty($anchor))) {
        header("Refresh: 5;url=./view-logs#$anchor");
    } else {
        header("Refresh: 5;url=./view-logs");
    }
}


//Display the waiting template
include('main/templates/waiting.html');

?>
