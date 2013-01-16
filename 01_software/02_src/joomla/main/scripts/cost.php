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
$main_error=array();
$main_info=array();
$error=array();
$info=array();
$lang=get_configuration("LANG",$main_error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


// ================= VARIABLES ================= //
$startday=getvar('startday');
$endday=getvar('endday');
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$price=get_configuration("COST_PRICE",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$select_plug=getvar('select_plug');
$compute=0;
$pop_up_error_message="";
$pop_up="";
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$cost_type=get_configuration("COST_TYPE",$main_error);
$stats=get_configuration("STATISTICS",$main_error);


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($main_error);
   if(!compare_program($program,$sd_card)) {
      $main_info[]=__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$main_error);
      set_historic_value(__('UPDATED_PROGRAM')." (".__('COST_PAGE').")","histo_info",$main_error);
   }
   check_and_copy_firm($sd_card,$main_error);
   check_and_copy_log($sd_card,$main_error);
   $main_info[]=__('INFO_SD_CARD').": $sd_card";
} else {
        $main_error[]=__('ERROR_SD_CARD');
}


//Setting some default value if they are not configured
if((!isset($select_plug))||(empty($select_plug))) {
   $select_plug="all";
}

if((!isset($startday))||(empty($startday))) {
   $startday=date('Y')."-".date('m')."-".date('d');
} 
$startday=str_replace(' ','',"$startday");

if((!isset($endday))||(empty($endday))) {
   $endday=date('Y')."-".date('m')."-".date('d');
} 
$endday=str_replace(' ','',"$endday");


//Checking value entered by user before performing actions
$check_format=check_format_date($startday,"days");
if(!$check_format) {
      $error['startday']=__('ERROR_FORMAT_DATE_DAY_START');
      $pop_up_error_message=$pop_up_error_message.clean_popup_message($error['startday']);
      $startday=date('Y')."-".date('m')."-".date('d');
} 

$check_format=check_format_date($endday,"days");
if(!$check_format) {
      $error['endday']=__('ERROR_FORMAT_DATE_DAY_END');
      $pop_up_error_message=$pop_up_error_message.clean_popup_message($error['endday']);
      $endday=$startday;
      $check_format=true;
}

if(!check_date("$startday,","$endday")) {
      $startday=date('Y')."-".date('m')."-".date('d');
      $endday=$startday;
      $error['interval']=__('ERROR_DATE_INTERVAL');
      $pop_up_error_message=$pop_up_error_message.clean_popup_message($error['interval']);
}


//Computing cost value:
if(strcmp($select_plug,"distinct_all")!=0) {
    $theorical_power=get_theorical_power($select_plug,$cost_type,$main_error,$check);
    $nb=get_nb_days($startday,$endday)+1;
    $theorical_power=$theorical_power*$nb;

    if(strcmp($select_plug,"all")==0) {
        $title=__('PRICE_SELECT_ALL_PLUG');
        $color_cost = get_configuration("COLOR_COST_GRAPH",$main_error);
    } else {
        $title=$plugs_infos[$select_plug-1]['PLUG_NAME'];
        $color_cost=$GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$select_plug-1];
    }

    $data_power=get_data_power($startday,$endday,$select_plug,$main_error);
    $real_power=get_real_power($data_power,$cost_type,$main_error);

    $data_price[]= array( 
        "number" => "$select_plug",
        "theorical" => "$theorical_power",
        "real" => "$real_power", 
        "title" => "$title",
        "color" => "$color_cost"
    );
} else {
    $nb_plugs = get_configuration("NB_PLUGS",$main_error);
    $nb=get_nb_days($startday,$endday)+1;
    for($i=1;$i<=$nb_plugs;$i++) {
       $theorical_power=get_theorical_power($i,$cost_type,$main_error,$check);
       $theorical_power=$theorical_power*$nb;

       $data_power=get_data_power($startday,$endday,$i,$main_error);
       $real_power=get_real_power($data_power,$cost_type,$main_error);
       $title=$plugs_infos[$i-1]['PLUG_NAME'];

       $data_price[]= array(
        "number" => "$i",
        "real" => "$real_power",
        "theorical" => "$theorical_power",
        "title" => "$title",
        "color" => $GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$i-1]
       ); 
    }
}


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$main_error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                    $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a href=".$file[3]." target='_blank'>".$file[2]."</a>";
               }
            }
      }
}


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
$informations = Array();
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
        clean_big_file("$sd_card/log.txt");
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


//Display the cost template
include('main/templates/cost.html');

?>
