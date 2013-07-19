<?php

if (!isset($_SESSION)) {
   session_start();
}

// Compute page time loading for debug option
$start_load = getmicrotime();



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
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');


// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$several_sensor=get_configuration("REGUL_SENSOR",$main_error);
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
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$selected_error="";
$second_regul=get_configuration("SECOND_REGUL",$main_error);
$jumpto=getvar("jumpto");
$submit=getvar("submit_plugs");

$main_info[]=__('WIZARD_ENABLE_FUNCTION').": <a href='wizard-".$_SESSION['SHORTLANG']."'><img src='../../main/libs/img/wizard.png' alt='".__('WIZARD')."' title='' id='wizard' /></a>";


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


// Setting default value for selected_plug variable
if((!isset($selected_plug))||(empty($selected_plug))) {
   $selected_plug=1;
}


//Reset a program if selected by the user (button reset)
/*
if((isset($reset))&&(!empty($reset))) {
   reset_plug_identificator($main_error);
   $reset=true;
} 
*/

for($nb=1;$nb<=$nb_plugs;$nb++) {
   //$id=getvar("plug_id${nb}");
   $name=getvar("plug_name${nb}");
   $type=getvar("plug_type${nb}");
   $tolerance=getvar("plug_tolerance{$nb}");
   $plug_update=false;
   $power=getvar("plug_power${nb}");
    
   if((strcmp("$type","lamp")==0)||(strcmp("$type","other")==0)) {
        $regul="False";
   } else {
        $regul=getvar("plug_regul${nb}");
        $regul_senss=getvar("plug_senss${nb}");
        $regul_value=getvar("plug_regul_value${nb}");
        $regul_value=str_replace(',','.',$regul_value);
        $regul_value=str_replace(' ','',$regul_value);
   }
   
   $enable=getvar("plug_enable${nb}");
   $power_max=getvar("plug_power_max${nb}");
   $sensor=getvar("plug_sensor${nb}");


   $old_name=get_plug_conf("PLUG_NAME",$nb,$main_error);
   $old_type=get_plug_conf("PLUG_TYPE",$nb,$main_error);
   $old_tolerance=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
   //$old_id=get_plug_conf("PLUG_ID",$nb,$main_error);
   $old_power=get_plug_conf("PLUG_POWER",$nb,$main_error);
   $old_regul=get_plug_conf("PLUG_REGUL",$nb,$main_error);
   $old_senso=get_plug_conf("PLUG_SENSO",$nb,$main_error);
   $old_senss=get_plug_conf("PLUG_SENSS",$nb,$main_error);
   $old_regul_value=get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);
   $old_enable=get_plug_conf("PLUG_ENABLED",$nb,$main_error);
   $old_power_max=get_plug_conf("PLUG_POWER_MAX",$nb,$main_error);
   $old_sensor=get_plug_conf("PLUG_REGUL_SENSOR",$nb,$main_error);

   /* 
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
                  set_historic_value(__('ERROR_PLUG_ID')." (".__('PLUG_PAGE').")","histo_error",$main_error);
         }
       }
   } 
   */

    if((!empty($enable))&&(isset($enable))&&(strcmp("$enable","$old_enable")!=0)) {
        insert_plug_conf("PLUG_ENABLED",$nb,$enable,$main_error);
        if(strcmp("$enable","True")==0) {
            set_historic_value(__('VALID_ENABLED_PLUG')." (".__('PLUG_PAGE')." - ".__('PLUG_HISTO_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
        } else {
            set_historic_value(__('VALID_DISABLED_PLUG')." (".__('PLUG_PAGE')." - ".__('PLUG_HISTO_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
        }
        $update_program=true;
        $plug_update=true;
    }    

    if((!empty($enable))&&(isset($enable))&&(strcmp("$enable","True")==0)) {
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

  
   /* 
   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$main_error);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }
   */

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
                insert_plug_conf("PLUG_POWER",$nb,"",$main_error);
                $update_program=true;
                $plug_update=true;
            }
        }

        if((!empty($power_max))&&(isset($power_max))&&(!$reset)&&(strcmp("$old_power_max","$power_max")!=0)) {
            insert_plug_conf("PLUG_POWER_MAX",$nb,$power_max,$main_error);
            $update_program=true;
            $plug_update=true;
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


        if((strcmp($type,"other")==0)||(strcmp($type,"lamp")==0)) {
            $regul_senso=getvar("plug_senso${nb}");
        } elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)) {
            $regul_senso="H";
        } elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
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

       if((!empty($sensor))&&(isset($sensor))&&(!$reset)&&(strcmp("$old_sensor","$sensor")!=0)) {
           insert_plug_conf("PLUG_REGUL_SENSOR",$nb,"$sensor",$main_error);
           $update_program=true;
           $plug_update=true;
       }
    }


   if(!empty($error[$nb])) {
        foreach($error[$nb] as $err) {
	        $pop_up_error_message=$pop_up_error_message.popup_message($err);
        }

        if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)) {
            insert_plug_conf("PLUG_TOLERANCE",$nb,"$old_tolerance",$main_error);
        } else {
            insert_plug_conf("PLUG_TOLERANCE",0,"$old_tolerance",$main_error);
        }
        insert_plug_conf("PLUG_TYPE",$nb,"$old_type",$main_error); 
        insert_plug_conf("PLUG_NAME",$nb,"$old_name",$main_error);
        //insert_plug_conf("PLUG_ID",$nb,"$old_id",$main_error);
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
   $plug_enable{$nb}=get_plug_conf("PLUG_ENABLED",$nb,$main_error);
   $plug_power_max{$nb}=get_plug_conf("PLUG_POWER_MAX",$nb,$main_error);
   $plug_sensor{$nb}=get_plug_conf("PLUG_REGUL_SENSOR",$nb,$main_error);

   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
}

