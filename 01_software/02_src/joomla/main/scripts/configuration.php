<?php

if (!isset($_SESSION)) {
    session_start();
}

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
$info=array();
$main_info=array();

$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$update_conf=false;

$pop_up_message="";
$pop_up_error_message="";

$alarm_enable   = getvar('alarm_enable');
$wifi_enable    = getvar('wifi_enable');
$wifi_ssid      = getvar('wifi_ssid');
$wifi_key_type  = getvar('wifi_key_type');
$wifi_password  = getvar('wifi_password',false);
$wifi_ip        = getvar('wifi_ip');
$submenu        = getvar("submenu",$main_error);
$stats          = getvar("stats",$main_error);
$submit         = getvar("submit_conf_value",$main_error);
$wifi_manual    = getvar("wifi_ip_manual");
$rtc_offset     = getvar("rtc_offset");
$version        = get_configuration("VERSION",$main_error);

// By default the expanded menu is the user interface menu
if((!isset($submenu))||(empty($submenu))) {
    if(isset($_SESSION['submenu'])) {
        $submenu=$_SESSION['submenu'];
        unset($_SESSION['submenu']);
    } else {
        $submenu="user_interface";
    }
} 

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $hdd_list=array();
   $sd_card=get_sd_card($hdd_list);
   $new_arr=array();
   foreach($hdd_list as $hdd) {
        if(disk_total_space($hdd)<=2200000000) $new_arr[]=$hdd;

   }
   $hdd_list=$new_arr;
   sort($hdd_list);
}

// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

//============================== GET OR SET CONFIGURATION PART ====================
//update_conf sert à définir si la configuration impacte la carte SD
$conf_arr = array();
$conf_arr["SHOW_POPUP"]             = array ("update_conf" => "0", "var" => "pop_up");
$conf_arr["CHECK_UPDATE"]           = array ("update_conf" => "0", "var" => "update");
$conf_arr["COLOR_COST_GRAPH"]       = array ("update_conf" => "0", "var" => "color_cost");
$conf_arr["RECORD_FREQUENCY"]       = array ("update_conf" => "1", "var" => "record_frequency");
$conf_arr["POWER_FREQUENCY"]        = array ("update_conf" => "1", "var" => "power_frequency");
$conf_arr["UPDATE_PLUGS_FREQUENCY"] = array ("update_conf" => "1", "var" => "update_frequency");
$conf_arr["NB_PLUGS"]               = array ("update_conf" => "0", "var" => "nb_plugs");
$conf_arr["SECOND_REGUL"]           = array ("update_conf" => "0", "var" => "second_regul");
$conf_arr["STATISTICS"]             = array ("update_conf" => "0", "var" => "stats");
$conf_arr["SHOW_COST"]              = array ("update_conf" => "0", "var" => "show_cost");
$conf_arr["ADVANCED_REGUL_OPTIONS"] = array ("update_conf" => "1", "var" => "advanced_regul");
$conf_arr["RTC_OFFSET"]             = array ("update_conf" => "1", "var" => "rtc_offset");
$conf_arr["RESET_MINMAX"]             = array ("update_conf" => "1", "var" => "reset_minmax");
$conf_arr["ALARM_VALUE"]             = array ("update_conf" => "1", "var" => "alarm_value");

foreach ($conf_arr as $key => $value) {
    ${$value['var']} = get_configuration($key,$main_error);
}



if((isset($rtc_offset))&&(!empty($rtc_offset))) {
    insert_configuration("RTC_OFFSET",$rtc_offset,$main_error);
    $update_conf=true;
    
    // Value saved must be converted to be saved in SD Card
    $rtc_offset_computed = get_rtc_offset($rtc_offset);
    
} else {
    $rtc_offset = get_configuration("RTC_OFFSET",$main_error);
    $rtc_offset_computed = $rtc_offset;
}




if(!empty($alarm_enable)) {
    insert_configuration("ALARM_ACTIV","$alarm_enable",$main_error);
    $update_conf=true;

    if((strcmp($alarm_enable,"0001")==0)&&(!empty($alarm_value))) {
       insert_configuration("ALARM_VALUE","$alarm_value",$main_error);
    } else {
        $alarm_value=get_configuration("ALARM_VALUE",$main_error);
    }
} else {
    $alarm_enable = get_configuration("ALARM_ACTIV",$main_error);
    $alarm_value = get_configuration("ALARM_VALUE",$main_error);
}

if(empty($alarm_value)) {
    $alarm_value = get_configuration("ALARM_VALUE",$main_error);
}

