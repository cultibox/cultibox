<?php

if (!isset($_SESSION)) {
   session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$error="";
$info="";
$program="";
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);

if((isset($lang))&&(!empty($lang))) {
   insert_configuration("LANG",$lang,$error);
} else {
   $lang=get_configuration("LANG",$error);
}

set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');



if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   if(!compare_program($program,$sd_card)) {
      $info=$info.__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$error);
   }
   check_and_copy_firm($sd_card,$error);
   check_and_copy_log($sd_card,$error);
} else {
        $error=$error.__('ERROR_SD_CARD_CONF');
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


if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
}

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



if(!isset($pop_up)) {
   $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((isset($sd_card))&&(!empty($sd_card))) {
   $data=create_calendar_from_database($error);
   if(count($data)>0) {
      write_calendar($sd_card,$data,$error);
   }
   $info=$info.__('INFO_SD_CARD').": $sd_card";
}

if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                  $tmp="";
                  $tmp=__('INFO_UPDATE_AVAILABLE');
                  $tmp=str_replace("</li>","<a href=".$file[3]." target='_blank'>".$file[2]."</a></li>",$tmp);
                  $info=$info.$tmp;
               }
            }
      }
}

include('main/templates/calendar.html');

?>
