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


// ================= VARIABLES ================= //
$main_error=array();
$main_info=array();
$informations = Array(); //Aray containing data from the informations table or the log.txt file
$version             = get_configuration("VERSION",$main_error); //To get the current version of the software 

$calendar_start=getvar('calendar_startdate'); //Variable used when user add a grown calendar to a specific date
if((!isset($calendar_start))||(empty($calendar_start))) {
    $calendar_start=date('Y')."-".date('m')."-".date('d'); //If user didn't had a grown calendar, today's date is used for the form
}

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
$lang=$_SESSION['LANG'];
__('LANG');

$title_list = calendar\get_title_list(); //Get list of titles available from the database to be used in the calendar form
    
// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


//Get the important event list for the previous and next week to display:
$important_list = array();
$important_list = calendar\get_important_event_list($main_error); //Get import event list from database

// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

$substrat=array();
$product=array();

// List XML file find in folder
foreach(glob('main/xml/*.{xml}', GLOB_BRACE) as $entry) {
    $rss_file = file_get_contents($entry);
    $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

    foreach ($xml as $tab) {
        if(is_array($tab)) {
            if((array_key_exists('substrat', $tab))
                &&(array_key_exists('marque', $tab))
                &&(array_key_exists('periode', $tab)))
            {
                $substrat[]=ucwords(strtolower($tab['substrat']));
                $product[]= array(
                    "marque" => ucwords(strtolower($tab['marque'])),
                    "periode" => ucwords(strtolower($tab['periode'])),
                    "substrat" => ucwords(strtolower($tab['substrat']))
                );
            }
        }
    }   
}

$substrat=array_unique($substrat);
asort($substrat);

// Include in html pop up and message
include('main/templates/post_script.php');

//Display the calendar template
include('main/templates/calendar.html');

//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) {
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." secondes.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}


?>
