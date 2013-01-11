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
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$update_program=false;
$reset=getvar('reset');
$reccord=getvar('reccord');
$pop_up=getvar('pop_up');
$selected_plug=getvar('selected_plug');
$pop_up_message="";
$pop_up_error_message="";
$count_err=false;
$program="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$main_info[]=__('WIZARD_ENABLE_FUNCTION');
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$selected_error="";


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


// Setting default value for selected_plug variable
if((!isset($selected_plug))||(empty($selected_plug))) {
   $selected_plug=1;
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($main_error);
   if(!compare_program($program,$sd_card)) {
      $main_info[]=__('UPDATED_PROGRAM');
      $pop_up_message=$pop_up_message.clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$main_error);
   }
   check_and_copy_firm($sd_card,$main_error);
   check_and_copy_log($sd_card,$main_error);
   $main_info[]=__('INFO_SD_CARD').": $sd_card";
} else {
        $main_error[]=__('ERROR_SD_CARD_CONF');
}


//Reset a program if selected by the user (button reset)
if((isset($reset))&&(!empty($reset))) {
   reset_plug_identificator($main_error);
   $reset=true;
} 

for($nb=1;$nb<=$nb_plugs;$nb++) {
   $id=getvar("plug_id${nb}");
   $name=getvar("plug_name${nb}");
   $type=getvar("plug_type${nb}");
   $tolerance=getvar("plug_tolerance{$nb}");
   $plug_update=false;
   $power=getvar("plug_power${nb}");
   $regul=getvar("plug_regul${nb}");
   $regul_senss=getvar("plug_senss${nb}");
   $regul_value=getvar("plug_regul_value${nb}");
   $regul_value=str_replace(',','.',$regul_value);
   $regul_value=str_replace(' ','',$regul_value);


   $old_name=get_plug_conf("PLUG_NAME",$nb,$main_error);
   $old_type=get_plug_conf("PLUG_TYPE",$nb,$main_error);
   $old_tolerance=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
   $old_id=get_plug_conf("PLUG_ID",$nb,$main_error);
   $old_power=get_plug_conf("PLUG_POWER",$nb,$main_error);
   $old_regul=get_plug_conf("PLUG_REGUL",$nb,$main_error);
   $old_senso=get_plug_conf("PLUG_SENSO",$nb,$main_error);
   $old_senss=get_plug_conf("PLUG_SENSS",$nb,$main_error);
   $old_regul_value=get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);

   
   if((!empty($id))&&(isset($id))&&(!$reset)) {
      if(strcmp("$old_id","$id")!=0) {
         if(check_numeric_value($id)) {
            while(strlen("$id")<3) {
               $id="0$id";
            }
            insert_plug_conf("PLUG_ID",$nb,"$id",$main_error);
            $update_program=true;
            $plug_update=true;
         } else {
                  $error[$nb]['plug_id']=__('ERROR_PLUG_ID');
         }
       }
   } 

   if((!empty($name))&&(isset($name))&&(!$reset)&&(strcmp("$old_name","$name")!=0)) {
      $name=mysql_escape_string($name);
      insert_plug_conf("PLUG_NAME",$nb,$name,$main_error);
      $update_program=true;
      $plug_update=true;
   }
   

   if((!empty($type))&&(isset($type))&&(!$reset)&&(strcmp("$old_type","$type")!=0)) {
      insert_plug_conf("PLUG_TYPE",$nb,$type,$main_error);
      $update_program=true;
      $plug_update=true;
   }

   if(((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0))) {
         if(check_tolerance_value($type,$tolerance)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$main_error);
            $update_program=true;
            $plug_update=true;
         } else {
            $error[$nb]['tolerance']=__('ERROR_TOLERANCE_VALUE_DEGREE');
        }
   } 

   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$main_error);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }


   if((!empty($power))&&(isset($power))&&(!$reset)&&(strcmp("$old_power","$power")!=0)) {
      if(check_power_value($power)) {
      	insert_plug_conf("PLUG_POWER",$nb,$power,$main_error);
      	$update_program=true;
      	$plug_update=true;
     } else {
        $error[$nb]['power']=__('ERROR_POWER_VALUE');
     }
   } else {
         if((empty($power))&&(!$reset)&&(!empty($reccord))&&(strcmp("$old_power","$power")!=0)) {
            insert_plug_conf("PLUG_POWER",$nb,0,$main_error);
            $update_program=true;
            $plug_update=true;
         }
   }

   if((!empty($regul))&&(isset($regul))&&(!$reset)&&(strcmp("$old_regul","$regul")!=0)) {
         insert_plug_conf("PLUG_REGUL",$nb,"$regul",$main_error);
         $update_program=true;
         $plug_update=true;
   }

   if((!empty($regul_senss))&&(isset($regul_senss))&&(!$reset)&&(strcmp("$old_senss","$regul_senss")!=0)) {
         insert_plug_conf("PLUG_SENSS",$nb,"$regul_senss",$main_error);
         $update_program=true;
         $plug_update=true;
   }


   if((strcmp($type,"unknown")==0)||(strcmp($type,"lamp")==0)) {
            $regul_senso=getvar("plug_senso${nb}");
   } elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
            $regul_senso="H";
   } elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"deshumidifier")==0)) {
            $regul_senso="T";
   } else {
            $regul_senso="";
   }

   if((!empty($regul_senso))&&(isset($regul_senso))&&(!$reset)&&(strcmp("$old_senso","$regul_senso")!=0)) {
         insert_plug_conf("PLUG_SENSO",$nb,"$regul_senso",$main_error);
         $update_program=true;
         $plug_update=true;
   }

   if((!empty($regul_value))&&(isset($regul_value))&&(!$reset)&&(strcmp("$old_regul_value","$regul_value")!=0)) {
         if(check_regul_value("$regul_value")) { 
            insert_plug_conf("PLUG_REGUL_VALUE",$nb,"$regul_value",$main_error);
            $update_program=true;
            $plug_update=true;
         } else {
            $error[$nb]['regul_value']=__('ERROR_REGUL_VALUE');
        }

   }

   if(!empty($error[$nb])) {
        foreach($error[$nb] as $err) {
	        $pop_up_error_message=$pop_up_error_message.clean_popup_message($err);
        }

        if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,"$old_tolerance",$main_error);
        } else {
            insert_plug_conf("PLUG_TOLERANCE",0,"$old_tolerance",$main_error);
        }
        insert_plug_conf("PLUG_TYPE",$nb,"$old_type",$main_error); 
        insert_plug_conf("PLUG_NAME",$nb,"$old_name",$main_error);
        insert_plug_conf("PLUG_ID",$nb,"$old_id",$main_error);
        $count_err=true;
        $selected_error=$nb;

   } 

   $plug_name{$nb}=get_plug_conf("PLUG_NAME",$nb,$main_error);
   $plug_type{$nb}=get_plug_conf("PLUG_TYPE",$nb,$main_error);
   $plug_power{$nb}=get_plug_conf("PLUG_POWER",$nb,$main_error);
   $plug_regul{$nb}=get_plug_conf("PLUG_REGUL",$nb,$main_error);
   $plug_senso{$nb}=get_plug_conf("PLUG_SENSO",$nb,$main_error);
   $plug_senss{$nb}=get_plug_conf("PLUG_SENSS",$nb,$main_error);
   $plug_regul_value{$nb}=get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);

   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
   if($plug_tolerance{$nb}==0) {
      $plug_tolerance{$nb}="1.0";
   }
}

if((!empty($selected_error))&&(strcmp("$selected_plug","all")!=0)) { 
    $selected_plug=$selected_error;
}


if(($update_program)&&(count($main_error)==0)&&(!$count_err)) {
          $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_CONF'));
          $main_info[]=__('VALID_UPDATE_CONF');
} 

// Write file plug01 plug02...
if((isset($sd_card))&&(!empty($sd_card))) {
   // build conf plug array
   $plugconf=create_plugconf_from_database(17,$main_error);
   if(count($plugconf)>0) {
      write_plugconf($plugconf,$sd_card,$main_error);
   }
   //write pluga file
   write_pluga($sd_card,$main_error);
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
$informations=array();
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


//Display the plug template
include('main/templates/plugs.html');
?>
