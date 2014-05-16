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
$update=get_configuration("CHECK_UPDATE",$main_error); //To check if update checkin is anabled by the user
$version=get_configuration("VERSION",$main_error); //To get the current version of the software 
$pop_up = get_configuration("SHOW_POPUP",$main_error); // To check if pop up messages are enabled
$pop_up_message=""; 
$xml_list=get_external_calendar_file(); //Get the list of the xml file is the mail/xml directory to add event from those files
$calendar_start=getvar('calendar_startdate'); //Variable used when user add a grown calendar to a specific date

if((!isset($calendar_start))||(empty($calendar_start))) {
    $calendar_start=date('Y')."-".date('m')."-".date('d'); //If user didn't had a grown calendar, today's date is used for the form
}

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
$lang=$_SESSION['LANG'];
__('LANG');

$title_list=get_title_list(); //Get list of titles available from the database to be used in the calendar form


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


// After creating the XML available file list, checking that each file is an external file like moon calendar
$list_xml=array();
foreach($xml_list as $liste) {
    $check_xml=check_config_xml_file($liste);
    $list_xml[]=array(
            "name" => $liste,
            "value" => $check_xml
            );
}

if(count($list_xml)>0) {
    array_multisort($list_xml, SORT_ASC); //Sort of xml files to be displayd by alphabetical order
}

//Get the important event list for the previous and next week to display:
$important_list=array();
$important_list=get_important_event_list($main_error); //Get import event list from database


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


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations will be send for debug
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["log"]="";

if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    copy_empty_big_file("$sd_card/log.txt");
}

if(strcmp($informations["cbx_id"],"")!=0) insert_informations("cbx_id",$informations["cbx_id"]);
if(strcmp($informations["firm_version"],"")!=0) insert_informations("firm_version",$informations["firm_version"]);
if(strcmp($informations["log"],"")!=0) insert_informations("log",$informations["log"]);


//Get informations from XML files
$substrat=array();
$product=array();
$file=array();
if($handle = @opendir('main/xml')) {
    while (false !== ($entry = readdir($handle))) {
        if(($entry!=".")&&($entry!="..")) {
            $file[]=$entry;
        }
    }
}

if(count($file)>0) {
    asort($file);
    foreach($file as $entry) {
        $rss_file = file_get_contents("main/xml/".$entry);
        $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

        foreach ($xml as $tab) {
            if(is_array($tab)) {
                if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))&&(array_key_exists('periode', $tab))) {
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
}

$substrat=array_unique($substrat);
asort($substrat);


// Check for update availables. If an update is available, the link to this update is displayed with the informations div
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



// Part for the calendar: if a cultibox SD card is present, the 'calendar' is updated into this SD card
if((isset($sd_card))&&(!empty($sd_card))) {
    $data=create_calendar_from_database($main_error);
    if(!check_sd_card($sd_card)) {
        $main_error[]=__('ERROR_WRITE_CALENDAR');
    } else {
        clean_calendar($sd_card);
        if(count($data)>0) {
            if(!write_calendar($sd_card,$data,$main_error)) {
                $main_error[]=__('ERROR_WRITE_CALENDAR');
            }
        }
    } 
}



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