// Wifi tab is modified
if($wifi_enable != "") {

    // Save activation of wifi
    insert_configuration("WIFI",$wifi_enable,$main_error);
    
    if($wifi_enable) {
       insert_configuration("WIFI_SSID",$wifi_ssid,$main_error);
       insert_configuration("WIFI_KEY_TYPE",$wifi_key_type,$main_error);
       if(strcmp($wifi_password,"")!=0) {
           insert_configuration("WIFI_PASSWORD",$wifi_password,$main_error);
       }
            
       if($wifi_manual) {
           insert_configuration("WIFI_IP",$wifi_ip,$main_error);
           insert_configuration("WIFI_IP_MANUAL","1",$main_error);
       } else {
           insert_configuration("WIFI_IP_MANUAL","0",$main_error);    
           insert_configuration("WIFI_IP","000.000.000.000",$main_error);
           $wifi_ip=get_configuration("WIFI_IP");
       }
    } else {
       insert_configuration("WIFI_SSID","",$main_error);
       insert_configuration("WIFI_KEY_TYPE","NONE",$main_error);
       insert_configuration("WIFI_PASSWORD","",$main_error);
       insert_configuration("WIFI_IP","000.000.000.000",$main_error);
       insert_configuration("WIFI_IP_MANUAL","0",$main_error);

       $wifi_ssid="";
       $wifi_key_type="NONE";
       $wifi_password="";
       $wifi_ip="";
    }
    $update_conf=true;

    if(!empty($sd_card) && isset($sd_card)) {
        $wifi_conf = create_wificonf_from_database($main_error);
        if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
            $main_error[]=__('ERROR_WIFI_CONF');
        }
    }
} 

$wifi_enable    = get_configuration("WIFI",$main_error);
$wifi_ssid      = get_configuration("WIFI_SSID",$main_error);
$wifi_key_type  = get_configuration("WIFI_KEY_TYPE",$main_error);
$wifi_manual    = get_configuration("WIFI_IP_MANUAL",$main_error);
$wifi_password  = get_configuration("WIFI_PASSWORD",$main_error);
$wifi_ip        = get_configuration("WIFI_IP",$main_error);



if((isset($advanced_regul))&&(!empty($advanced_regul))) {
    insert_configuration("ADVANCED_REGUL_OPTIONS","$advanced_regul",$main_error);
    if(strcmp("$advanced_regul","False")==0) {
        $check_error=false;
        for($i=0;$i<$GLOBALS['NB_MAX_PLUG'];$i++) {
            insert_plug_conf("PLUG_REGUL_SENSOR",$i,"1",$main_error);
            insert_plug_conf("PLUG_COMPUTE_METHOD",$i,"M",$main_error);
      
            if((!empty($sd_card))&&(isset($sd_card))) {
                $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
                if(count($plugconf)>0) {
                    if(!write_plugconf($plugconf,$sd_card)) {
                        if(!$check_error) {
                            $main_error[]=__('ERROR_COPY_PLUG_CONF');
                            $check_error=true;
                        }
                    }
                }
            }
        }
    }
    $update_conf=true;
} else {
    $advanced_regul = get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
}



// Is a field has been changed and there is no error in the value: display success message
if(empty($main_error)) {
    if($update_conf === true) {
        if((!empty($sd_card))&&(isset($sd_card))) {
            $main_info[]=__('VALID_UPDATE_CONF');
            $pop_up_message=$pop_up_message.popup_message(__('VALID_UPDATE_CONF'));
        } else {
            $main_info[]=__('VALID_UPDATE_CONF_WITHOUT_SD');
            $pop_up_message=$pop_up_message.popup_message(__('VALID_UPDATE_CONF_WITHOUT_SD'));
        }
    }
}


// Change files on the cultibox SD card after the configuration has been updated: plug's frequency, alarm value etc...
if((isset($submit))&&(!empty($submit))) {
    if((isset($sd_card))&&(!empty($sd_card))) {
        $updateFrequencyCorrected = $update_frequency;

        if("$updateFrequencyCorrected"=="-1") {
            $updateFrequencyCorrected = 0;
        }

        // Save the configuration on SD Card
        if(!write_sd_conf_file($sd_card,$record_frequency,$updateFrequencyCorrected,$power_frequency,"$alarm_enable","$alarm_value","$reset_minmax",$rtc_offset_computed,$main_error)) {
            $main_error[] = __('ERROR_WRITE_SD_CONF');
        }
    } 
    else
    {
        $main_error[] = __('ERROR_SD_CARD_CONF')." <img src='main/libs/img/infos.png' alt='' title='".__('TOOLTIP_WITHOUT_SD')."' />";
    }
}


// Include in html pop up and message
include('main/templates/post_script.php');

//Display the configuration template
include('main/templates/configuration.html');

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
