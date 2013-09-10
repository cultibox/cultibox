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
$selected_plug=getvar('selected_plug');
$close=getvar('close');
$program="";
$pop_up_message="";
$pop_up_error_message="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$main_info[]=__('WIZARD_DISABLE_FUNCTION').": <a href='programs-".$_SESSION['SHORTLANG']."'><img src='../../main/libs/img/wizard.png' alt='".__('CLASSIC')."' title='' id='Classic' /></a>";
$type_submit=getvar('type_submit');

$error_value[2]=__('ERROR_VALUE_PROGRAM','html');
$error_value[3]=__('ERROR_VALUE_PROGRAM_TEMP','html');
$error_value[4]=__('ERROR_VALUE_PROGRAM_HUMI','html');
$error_value[5]=__('ERROR_VALUE_PROGRAM','html');


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
    $sd_card=get_sd_card();
}


if((isset($close))&&(!empty($close))) {
    header('Location: programs-'.$_SESSION['SHORTLANG']."?selected_plug=".$selected_plug);
}



// Setting some default values:
if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=1;
}

$chtime="";
$step=getvar('step');
$next=getvar('next');
$previous=getvar('previous');
$start_time="06:00:00";
$end_time="18:00:00";

if($selected_plug==1) {
    $plug_type="lamp";
} else {
    $plug_type=getvar('plug_type');
}

if($selected_plug>3) {
    $plug_power_max=getvar('plug_power_max');
} 

$value_program=getvar('value_program');

if((empty($plug_type))||(!isset($plug_type))) {
    $plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error); 
}

if((empty($plug_power_max))||(!isset($plug_power_max))) {
    $plug_power_max=get_plug_conf("PLUG_POWER_MAX",$selected_plug,$main_error);
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    if((!isset($step))||(empty($step))||(!is_numeric($step))||($step<0)) {
        $conf_uptodate=true;
        if(check_sd_card($sd_card)) {
           /* TO BE DELETED */
           compat_old_sd_card($sd_card);   
           /* ************* */

            $program=create_program_from_database($main_error);

            if(!compare_program($program,$sd_card)) {
                $conf_uptodate=false;
                save_program_on_sd($sd_card,$program,$main_error);
            }

            if(check_and_copy_firm($sd_card,$main_error)) {
                $conf_uptodate=false;
            }

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
                set_historic_value(__('UPDATED_PROGRAM')." (".__('WIZARD_PAGE').")","histo_info",$main_error);
            }
            $main_info[]=__('INFO_SD_CARD').": $sd_card";
        } else {
            $main_error[]=__('ERROR_WRITE_PROGRAM');
        }
    }
} else {
    $main_error[]=__('ERROR_SD_CARD_CONF')." <img src=\"main/libs/img/infos.png\" alt=\"\" title=\"".__('TOOLTIP_WITHOUT_SD')."\" />";
}


if((empty($value_program))||(!isset($value_program))) {
    if((!empty($plug_type))&&(isset($plug_type))) {
        switch ($plug_type) {
            case 'heating':
                $value_program=22.0;
                break;
            case 'ventilator':
                $value_program=22.0;
                break;
            case 'humidifier':
                $value_program=55.0;
                break;
            case 'dehumidifier':
                $value_program=55.0;
                break;
            case 'lamp':
                $value_program=0.0;
                break;
            case 'other' :
                $value_program=0.0;
               break;
        }
    }
}


if((strcmp($type_submit,"submit_close")==0)||(strcmp($type_submit,"submit_next")==0)) {
    $program=getvar('program');

    if((isset($program))&&(!empty($program))) {
        if("$selected_plug"=="1") {
            $value_program="99.9";
            $plug_type="lamp";
            $start_time=getvar('start_time');
            $end_time=getvar('end_time');
        } else {
            $value_program=getvar('value_program');
            $plug_type=getvar('plug_type');
            $start_time=getvar('start_time');
            $end_time=getvar('end_time');
        }

        $chtime=check_times($start_time,$end_time); 

        if(strcmp($plug_type,"lamp")!=0) {
            if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
		        $chval=check_format_values_program($value_program,"temp");
            } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"dehumidifier")==0)) {
                $chval=check_format_values_program($value_program,"humi");
            } else {
                $chval=check_format_values_program($value_program,"other");
            }
            if(strcmp("$chval","1")!=0) {
                $error['value']=$chval; 
            }
        } else {
            $chval="1";
        }
        $plug_tolerance="1.0";

        if(($chtime)&&(strcmp("$chval","1")==0)) {
            if($chtime==2) {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "23:59:59",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type"
                );

                $prog[]= array(
                    "start_time" => "00:00:00",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
					"plug_type" => "$plug_type"
                ); 
            } else {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type"
                );
            }

			clean_program($selected_plug,$main_error);
			if(isset($plug_tolerance)) {
                insert_plug_conf("PLUG_TOLERANCE",$selected_plug,$plug_tolerance,$main_error);
            }

            if(isset($plug_power_max)) {
                insert_plug_conf("PLUG_POWER_MAX",$selected_plug,$plug_power_max,$main_error);
            }

            $chinsert=true;
            if(!insert_program($prog,$main_error)) $chinsert=false;

            if($chinsert) {
               if((!empty($sd_card))&&(isset($sd_card))) {
                    $program=create_program_from_database($main_error);
                    save_program_on_sd($sd_card,$program,$main_error);
                } 
			    insert_plug_conf("PLUG_TYPE",$prog[0]["selected_plug"],$prog[0]["plug_type"],$main_error);
            } else {
                unset($type_submit);
            }

            insert_plug_conf("PLUG_ENABLED",$selected_plug,"True",$main_error);
            if((!empty($sd_card))&&(isset($sd_card))) {
                $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
                if(count($plugconf)>0) {
                    if(!compare_plugconf($plugconf,$sd_card)) {
                        write_plugconf($plugconf,$sd_card);
                    }
                }
            }


            if(count($error)==0) {
                if(($selected_plug==$nb_plugs)||((isset($type_submit))&&(!empty($type_submit))&&(strcmp($type_submit,"submit_close")==0))) {            
                    set_historic_value(__('VALID_UPDATE_PROGRAM')." (".__('WIZARD_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
                    header('Location: programs-'.$_SESSION['SHORTLANG']."?selected_plug=".$selected_plug);
                }
			}

        } else {
            if((isset($error))&&(!empty($error))&&(count($error)>0)) {
                foreach($error as $err) {
                    $pop_up_error_message=$pop_up_error_message.popup_message($err);
                }
            }
        }
    }
    if((isset($type_submit))&&(!empty($type_submit))&&(strcmp($type_submit,"submit_next")==0)&&(count($error)==0)) {
        $selected_plug=$selected_plug+1;
        $step=1;
    }
}

if((!empty($selected_plug))&&(isset($selected_plug))) {
   $plug_name=get_plug_conf("PLUG_NAME",$selected_plug,$main_error);
}


if((!isset($step))||(empty($step))||(!is_numeric($step))||($step<0)) {
    $step=1;
} else if((isset($next))&&(!empty($next))) {
    $step=$step+1;	
} else if((isset($previous))&&(!empty($previous))) {
    $step=$step-1;
}


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
    if($sock=@fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
      if(check_update_available($version,$main_error)) {
        $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
      }
   } else {
    $main_error[]=__('ERROR_REMOTE_SITE');
   }
}


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
$informations = Array();
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


//Display the wizard template
include('main/templates/wizard.html');

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
