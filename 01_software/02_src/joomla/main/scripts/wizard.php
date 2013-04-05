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

$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');



// ================= VARIABLES ================= //
$finish=getvar('finish');
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$selected_plug=getvar('selected_plug');
$next_plug=getvar('next_plug');
$close=getvar('close');
$program="";
$pop_up_message="";
$pop_up_error_message="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$main_info[]=__('WIZARD_DISABLE_FUNCTION');




// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
    $sd_card=get_sd_card();
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
    if(check_sd_card($sd_card)) {
        $program=create_program_from_database($main_error);
        if(!compare_program($program,$sd_card)) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            save_program_on_sd($sd_card,$program,$main_error);
            set_historic_value(__('UPDATED_PROGRAM')." (".__('WIZARD_PAGE').")","histo_info",$main_error);
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


if((isset($close))&&(!empty($close))) {
    header('Location: plugs-'.$_SESSION['SHORTLANG']);
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

$value_program=getvar('value_program');

if((empty($plug_type))||(!isset($plug_type))) {
    $plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error); 
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




if(((isset($finish))&&(!empty($finish)))||((isset($next_plug))&&(!empty($next_plug)))) {
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

        if(!check_format_time($start_time)) {
            $error['start_time']=__('ERROR_FORMAT_TIME_START');         
        }

                
        if(!check_format_time($end_time)) {
            $error['end_time']=__('ERROR_FORMAT_TIME_END'); 
        }

        if((!isset($error['start_time']))&&(!isset($error['end_time']))) {
            $chtime=check_times($start_time,$end_time); 
            if(!$chtime) {
                $error['start_time']=__('ERROR_SAME_TIME');
                $error['end_time']=__('ERROR_SAME_TIME');           
            }
        } else {
            $chtime=false;
        }

        if(strcmp($plug_type,"lamp")!=0) {
            if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
		        $chval=check_format_values_program($value_program,"temp");
            } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"deshumidifier")==0)) {
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

            foreach($prog as $val) {	
                if(insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$main_error)) {
                    insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$main_error);
                        if((!empty($sd_card))&&(isset($sd_card))) {
                            $program=create_program_from_database($main_error);
                            save_program_on_sd($sd_card,$program,$main_error);
                        } 
				        insert_plug_conf("PLUG_TYPE",$val["selected_plug"],$val["plug_type"],$main_error);
                } else {
                    unset($finish);
                }
            }

            insert_plug_conf("PLUG_ENABLED",$selected_plug,"True",$main_error);

            if(count($error)==0) {
                if(($selected_plug==$nb_plugs)||((isset($finish))&&(!empty($finish)))) {            
                    set_historic_value(__('VALID_UPDATE_PROGRAM')." (".__('WIZARD_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
                    header('Location: programs-'.$_SESSION['SHORTLANG']);
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
    if((isset($next_plug))&&(!empty($next_plug))&&(count($error)==0)) {
        $selected_plug=$selected_plug+1;
        $step=2;
    }
}

if((!empty($selected_plug))&&(isset($selected_plug))) {
   $plug_name=get_plug_conf("PLUG_NAME",$selected_plug,$main_error);
}


if((!isset($step))||(empty($step))||(!is_numeric($step))||($step<0)) {
    if(($selected_plug==1)) {
        $step=1;
    } else {
        $step=2;
    }
} else if((isset($next))&&(!empty($next))) {
    $step=$step+1;	
} else if((isset($previous))&&(!empty($previous))) {
    $step=$step-1;
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


//Display the wizard template
include('main/templates/wizard.html');

?>
