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
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');


// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$advanced_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
$update_program=false;
$reset=getvar('reset');
$reccord=getvar('reccord');
$pop_up_message="";
$pop_up_error_message="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$second_regul=get_configuration("SECOND_REGUL",$main_error);
$jumpto=getvar("jumpto");
$submit=getvar("submit_plugs");
$jumpwizard=getvar("jumpwizard");
$submenu=getvar("submenu",$main_error);

// By default the expanded menu is the plug1 menu
if((!isset($submenu))||(empty($submenu))) {
        if(isset($_SESSION['submenu'])) {
            $submenu=$_SESSION['submenu'];
            unset($_SESSION['submenu']);
        } else {
            $submenu="1";
        }
}


if((isset($jumpwizard))&&(!empty($jumpwizard))) {
       $url="./wizard-".$_SESSION['SHORTLANG']."?selected_plug=".$jumpwizard;
       header("Location: $url");
}

$main_info[]=__('WIZARD_ENABLE_FUNCTION').": <a href='wizard-".$_SESSION['SHORTLANG']."'><img src='../../main/libs/img/wizard.png' alt='".__('WIZARD')."' title='' id='wizard' /></a>";


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
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
   $compute_method=getvar("plug_compute_method${nb}");
    
   if((strcmp("$type","lamp")==0)||(strcmp("$type","other")==0)) {
        $regul="False";
   } else {
        $regul=getvar("plug_regul${nb}");
        $regul_senss=getvar("plug_senss${nb}");
        $regul_value=getvar("plug_regul_value${nb}");
        $regul_value=str_replace(',','.',$regul_value);
        $regul_value=str_replace(' ','',$regul_value);
        $second_tol=getvar("plug_second_tolerance${nb}");
        $second_tol=str_replace(',','.',$second_tol);
   }
   
   $enable=getvar("plug_enable${nb}");
   $power_max=getvar("plug_power_max${nb}");
   if(strcmp($power_max,"VARIO")==0) {
       $power_max=getvar("dimmer_canal${nb}"); 
   }

   $sensor="";
   for($j=1;$j<=$GLOBALS['NB_MAX_SENSOR_PLUG'];$j++) { 
       $tmp_sensor=getvar("plug_sensor${nb}${j}");
       if(strcmp($tmp_sensor,"True")==0) {
            if(strcmp($sensor,"")!=0) {
                $sensor=$sensor."-".$j;
            } else {
                $sensor="$j";
            }
        }
   }

   if($sensor=="") {
        $sensor="1";
   }

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
   $old_second_tol=get_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$main_error);
   $old_compute_method=get_plug_conf("COMPUTE_METHOD",$nb,$main_error);

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
            set_historic_value(__('VALID_ENABLED_PLUG')." (".__('PLUG_PAGE').")","histo_info",$main_error);
        } else {
            set_historic_value(__('VALID_DISABLED_PLUG')." (".__('PLUG_PAGE').")","histo_info",$main_error);
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

        if((strcmp($type,"heating")==0)||(strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)||(strcmp($type,"ventilator")==0)||(strcmp($type,"pump")==0)) {
                insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$main_error);
                $update_program=true;
                $plug_update=true;
        } 

  
   /* 
   $plug_id{$nb}=get_plug_conf("PLUG_ID",$nb,$main_error);
   if((empty($plug_id{$nb}))||(!isset($plug_id{$nb}))) {
            $plug_id{$nb}=$GLOBALS['PLUGA_DEFAULT'][$nb-1];
   }
   */

        if((!empty($power))&&(isset($power))&&(!$reset)&&(strcmp("$old_power","$power")!=0)) {
      	        insert_plug_conf("PLUG_POWER",$nb,$power,$main_error);
      	        $update_program=true;
      	        $plug_update=true;
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


        if((!empty($regul_senss))&&(isset($regul_senss))&&(!$reset)&&(strcmp("$old_senss","$regul_senss")!=0)&&(strcmp($second_regul,"True")==0)) {
            insert_plug_conf("PLUG_SENSS",$nb,"$regul_senss",$main_error);
            $update_program=true;
            $plug_update=true;
        }

        if((!empty($second_tol))&&(isset($second_tol))&&(!$reset)&&(strcmp("$old_second_tol","$second_tol")!=0)&&(strcmp($second_regul,"True")==0)) {
            insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"$second_tol",$main_error);
            $update_program=true;
            $plug_update=true;
        }



        if((strcmp($type,"other")==0)||(strcmp($type,"lamp")==0)) {
            $regul_senso=getvar("plug_senso${nb}");
        } elseif((strcmp($type,"heating")==0)||(strcmp($type,"ventilator")==0)||(strcmp($type,"pump")==0)) {
            $regul_senso="H";
        } elseif((strcmp($type,"humidifier")==0)||(strcmp($type,"dehumidifier")==0)) {
            $regul_senso="T";
        } else {
            $regul_senso="";
        }

        if((!empty($regul_senso))&&(isset($regul_senso))&&(!$reset)&&(strcmp("$old_senso","$regul_senso")!=0)&&(strcmp($second_regul,"True")==0)) {
            insert_plug_conf("PLUG_SENSO",$nb,"$regul_senso",$main_error);
            $update_program=true;
            $plug_update=true;
        }


        if((!empty($regul_value))&&(isset($regul_value))&&(!$reset)&&(strcmp("$old_regul_value","$regul_value")!=0)&&(strcmp($second_regul,"True")==0)) {
            insert_plug_conf("PLUG_REGUL_VALUE",$nb,"$regul_value",$main_error);
            $update_program=true;
            $plug_update=true;
       }

       if((!empty($sensor))&&(isset($sensor))&&(!$reset)&&(strcmp("$old_sensor","$sensor")!=0)) {
           insert_plug_conf("PLUG_REGUL_SENSOR",$nb,"$sensor",$main_error);
           $update_program=true;
           $plug_update=true;
       }

       if((!empty($compute_method))&&(isset($compute_method))&&(!$reset)&&(strcmp("$old_compute_method","$compute_method")!=0)) {
           insert_plug_conf("PLUG_COMPUTE_METHOD",$nb,"$compute_method",$main_error);
           $update_program=true;
           $plug_update=true;
       }
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
   $plug_tolerance{$nb}=get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
   $plug_second_tolerance{$nb}=get_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$main_error); 
   $plug_compute_method{$nb}=get_plug_conf("PLUG_COMPUTE_METHOD",$nb,$main_error);

   $plug_sensor[$nb]=get_plug_regul_sensor($nb,$main_error);

}


