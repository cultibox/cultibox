<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();
$version=get_configuration("VERSION",$main_error); //Current version of the software

// ================= VARIABLES ================= //
$sd_card=""; //Path of the SD card
$wizard=true; 
$nb_plugs = get_configuration("NB_PLUGS",$main_error); //Get current actives number of plugs
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


if((!isset($sd_card))||(empty($sd_card))) {
   $main_error[]=__('ERROR_SD_CARD');
}


//Check the browser compatibility, if it's not compatible, the welcome page wil display a warning message
if(count($browser)>0) {
    $compat=check_browser_compat($browser); //Check is the browser used is compatible
}

//If programs configured by user is empty, display the wizard interface link
if(isset($nb_plugs)&&(!empty($nb_plugs))) {
    if(check_programs($nb_plugs,"-1")) {
        $wizard=false; //There is two welcome pages displayed: if it's the first time the user launche the software (no programs recorded yet) we will display links altought we will display the notes
    }
}

$user_agent = getenv("HTTP_USER_AGENT");

get_notes($notes,$_SESSION['LANG'],$main_error);

// Include in html pop up and message
include('main/scripts/post_script.php');

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
