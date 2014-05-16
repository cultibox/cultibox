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
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$informations = Array();
$update=get_configuration("CHECK_UPDATE",$main_error);
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
    switch($plugs_infos[$i]['PLUG_TYPE']) {
        case 'other': $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
        case 'ventilator': $plugs_infos[$i]['translate']=__('PLUG_VENTILATOR'); break;
        case 'heating': $plugs_infos[$i]['translate']=__('PLUG_HEATING'); break;
        case 'pump': $plugs_infos[$i]['translate']=__('PLUG_PUMP'); break;
        case 'lamp': $plugs_infos[$i]['translate']=__('PLUG_LAMP'); break;
        case 'humidifier': $plugs_infos[$i]['translate']=__('PLUG_HUMIDIFIER'); break;
        case 'dehumidifier': $plugs_infos[$i]['translate']=__('PLUG_DEHUMIDIFIER'); break;
        default: $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
    }
}


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
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
