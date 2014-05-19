<?php

if (!isset($_SESSION)) {
	session_start();
}

/* Libraries requiered: 
        db_*_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
        debug.php     : functions for PHP and SQL debugs
        utilfunc_sd_card.php : functions for SD card management
*/
require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once('main/libs/db_set_common.php');
require_once('main/libs/utilfunc.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang(); //Language used by the user
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']); //Short language used to compute pages
$version=get_configuration("VERSION",$main_error); //Current version of the software
__('LANG');

// ================= VARIABLES ================= //
$sd_card=""; //Path of the SD card
$wizard=true; 
$nb_plugs = get_configuration("NB_PLUGS",$main_error); //Get current actives number of plugs
$stats=get_configuration("STATISTICS",$main_error); //Check if the user has activated the sending statistics option
$pop_up_message=""; //For the informations pop up messages
$pop_up_error_message=""; //For the errors pop up messages
$pop_up=get_configuration("SHOW_POPUP",$main_error); //Check if the user has activated the pop up messages options
$compat=true; //Variable to check if the browser used is compatible with the software
$notes=array(); //Array which will contains notes displayed in the welcome pages
$browser=get_browser_infos(); //Get browsers informations by PHP: browser name, version...

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

//Check the browser compatibility, if it's not compatible, the welcome page wil display a warning message
if(count($browser)>0) {
    $compat=check_browser_compat($browser); //Check is the browser used is compatible
}

//If programs configured by user is empty, display the wizard interface link
if(isset($nb_plugs)&&(!empty($nb_plugs))) {
    if(check_programs($nb_plugs)) {
        $wizard=false; //There is two welcome pages displayed: if it's the first time the user launche the software (no programs recorded yet) we will display links altought we will display the notes
    }
}

$user_agent = getenv("HTTP_USER_AGENT");

get_notes($notes,$_SESSION['LANG'],$main_error);

// Include in html pop up and message
include('main/templates/post_script.php');

//program\check_db();

//Display the welcome template
include('main/templates/welcome.html');

//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) { //If the debug option is activated, we print generation time of the page, variables sizes...
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." s.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}


?>