if((!empty($selected_error))&&(strcmp("$selected_plug","all")!=0)) { 
    $selected_plug=$selected_error;
}


if(($update_program)&&(count($error)==0)&&(!$count_err)) {
          $pop_up_message=$pop_up_message.popup_message(__('VALID_UPDATE_CONF'));
          $main_info[]=__('VALID_UPDATE_CONF');
          set_historic_value(__('VALID_UPDATE_CONF')." (".__('PLUG_PAGE').")","histo_info",$main_error);
} 

// Write file plug01 plug02...
if((isset($sd_card))&&(!empty($sd_card))) {
    if((isset($submit))&&(!empty($submit))) {
   // build conf plug array
       $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
       if(count($plugconf)>0) {
            if(!check_sd_card($sd_card)) {
                $main_error[]=__('ERROR_WRITE_SD_PLUGCONF');
            } else {
                write_plugconf($plugconf,$sd_card);
            } 
        }

        //write pluga file
        if(!check_sd_card($sd_card)) {
            $main_error[]=__('ERROR_WRITE_SD_PLUGA');
        } else {
            write_pluga($sd_card,$main_error);
        }
   }
}


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
    if($sock = @fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
      $ret=array();
      check_update_available($version,$ret,$main_error);
      foreach($ret as $file) {
                $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a class='download'  href=".$file[2].">".$file[1]."</a>";
      }
   } else {
    $main_error[]=__('ERROR_REMOTE_SITE');
   }
}

// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
$informations=array();
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";


if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    if(strcmp($informations["log"],"")!=0) {
        clean_log_file("$sd_card/log.txt");
    }
}


if((isset($stats))&&(!empty($stats))&&(strcmp("$stats","True")==0)) {
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

    if(strcmp($informations["log"],"")==0) {
        $informations["log"]=get_informations("log");
    } else {
        insert_informations("log",$informations["log"]);
    }
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    $conf_uptodate=true;
    if(check_sd_card($sd_card)) {
        $program=create_program_from_database($main_error);

        if(!compare_program($program,$sd_card)) {
            $conf_uptodate=false;
            save_program_on_sd($sd_card,$program,$main_error);
        }

        if(check_and_copy_firm($sd_card,$main_error)) {
            $conf_uptodate=false;
        }


        if((!isset($submit))||(empty($submit))) {
            if(!compare_pluga($sd_card)) {
                $conf_uptodate=false;
                write_pluga($sd_card,$main_error);
            }


            $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
            if(count($plugconf)>0) {
                if(!compare_plugconf($plugconf,$sd_card)) {
                    $conf_uptodate=false;
                    write_plugconf($plugconf,$sd_card);
                }
            }
        }

        if(!check_and_copy_log($sd_card)) {
            $main_error[]=__('ERROR_COPY_TPL');
        }

        if(!check_and_copy_index($sd_card)) {
            $main_error[]=__('ERROR_COPY_FILE');
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
            write_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,"$alarmenable","$alarmvalue","$resetvalue",$main_error);
        }

        if(!$conf_uptodate) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            set_historic_value(__('UPDATED_PROGRAM')." (".__('PLUG_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_PROGRAM');
    }
} else {
        $main_error[]=__('ERROR_SD_CARD_CONF');
}



if((isset($jumpto))&&(!empty($jumpto))) {
    if((!isset($pop_up_error_message))||(empty($pop_up_error_message))) {
        $url="./programs-".$_SESSION['SHORTLANG']."?selected_plug=".$jumpto;
        header("Refresh: 0;url=$url");
    }
}

// Retrieve plug's informations from the database
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);

//Display the plug template
include('main/templates/plugs.html');


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
