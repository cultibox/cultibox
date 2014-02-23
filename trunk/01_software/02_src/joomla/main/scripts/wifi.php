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
require_once('main/libs/debug.php');

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
}


$type_sensor[]=__('NA','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_TEMPHUMI','raw');
$type_sensor[]=__('SENSOR_T_WATER','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');





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
    $program="";
    $conf_uptodate=true;
    $error_copy=false;
    if(check_sd_card($sd_card)) {


        /* TO BE DELETED */
        if(!compat_old_sd_card($sd_card)) {
            $main_error[]=__('ERROR_COPY_FILE');
            $error_copy=true;
        }
        /* ************* */


        $program=create_program_from_database($main_error);
        if(!compare_program($program,$sd_card)) {
            $conf_uptodate=false;
            if(!save_program_on_sd($sd_card,$program)) {
                $main_error[]=__('ERROR_WRITE_PROGRAM');
                $error_copy=true;
            }
        }


        $ret_firm=check_and_copy_firm($sd_card);
        if(!$ret_firm) {
            $main_error[]=__('ERROR_COPY_FIRM');
            $error_copy=true;
        } else if($ret_firm==1) {
            $conf_uptodate=false;
        }


        if(!compare_pluga($sd_card)) {
            $conf_uptodate=false;
            if(!write_pluga($sd_card,$main_error)) {
                $main_error[]=__('ERROR_COPY_PLUGA');
                $error_copy=true;
            }
        }


        $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
        if(count($plugconf)>0) {
            if(!compare_plugconf($plugconf,$sd_card)) {
                $conf_uptodate=false;
                if(!write_plugconf($plugconf,$sd_card)) {
                    $main_error[]=__('ERROR_COPY_PLUG_CONF');
                    $error_copy=true;
                }
            }
        }


        if(!is_file("$sd_card/log.txt")) {
            if(!copy_empty_big_file("$sd_card/log.txt")) {
                $main_error[]=__('ERROR_COPY_TPL');
                $error_copy=true;
            }
        }


        if(!check_and_copy_index($sd_card)) {
            $main_error[]=__('ERROR_COPY_INDEX');
            $error_copy=true;
        }


        $wifi_conf=create_wificonf_from_database($main_error);
        if(!compare_wificonf($wifi_conf,$sd_card)) {
            $conf_uptodate=false;
            if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
                $main_error[]=__('ERROR_COPY_WIFI_CONF');
                $error_copy=true;
            }
        }

        $recordfrequency = get_configuration("RECORD_FREQUENCY",$main_error);
        $powerfrequency = get_configuration("POWER_FREQUENCY",$main_error);
        $updatefrequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$main_error);
        $alarmenable = get_configuration("ALARM_ACTIV",$main_error);
        $alarmvalue = get_configuration("ALARM_VALUE",$main_error);
        $resetvalue= get_configuration("RESET_MINMAX",$main_error);
        if("$updatefrequency"=="-1") {
            $updatefrequency="0";
        }


        if(!compare_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,$alarmenable,$alarmvalue,"$resetvalue")) {
            $conf_uptodate=false;
            if(!write_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,"$alarmenable","$alarmvalue","$resetvalue",$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_CONF');
                $error_copy=true;
            }
        }

        if((!$conf_uptodate)&&(!$error_copy)) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            set_historic_value(__('UPDATED_PROGRAM')." (".__('PLUG_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_SD');
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    }
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
