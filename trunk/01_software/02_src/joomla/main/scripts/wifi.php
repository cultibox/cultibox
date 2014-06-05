<?php


if (!isset($_SESSION)) {
    session_start();
}

/* Libraries requiered: 
        db_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
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
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$informations = Array();
$version=get_configuration("VERSION",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);
$pop_up_message="";
$pop_up_error_message="";
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$wifi_ip=get_configuration("WIFI_IP",$main_error);

if((!isset($wifi_ip))||(empty($wifi_ip))) {
   $wifi_ip="000.000.000.000";
   $main_erro[]=__('ERROR_ACCESS_INFO_WIFI');
} else {
   $wifi_file=@file_get_contents('http://'.$wifi_ip.'/info.xml'); 
   if(!$wifi_file) {
        $main_error[]=__('ERROR_ACCESS_INFO_WIFI');
   }
}


$type_sensor[]=__('NA','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_TEMPHUMI','raw');
$type_sensor[]=__('SENSOR_T_WATER','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');


//Translate for Jquery:
$translate[]=__('NA');
$translate[]=__('DIMMER');


//Set plug type translation:
for($i=0; $i<count($plugs_infos);$i++) {
    $plugs_infos[$i]['translate'] = translate_PlugType($plugs_infos[$i]['PLUG_TYPE'])
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

// Include in html pop up and message
include('main/templates/post_script.php');

//Display the plug template
include('main/templates/wifi.html');

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
