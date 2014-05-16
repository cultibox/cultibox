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
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang(); //Language used by the user
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']); //Short language used to compute pages
$version=get_configuration("VERSION",$main_error); //Current version of the software
__('LANG');


// ================= VARIABLES ================= //
$sd_card=""; //Path of the SD card
$update=get_configuration("CHECK_UPDATE",$main_error); //Check if user has activated the update checking system
$wizard=true; 
$nb_plugs = get_configuration("NB_PLUGS",$main_error); //Get current actives number of plugs
$stats=get_configuration("STATISTICS",$main_error); //Check if the user has activated the sending statistics option
$pop_up_message=""; //For the informations pop up messages
$pop_up_error_message=""; //For the errors pop up messages
$pop_up=get_configuration("SHOW_POPUP",$main_error); //Check if the user has activated the pop up messages options
$compat=true; //Variable to check if the browser used is compatible with the software
$notes=array(); //Array which will contains notes displayed in the welcome pages
$browser=get_browser_infos(); //Get browsers informations by PHP: browser name, version...


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


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card(); 
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    $conf_uptodate=1; //To chck if the sd configuration has been updated or not
    $conf_uptodate=check_and_update_sd_card("$sd_card"); //Check if the SD card is updated or not

    if(!$conf_uptodate) { //If the SD card has been updated
        //Display messages:
        $main_info[]=__('UPDATED_PROGRAM');
        $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
    } else if($conf_uptodate>1) {
        $error_message=get_error_sd_card_update_message($conf_uptodate);
        if(strcmp("$error_message","")!=0) {
            $main_error[]=get_error_sd_card_update_message($conf_uptodate);
        }
    }
    $main_info[]=__('INFO_SD_CARD').": $sd_card";
} else {
    $main_error[]=__('ERROR_SD_CARD');
}


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations are sent to debug by cultibox.js script
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";

if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations); //Try to retrieve informations from the log.txt file
    copy_empty_big_file("$sd_card/log.txt"); //Clean of the log.txt file
}


//If some informations have been found from the log.txt file, insertion in the informations table of the database - else, getting current informations of the databese:
if(strcmp($informations["cbx_id"],"")==0) {
    $informations["cbx_id"]=get_informations("cbx_id");
} else {
    insert_informations("cbx_id",$informations["cbx_id"]);
}

if(strcmp($informations["firm_version"],"")==0) {
    $informations["firm_version"]=get_informations("firm_version");
} else {
    insert_informations("firm_version",$informations["firm_version"]);
}

if(strcmp($informations["log"],"")==0) {
    $informations["log"]="NA";
} else {    
    insert_informations("log",$informations["log"]);
}


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
    if((!isset($_SESSION['UPDATE_CHECKED']))||(empty($_SESSION['UPDATE_CHECKED']))) {
        if($sock=@fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
            if(check_update_available($version,$main_error)) {
                $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
                $_SESSION['UPDATE_CHECKED']="True";
            } else {
                $_SESSION['UPDATE_CHECKED']="False";
            }
        } else {
            $main_error[]=__('ERROR_REMOTE_SITE');
            $_SESSION['UPDATE_CHECKED']="";
        }
    } else if(strcmp($_SESSION['UPDATE_CHECKED'],"True")==0) {
        $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
    }
} 
 

//Get databases notes recorded in notes table in the notes array variable - to be displayed if users has already recorded programs
get_notes($notes,$_SESSION['LANG'],$main_error);

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
