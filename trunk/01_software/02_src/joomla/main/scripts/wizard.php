<?php

if (!isset($_SESSION)) {
	session_start();
}

/* Libraries requiered: 
        db_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
*/
require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once('main/libs/db_set_common.php');
require_once('main/libs/utilfunc.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');


// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$selected_plug=getvar('selected_plug');
$close=getvar('close');
$pop_up_message="";
$pop_up_error_message="";
$version=get_configuration("VERSION",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);
$main_info[]=__('WIZARD_DISABLE_FUNCTION').": <a href='programs-".$_SESSION['SHORTLANG']."'><img src='../../main/libs/img/wizard.png' alt='".__('CLASSIC')."' title='' id='Classic' /></a>";
$type_submit=getvar('type_submit');
$status=get_canal_status($main_error);
$type=getvar("type");


$error_value[2]=__('ERROR_VALUE_PROGRAM','html');
$error_value[3]=__('ERROR_VALUE_PROGRAM_TEMP','html');
$error_value[4]=__('ERROR_VALUE_PROGRAM_HUMI','html');
$error_value[5]=__('ERROR_VALUE_PROGRAM_CM','html');
$error_value[6]=__('ERROR_VALUE_PROGRAM','html');

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

// Setting some default values:
if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=1;
}

if((!empty($close))&&(isset($close))) {
        header('Location: programs-'.$_SESSION['SHORTLANG']."?selected_plug=".$selected_plug);
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
    if(strcmp($plug_power_max,"VARIO")==0) {
       $plug_power_max=getvar("dimmer_canal");
       $type="2";
   }

} 

$value_program=getvar('value_program');

if((empty($plug_type))||(!isset($plug_type))) {
    $plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error); 
}

if((empty($plug_power_max))||(!isset($plug_power_max))) {
    $plug_power_max=get_plug_conf("PLUG_POWER_MAX",$selected_plug,$main_error);
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);
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
            case 'pump':
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
                if((!isset($type))||(empty($type))) {
                    $type="1";
                } else {
                    echo "$type";
                }
            } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"dehumidifier")==0)) {
                $chval=check_format_values_program($value_program,"humi");
                if((!isset($type))||(empty($type))) {
                    $type="1";
                }
            }elseif(strcmp($plug_type,"pump")==0) {
                $chval=check_format_values_program($value_program,"cm"); 
                if((!isset($type))||(empty($type))) {
                    $type="1";
                }
            } else {
                $chval=check_format_values_program($value_program,"other");
            }
            if(strcmp("$chval","1")!=0) {
                $error['value']=$chval; 
            }
        } else {
            $chval="1";
        }

        if((!isset($type))||(empty($type)))  {
            $type="0";
        }

        $plug_tolerance="1.0";

        if(($chtime)&&(strcmp("$chval","1")==0)) {
            if($chtime==2) {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "23:59:59",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type",
                    "type" => "$type"
                );

                $prog[]= array(
                    "start_time" => "00:00:00",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
					"plug_type" => "$plug_type",
                    "type" => "$type"
                ); 
            } else {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type",
                    "type" => "$type"
                );
            }

            // Clean current program
			clean_program($selected_plug,1,$main_error);
            
			if(isset($plug_tolerance)) {
                insert_plug_conf("PLUG_TOLERANCE",$selected_plug,$plug_tolerance,$main_error);
            }

            if(isset($plug_power_max)) {
                insert_plug_conf("PLUG_POWER_MAX",$selected_plug,$plug_power_max,$main_error);
            }

            $chinsert=true;
            if(!insert_program($prog,$main_error,"1"))
                $chinsert=false;

            if($chinsert) {
               if((!empty($sd_card))&&(isset($sd_card))) {
                    $program=create_program_from_database($main_error);
                    
                    // Create plugv from database
                    save_program_on_sd($sd_card,$program);
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
        } else {
            if((isset($error))&&(!empty($error))&&(count($error)>0)) {
                foreach($error as $err) {
                    $pop_up_error_message=$pop_up_error_message.popup_message($err);
                }
            }
        }
    }
    if((isset($type_submit))&&(!empty($type_submit))&&(strcmp($type_submit,"submit_next")==0)) {
        $selected_plug=$selected_plug+1;
        $step=1;
    } elseif(($type_submit)&&(!empty($type_submit))&&(strcmp($type_submit,"submit_close")==0)) {
        header('Location: programs-'.$_SESSION['SHORTLANG']."?selected_plug=".$selected_plug);
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

// Include in html pop up and message
include('main/templates/post_script.php');

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
