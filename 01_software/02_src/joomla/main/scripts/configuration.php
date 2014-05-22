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
$info=array();
$main_info=array();

$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$color_humidity=getvar('color_humidity');
$color_temperature=getvar('color_temperature');
$color_water=getvar('color_water');
$color_level=getvar('color_level');
$color_ph=getvar('color_ph');
$color_ec=getvar('color_ec');
$color_od=getvar('color_od');
$color_orp=getvar('color_orp');
$color_power=getvar('color_power');
$color_cost=getvar('color_cost');
$record_frequency=getvar('record_frequency');
$power_frequency=getvar('power_frequency');
$update_frequency=getvar('update_frequency');
$nb_plugs=getvar('nb_plugs');
$update_conf=false;
$temp_axis=getvar('temp_axis');
$hygro_axis=getvar('hygro_axis');
$pop_up=getvar('pop_up');
$pop_up_message="";
$pop_up_error_message="";
$alarm_enable=getvar('alarm_enable');
$alarm_value=getvar('alarm_value');
$wifi_enable=getvar('wifi_enable');
$wifi_ssid=getvar('wifi_ssid');
$wifi_key_type=getvar('wifi_key_type');
$wifi_password=getvar('wifi_password',false);
$wifi_ip=getvar('wifi_ip');
$version=get_configuration("VERSION",$main_error);
$submenu=getvar("submenu",$main_error);
$stats=getvar("stats",$main_error);
$advanced_regul=getvar("advanced_regul",$main_error);
$second_regul=getvar("second_regul",$main_error);
$show_cost=getvar("show_cost",$main_error);
$submit=getvar("submit_conf_value",$main_error);
$update_menu=false;
$minmax=getvar("minmax",$main_error);
$wifi_manual=getvar("wifi_ip_manual");
$rtc_offset=getvar("rtc_offset");


// By default the expanded menu is the user interface menu
if((!isset($submenu))||(empty($submenu))) {
    if(isset($_SESSION['submenu'])) {
        $submenu=$_SESSION['submenu'];
        unset($_SESSION['submenu']);
    } else {
        $submenu="user_interface";
    }
} 

// Check database consistency
check_database();

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
if((isset($pop_up))&&(!empty($pop_up))) {
    insert_configuration("SHOW_POPUP","$pop_up",$main_error);
    $update_conf=true;
} else {
    $pop_up = get_configuration("SHOW_POPUP",$main_error);
}

if((isset($update))&&(!empty($update))) {
   insert_configuration("CHECK_UPDATE",$update,$main_error);
   $update_conf=true;
} else {
   $update = get_configuration("CHECK_UPDATE",$main_error);
}

if((isset($color_humidity))&&(!empty($color_humidity))) {
    insert_configuration("COLOR_HUMIDITY_GRAPH",$color_humidity,$main_error);
    $update_conf=true;
} else {
    $color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$main_error);
}


if((isset($color_temperature))&&(!empty($color_temperature))) {
    insert_configuration("COLOR_TEMPERATURE_GRAPH",$color_temperature,$main_error);
    $update_conf=true;
} else {
    $color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error);
}

if((isset($color_water))&&(!empty($color_water))) {
    insert_configuration("COLOR_WATER_GRAPH",$color_water,$main_error);
    $update_conf=true;
} else {
    $color_water = get_configuration("COLOR_WATER_GRAPH",$main_error);
}

if((isset($color_level))&&(!empty($color_level))) {
    insert_configuration("COLOR_LEVEL_GRAPH",$color_level,$main_error);
    $update_conf=true;
} else {
    $color_level = get_configuration("COLOR_LEVEL_GRAPH",$main_error);
}

if((isset($color_ph))&&(!empty($color_ph))) {
    insert_configuration("COLOR_PH_GRAPH",$color_ph,$main_error);
    $update_conf=true;
} else {
    $color_ph = get_configuration("COLOR_PH_GRAPH",$main_error);
}

if((isset($color_ec))&&(!empty($color_ec))) {
    insert_configuration("COLOR_EC_GRAPH",$color_ec,$main_error);
    $update_conf=true;
} else {
    $color_ec = get_configuration("COLOR_EC_GRAPH",$main_error);
}

if((isset($color_od))&&(!empty($color_od))) {
    insert_configuration("COLOR_OD_GRAPH",$color_od,$main_error);
    $update_conf=true;
} else {
    $color_od = get_configuration("COLOR_OD_GRAPH",$main_error);
}

