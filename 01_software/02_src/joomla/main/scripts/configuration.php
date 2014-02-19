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
$info=array();
$main_info=array();

$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');



// ================= VARIABLES ================= //
$color_humidity = getvar('color_humidity');
$color_temperature = getvar('color_temperature');
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
$update=getvar('update');
$version=get_configuration("VERSION",$main_error);
$submenu=getvar("submenu",$main_error);
$stats=getvar("stats",$main_error);
$advanced_regul=getvar("advanced_regul",$main_error);
$second_regul=getvar("second_regul",$main_error);
$show_cost=getvar("show_cost",$main_error);
$show_historic=getvar("show_historic",$main_error);
$submit=getvar("submit_conf_value",$main_error);
$update_menu=false;
$minmax=getvar("minmax",$main_error);
$wifi_manual=getvar("wifi_ip_manual");




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


// If a cultibox SD card is plugged, manage some administrators operations: check the firmware and log.txt files, check if 'programs' are up tp date...
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
            set_historic_value(__('UPDATED_PROGRAM')." (".__('CONFIGURATION_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_SD');
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    }
} 


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
           insert_configuration("WIFI_PASSWORD","$wifi_password",$main_error);
           if($wifi_manual) {
               insert_configuration("WIFI_IP","$wifi_ip",$main_error);
               insert_configuration("WIFI_IP_MANUAL","1",$main_error);
           } else {
               insert_configuration("WIFI_IP_MANUAL","0",$main_error);    
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
               $wifi_conf=create_wificonf_from_database($main_error);
               if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
                   $main_error[]=__('ERROR_WIFI_CONF');
               }
       }
} else {
       $wifi_enable = get_configuration("WIFI",$main_error);
       $wifi_ssid=get_configuration("WIFI_SSID",$main_error);
       $wifi_key_type=get_configuration("WIFI_KEY_TYPE",$main_error);
       $wifi_password=get_configuration("WIFI_PASSWORD",$main_error);
       $wifi_ip=get_configuration("WIFI_IP",$main_error);
}

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


if((isset($show_historic))&&(!empty($show_historic))) {
        insert_configuration("SHOW_HISTORIC","$show_historic",$main_error);
        $update_conf=true;
        $update_menu=true;
} else {
        $show_historic = get_configuration("SHOW_HISTORIC",$main_error);
}


if((isset($minmax))&&(!empty($minmax))) {
    insert_configuration("RESET_MINMAX","$minmax",$main_error);
    $update_conf=true;
} else {
        $minmax = get_configuration("RESET_MINMAX",$main_error);
}


// Is a field has been changed and there is no error in the value: display success message
if(empty($main_error)) {
	if($update_conf) {
        if((!empty($sd_card))&&(isset($sd_card))) {
			   $main_info[]=__('VALID_UPDATE_CONF');
               set_historic_value(__('VALID_UPDATE_CONF')." (".__('CONFIGURATION_PAGE').")","histo_info",$main_error);
			   $pop_up_message=$pop_up_message.popup_message(__('VALID_UPDATE_CONF'));
         } else {
            $main_info[]=__('VALID_UPDATE_CONF_WITHOUT_SD');
            set_historic_value(__('VALID_UPDATE_CONF_WITHOUT_SD')." (".__('CONFIGURATION_PAGE').")","histo_info",$main_error);
            $pop_up_message=$pop_up_message.popup_message(__('VALID_UPDATE_CONF_WITHOUT_SD'));
         }
	}
}

if((!isset($sd_card))||(empty($sd_card))) {
    $main_error[]=__('ERROR_SD_CARD_CONF')." <img src=\"main/libs/img/infos.png\" alt=\"\" title=\"".__('TOOLTIP_WITHOUT_SD')."\" />";
}



// Change files on the cultibox SD card after the configuration has been updated: plug's frequency, alarm value etc...
if((isset($submit))&&(!empty($submit))) {
    if((isset($sd_card))&&(!empty($sd_card))) {
	    if("$update_frequency"=="-1") {
		    if(!write_sd_conf_file($sd_card,$record_frequency,"0",$power_frequency,"$alarm_enable","$alarm_value","$minmax",$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_CONF');
            }
	    } else {
		    if(!write_sd_conf_file($sd_card,$record_frequency,$update_frequency,$power_frequency,"$alarm_enable","$alarm_value","$minmax",$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_CONF');
            }
	    }	
    }
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
