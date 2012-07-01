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
$error_plug=Array();
$info_plug=Array();
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$error);
$update_program=false;
$reset=getvar('reset');
$pop_up=getvar('pop_up');
$pop_up_message="";
$pop_up_error_message="";
$count_err=false;


if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   save_program_on_sd($sd_card,$program,$error,$info);
} else {
        $error=$error.__('ERROR_SD_CARD_CONF');
}

if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
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
   $plug_update=false;
   $power=getvar("plug_power${nb}");

   $error_plug[$nb]="";
   $old_name=get_plug_conf("PLUG_NAME",$nb,$error_plug[$nb]);
   $old_type=get_plug_conf("PLUG_TYPE",$nb,$error_plug[$nb]);
   $old_tolerance=get_plug_conf("PLUG_TOLERANCE",$nb,$error_plug[$nb]);
   $old_id=get_plug_conf("PLUG_ID",$nb,$error_plug[$nb]);
   $old_power=get_plug_conf("PLUG_POWER",$nb,$error_plug[$nb]);

   

   if((!empty($id))&&(isset($id))&&(!$reset)) {
      if(strcmp("$old_id","$id")!=0) {
         if(check_numeric_value($id)) {
            while(strlen("$id")<3) {
               $id="0$id";
            }
            insert_plug_conf("PLUG_ID",$nb,"$id",$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         } else {
                  $error_plug[$nb]=__('ERROR_PLUG_ID');
         }
       }
   } 

   if((!empty($name))&&(isset($name))&&(!$reset)&&(strcmp("$old_name","$name")!=0)) {
      insert_plug_conf("PLUG_NAME",$nb,$name,$error_plug[$nb]);
      $update_program=true;
      $plug_update=true;
   }
   

   if((!empty($type))&&(isset($type))&&(!$reset)&&(strcmp("$old_type","$type")!=0)) {
      insert_plug_conf("PLUG_TYPE",$nb,$type,$error_plug[$nb]);
      $update_program=true;
      $plug_update=true;
   }

   if(((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0))) {
         if(check_tolerance_value($type,$tolerance,$error_plug[$nb])) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$error_plug[$nb]);
            $update_program=true;
            $plug_update=true;
         } 
   } 

   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$error_plug[$nb]);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }


   if((!empty($power))&&(isset($power))&&(!$reset)&&(strcmp("$old_power","$power")!=0)) {
      if(check_power_value($power,$error_plug[$nb])) {
      	insert_plug_conf("PLUG_POWER",$nb,$power,$error_plug[$nb]);
      	$update_program=true;
      	$plug_update=true;
     }
   }


   if(!empty($error_plug[$nb])) {
	$pop_up_error_message=clean_popup_message($error_plug[$nb]);
        if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,"$old_tolerance",$error_plug[$nb]);
        } else {
            insert_plug_conf("PLUG_TOLERANCE",0,"$old_tolerance",$error_plug[$nb]);
        }
        insert_plug_conf("PLUG_TYPE",$nb,"$old_type",$error_plug[$nb]); 
        insert_plug_conf("PLUG_NAME",$nb,"$old_name",$error_plug[$nb]);
        insert_plug_conf("PLUG_ID",$nb,"$old_id",$error_plug[$nb]);
        $count_err=true;
   } else if($plug_update) {
                if(("$old_name"!="$name")||("$old_type"!="$type")||("$old_tolerance"!="$tolerance")||("$id"!="$old_id")) {
        		$info_plug[$nb]=__('VALID_UPDATE_CONF');
                }
   }

   $plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$error_plug[$nb]);
   $plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$error_plug[$nb]);
   $plug_power{$nb}=get_plug_conf("PLUG_POWER",$nb,$error_plug[$nb]);

   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$error_plug[$nb]);
   if($plug_tolerance{$nb}==0) {
      $plug_tolerance{$nb}="1.0";
   }

}



if(($update_program)&&(empty($error))&&(!$count_err)) {
          $info=$info.__('VALID_UPDATE_CONF');
          $pop_up_message=clean_popup_message(__('VALID_UPDATE_CONF'));
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
