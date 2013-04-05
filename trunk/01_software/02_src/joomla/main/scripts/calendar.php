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


// ================= VARIABLES ================= //
$main_error=array();
$main_info=array();
$informations = Array();
$program="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);



// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
$lang=$_SESSION['LANG'];
__('LANG');


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    $program=create_program_from_database($main_error);
    if(check_sd_card($sd_card)) {
        if(!compare_program($program,$sd_card)) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            save_program_on_sd($sd_card,$program,$main_error);
            set_historic_value(__('UPDATED_PROGRAM')." (".__('CALENDAR_PAGE').")","histo_info",$main_error);
        }
        check_and_copy_firm($sd_card,$main_error);
        check_and_copy_log($sd_card,$main_error);
    } else {
        $main_error[]=__('ERROR_WRITE_PROGRAM');
    }
} else {
        $main_error[]=__('ERROR_SD_CARD');
}


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
$informations["nb_reboot"]=0;
$informations["last_reboot"]="";
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["emeteur_version"]="";
$informations["sensor_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";

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


// Part for the calendar: if a cultibox SD card is present, the 'calendar' is updated into this SD card
if((isset($sd_card))&&(!empty($sd_card))) {
   $data=create_calendar_from_database($main_error);
   if(count($data)>0) {
      if(!check_sd_card($sd_card)) {
            $main_error[]=__('ERROR_WRITE_CALENDAR');
      } else {
            write_calendar($sd_card,$data,$main_error);
      }
   }
   $main_info[]=__('INFO_SD_CARD').": $sd_card";
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


//Display the calendar template
include('main/templates/calendar.html');

?>
