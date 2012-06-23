<?php

if (!isset($_SESSION)) {
   session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

$error="";
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$error);
$update_program=false;
$reset=getvar('reset');


if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   save_program_on_sd($sd_card,$program,$error,$info);
} else {
        $error=$error.__('ERROR_SD_CARD_CONF');
}

$info=$info.__('WIZARD_ENABLE_FUNCTION');


if((isset($reset))&&(!empty($reset))) {
   reset_plug_identificator($error);
   $reset=true;
} 

for($nb=1;$nb<=$nb_plugs;$nb++) {
   $id=getvar("plug_id${nb}");
   $name=getvar("plug_name${nb}");
   $type=getvar("plug_type${nb}");
   $tolerance=getvar("plug_tolerance{$nb}");

   if((!empty($id))&&(isset($id))&&(!$reset)) {
      if(check_numeric_value($id)) {
         while(strlen("$id")<3) {
            $id="0$id";
         }
         insert_plug_conf("PLUG_ID",$nb,"$id",$error);
         $update_program=true;
      } 
   } else {
         insert_plug_conf("PLUG_ID",$nb,"",$error);
   }

   if((!empty($name))&&(isset($name))&&(!$reset)) {
      insert_plug_conf("PLUG_NAME",$nb,$name,$error);
      $update_program=true;
   }
   

   if((!empty($type))&&(isset($type))&&(!$reset)) {
      insert_plug_conf("PLUG_TYPE",$nb,$type,$error);
      $update_program=true;
   }

   if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
      if((!empty($tolerance))&&(isset($tolerance))&&(!$reset)) {
         if(check_tolerance_value($type,$tolerance,$error)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$error);
            $update_program=true;
         }
      }
   } else {
      insert_plug_conf("PLUG_TOLERANCE",$nb,0,$error);
   } 

   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$error);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }


   $plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$error);

   $test=get_plug_conf("PLUG_NAME",$nb,$error);

   $plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$error);
   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$error);
  

}



if(($update_program)&&(empty($error))) {
   $info=$info.__('VALID_UPDATE_CONF');
}

// Write file plug01 plug02...
if((isset($sd_card))&&(!empty($sd_card))) {
   // Display at screen that SD card is available
   $info=$info.__('INFO_SD_CARD').": $sd_card";
   // build conf plug array
   $plugconf=create_plugconf_from_database(17,$error);
   if(count($plugconf)>0) {
      write_plugconf($plugconf,$sd_card,$error);
   }
   //write pluga file
   write_pluga($sd_card,$error);
   check_and_copy_firm($sd_card,$error);
}


include('main/templates/plugs.html');

?>
