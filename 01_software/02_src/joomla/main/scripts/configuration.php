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


// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$info=array();
$main_info=array();

$lang=getvar('lang');
if((isset($lang))&&(!empty($lang))) {
    insert_configuration("LANG",$lang,$main_error);
} else {
    $lang=get_configuration("LANG",$main_error);
}

set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
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
$alarm_senso=getvar('alarm_senso');
$alarm_senss=getvar('alarm_senss');
$update=getvar('update');
$program="";
$version=get_configuration("VERSION",$main_error);
$submenu=getvar("submenu",$main_error);
$stats=getvar("stats",$main_error);
$second_regul=getvar("second_regul",$main_error);

if(isset($_SESSION['reset_sd_card'])) {
    $reset_sd_card=$_SESSION['reset_sd_card'];
}

if(isset($_SESSION['selected_hdd'])) {
    $selected_hdd=$_SESSION['selected_hdd'];
}




// By default the expanded menu is the user interface menu
if((!isset($submenu))||(empty($submenu))) {
        if(isset($_SESSION['submenu'])) {
            $submenu=$_SESSION['submenu'];
            unset($_SESSION['submenu']);
        } else {
            $submenu="user_interface";
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
} else {
        $alarm_enable = get_configuration("ALARM_ACTIV",$main_error);
}

if(!empty($alarm_value)) {
	if((check_numeric_value("$alarm_value"))&&(check_alarm_value("$alarm_value"))) {
           insert_configuration("ALARM_VALUE","$alarm_value",$main_error);
           $update_conf=true;
        } else {
           $alarm_value=get_configuration("ALARM_VALUE",$main_error);
           $error['alarm']=__('ERROR_ALARM_VALUE');
           $pop_up_error_message=$pop_up_error_message.popup_message($error['alarm']);
           $submenu="alarm_interface";
        }
} else {
        $alarm_value = get_configuration("ALARM_VALUE",$main_error);
}

if(!empty($alarm_senso)) {
        insert_configuration("ALARM_SENSO","$alarm_senso",$main_error);
        $update_conf=true;
} else {
        $alarm_senso = get_configuration("ALARM_SENSO",$main_error);
}

if(!empty($alarm_senss)) {
        insert_configuration("ALARM_SENSS","$alarm_senss",$main_error);
        $update_conf=true;
} else {
        $alarm_senss = get_configuration("ALARM_SENSS",$main_error);
}


if((isset($stats))&&(!empty($stats))) {
        insert_configuration("STATISTICS","$stats",$main_error);
        $update_conf=true;
} else {
        $stats = get_configuration("STATISTICS",$main_error);
}


if((isset($second_regul))&&(!empty($second_regul))) {
        insert_configuration("SECOND_REGUL","$second_regul",$main_error);
        $update_conf=true;
} else {
        $second_regul = get_configuration("SECOND_REGUL",$main_error);
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


//Create sd card from the format button
if((!empty($reset_sd_card))&&(isset($reset_sd_card))&&(!empty($selected_hdd))&&(isset($selected_hdd))) {
   if(check_sd_card($selected_hdd)) {
    if(format_sd_card($selected_hdd,$main_error)) {
        $main_info[]=__('VALID_RESET_SD');
        $pop_up_message=$pop_up_message.popup_message(__('VALID_RESET_SD'));
        set_historic_value(__('VALID_RESET_SD')." (".__('CONFIGURATION_PAGE').")","histo_info",$main_error);
    } else {
        $main_error[]=__('ERROR_FORMAT_SD_CARD');
        $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_FORMAT_SD_CARD'));
        set_historic_value(__('ERROR_FORMAT_SD_CARD')." (".__('CONFIGURATION_PAGE').")","histo_error",$main_error);
    }
    $sd_card=get_sd_card();
   } else {
        $main_error[]=__('ERROR_FORMAT_SD_CARD');
        $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_FORMAT_SD_CARD'));
   }
}

unset($_SESSION['reset_sd_card']);
unset($_SESSION['selected_hdd']);


// Is a field has been changed an there is no error in the value: display success message
if(((empty($main_error))||(!isset($main_error)))&&(count($error)==0)) {
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

// If a cultibox SD card is plugged, manage some administrators operations: check the firmware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    if(check_sd_card($sd_card)) {
        $program=create_program_from_database($main_error);
        if(!compare_program($program,$sd_card)) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            save_program_on_sd($sd_card,$program,$main_error);
            set_historic_value(__('UPDATED_PROGRAM')." (".__('CONFIGURATION_PAGE').")","histo_info",$main_error);
        }
        check_and_copy_firm($sd_card,$main_error);
        check_and_copy_log($sd_card,$main_error);
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_PROGRAM');
    }
} else {
    $main_error[]=__('ERROR_SD_CARD_CONF')." <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"".__('TOOLTIP_WITHOUT_SD')."\" />";
}


// Change files on the cultibox SD card after the configuration has been updated: plug's frequency, alarm value etc...
if((isset($sd_card))&&(!empty($sd_card))) {
	if("$update_frequency"=="-1") {
		write_sd_conf_file($sd_card,$record_frequency,"0",$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$main_error);
	} else {
		write_sd_conf_file($sd_card,$record_frequency,$update_frequency,$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$main_error);	
	}	
}


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
    if($sock = @fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
      $ret=array();
      check_update_available($version,$ret,$main_error);
      foreach($ret as $file) {
                $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a href=".$file[2].">".$file[1]."</a>";
      }
   } else {
    $main_error[]=__('ERROR_REMOTE_SITE');
   }
}


$informations = Array();
$informations["nb_reboot"]=0;
$informations["last_reboot"]="";
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["emeteur_version"]="";
$informations["sensor_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    if(strcmp($informations["log"],"")!=0) {
        clean_log_file("$sd_card/log.txt");
    }
}

if((isset($stats))&&(!empty($stats))&&(strcmp("$stats","True")==0)) {
    if(strcmp($informations["nb_reboot"],"0")==0) {
        $informations["nb_reboot"]=get_informations("nb_reboot");
    } else {
        insert_informations("nb_reboot",$informations["nb_reboot"]);
    }

    if(strcmp($informations["last_reboot"],"")==0) {
        $informations["last_reboot"]=get_informations("last_reboot");
    } else {
        insert_informations("last_reboot",$informations["last_reboot"]);
    }

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

    if(strcmp($informations["emeteur_version"],"")==0) {
        $informations["emeteur_version"]=get_informations("emeteur_version");
    } else {
        insert_informations("emeteur_version",$informations["emeteur_version"]);
    }

    if(strcmp($informations["sensor_version"],"")==0) {
        $informations["sensor_version"]=get_informations("sensor_version");
    } else {
        insert_informations("sensor_version",$informations["sensor_version"]);
    }

    if(strcmp($informations["log"],"")==0) {
        $informations["log"]=get_informations("log");
    } else {
        insert_informations("log",$informations["log"]);
    }
}

//Display the configuration template
include('main/templates/configuration.html');

?>