if($update_program) {
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
                if(!write_plugconf($plugconf,$sd_card)) {
                    $main_error[]=__('ERROR_WRITE_SD_PLUGCONF');    
                }
            } 
        }

        //write pluga file
        if(!check_sd_card($sd_card)) {
            $main_error[]=__('ERROR_WRITE_SD_PLUGA');
        } else {
            if(!write_pluga($sd_card,$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_PLUGA');
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


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
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

        if(!check_and_copy_id($sd_card,get_informations("cbx_id"))) {
            $conf_uptodate=false;
        }
 
        if(!check_and_copy_index($sd_card)) {
            $main_error[]=__('ERROR_COPY_INDEX');
            $error_copy=true;
        }

        $wifi_conf=create_wificonf_from_database($main_error,get_ip_address());
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
            set_historic_value(__('UPDATED_PROGRAM')." (".__('PLUG_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_SD');
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    }
} else {
        $main_error[]=__('ERROR_SD_CARD');
}


if((isset($jumpto))&&(!empty($jumpto))) {
    if((!isset($pop_up_error_message))||(empty($pop_up_error_message))) {
        $url="./programs-".$_SESSION['SHORTLANG']."?selected_plug=".$jumpto;
        header("Refresh: 0;url=$url");
    }
}

// Retrieve plug's informations from the database
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$status=get_canal_status($main_error);


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