if((isset($color_orp))&&(!empty($color_orp))) {
    insert_configuration("COLOR_ORP_GRAPH",$color_orp,$main_error);
    $update_conf=true;
} else {
    $color_orp = get_configuration("COLOR_ORP_GRAPH",$main_error);
}

if((isset($color_power))&&(!empty($color_power))) {
   insert_configuration("COLOR_POWER_GRAPH",$color_power,$main_error);
   $update_conf=true;
} else {
   $color_power = get_configuration("COLOR_POWER_GRAPH",$main_error);
}

if((isset($color_cost))&&(!empty($color_cost))) {
   insert_configuration("COLOR_COST_GRAPH",$color_cost,$main_error);
   $update_conf=true;
} else {
   $color_cost = get_configuration("COLOR_COST_GRAPH",$main_error);
}


if((isset($record_frequency))&&(!empty($record_frequency))) {
    insert_configuration("RECORD_FREQUENCY",$record_frequency,$main_error);
    $update_conf=true;
} else {
    $record_frequency = get_configuration("RECORD_FREQUENCY",$main_error);
}

if((isset($power_frequency))&&(!empty($power_frequency))) {
    insert_configuration("POWER_FREQUENCY",$power_frequency,$main_error);
    $update_conf=true;
} else {
    $power_frequency = get_configuration("POWER_FREQUENCY",$main_error);
}


if((isset($nb_plugs))&&(!empty($nb_plugs))) {
    insert_configuration("NB_PLUGS",$nb_plugs,$main_error);
    $update_conf=true;
} else {
    $nb_plugs = get_configuration("NB_PLUGS",$main_error);
}

if(!empty($update_frequency)) {
    insert_configuration("UPDATE_PLUGS_FREQUENCY",$update_frequency,$main_error);
    $update_conf=true;
} else {
    $update_frequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$main_error);
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


if(strcmp("$wifi_enable","")!=0) {
       insert_configuration("WIFI","$wifi_enable",$main_error);
       if($wifi_enable) {
           insert_configuration("WIFI_SSID","$wifi_ssid",$main_error);
           insert_configuration("WIFI_KEY_TYPE","$wifi_key_type",$main_error);
           if(strcmp($wifi_password,"")!=0) {
               insert_configuration("WIFI_PASSWORD","$wifi_password",$main_error);
           }
                
           if($wifi_manual) {
               insert_configuration("WIFI_IP","$wifi_ip",$main_error);
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

       if((!empty($sd_card))&&(isset($sd_card))) {
               $wifi_conf=create_wificonf_from_database($main_error,get_ip_address());
               if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
                   $main_error[]=__('ERROR_WIFI_CONF');
               }
       }
} 

$wifi_enable = get_configuration("WIFI",$main_error);
$wifi_ssid=get_configuration("WIFI_SSID",$main_error);
$wifi_key_type=get_configuration("WIFI_KEY_TYPE",$main_error);
$wifi_manual=get_configuration("WIFI_IP_MANUAL",$main_error);
$wifi_password=get_configuration("WIFI_PASSWORD",$main_error);
$wifi_ip=get_configuration("WIFI_IP",$main_error);

if((isset($stats))&&(!empty($stats))) {
    insert_configuration("STATISTICS","$stats",$main_error);
    $update_conf=true;
} else {
    $stats = get_configuration("STATISTICS",$main_error);
}


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


if((isset($second_regul))&&(!empty($second_regul))) {
    insert_configuration("SECOND_REGUL","$second_regul",$main_error);
    $update_conf=true;
} else {
    $second_regul = get_configuration("SECOND_REGUL",$main_error);
}


if((isset($show_cost))&&(!empty($show_cost))) {
    insert_configuration("SHOW_COST","$show_cost",$main_error);
    $update_conf=true;
    $update_menu=true;
} else {
    $show_cost = get_configuration("SHOW_COST",$main_error);
}


if((isset($minmax))&&(!empty($minmax))) {
    insert_configuration("RESET_MINMAX","$minmax",$main_error);
    $update_conf=true;
} else {
    $minmax = get_configuration("RESET_MINMAX",$main_error);
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
        if(!write_sd_conf_file($sd_card,$record_frequency,$updateFrequencyCorrected,$power_frequency,"$alarm_enable","$alarm_value","$minmax",$rtc_offset_computed,$main_error)) {
            $main_error[]=__('ERROR_WRITE_SD_CONF');
        }
    } 
    else
    {
        $main_error[]=__('ERROR_SD_CARD_CONF')." <img src='main/libs/img/infos.png' alt='' title='".__('TOOLTIP_WITHOUT_SD')."' />";
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
