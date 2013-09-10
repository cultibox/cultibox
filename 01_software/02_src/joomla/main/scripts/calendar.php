<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

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


// ================= VARIABLES ================= //
$main_error=array();
$main_info=array();
$informations = Array();
$program="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$stats=get_configuration("STATISTICS",$main_error);
$pop_up = get_configuration("SHOW_POPUP",$main_error);
$pop_up_message=""; 
$reset=getvar('reset_calendar_submit');
$calendar_start=getvar('calendar_startdate');

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
$lang=$_SESSION['LANG'];
__('LANG');


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!isset($calendar_start))||(empty($calendar_start))) {
    $calendar_start=date('Y')."-".date('m')."-".date('d');
} else {
    $program_substrat=getvar('substrat');
    $program_product=getvar('product');
    $file="";

    if((isset($program_substrat))&&(!empty($program_substrat))&&(isset($program_product))&&(!empty($program_product))) {
        if($handle = @opendir('main/xml')) {
            while (false !== ($entry = readdir($handle))) {
                if(($entry!=".")&&($entry!="..")) {
                    $rss_file = file_get_contents("main/xml/".$entry);
                    $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

                    foreach ($xml as $tab) {
                        if(is_array($tab)) {
                            if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))&&(array_key_exists('periode', $tab))) {
                                if((strcmp(strtolower($tab['marque']." - ".$tab['periode']),strtolower($program_product))==0)&&((strcmp(strtolower($tab['substrat']),strtolower($program_substrat))==0))) {
                                    $file=$entry;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        if(!empty($file)) {
            $event=array();
            if($handle = @opendir('main/xml')) {
                $rss_file = file_get_contents("main/xml/".$file);
                $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);
                
                foreach ($xml as $tab) {
                    if(is_array($tab)) {
                        foreach($tab as $val) {
                            if(is_array($val)) {
                                if((array_key_exists('title', $val))&&(array_key_exists('duration', $val))&&(array_key_exists('start', $val))&&(array_key_exists('content', $val))) {
                                        if((!empty($val['title']))&&(!empty($val['content']))) {
                                            $datestartTimestamp = strtotime($calendar_start);
                                            $timestart = date('Ymd', strtotime('+'.$val['start'].' days', $datestartTimestamp));

                                            if(empty($val['start'])) {
                                                $val['start']=0;
                                            }

                                            if(empty($val['duration'])) {
                                                $val['duration']=0;
                                            }

                                            $val['duration']=$val['duration']-1;
                                            if($val['duration']<=0) {
                                                $timeend=$timestart;
                                            } else {
                                                $dateendTimestamp = strtotime($timestart);
                                                $timeend = date('Ymd', strtotime('+'.$val['duration'].' days', $dateendTimestamp));
                                            }

                                            if(array_key_exists('color', $val))  {
                                                $color=$val['color'];
                                            } else {
                                                $color="#821D78";
                                            }

                                            if(array_key_exists('icon', $val))  {
                                                $icon=$val['icon'];
                                            } else {
                                                $icon=null;
                                            }

                                            $desc=$val['content'];

                                            if(array_key_exists('nutriment', $val))  {
                                                        $desc=$desc."\n* Engrais:\n";
                                                        if(is_array($val['nutriment'])) {
                                                            foreach($val['nutriment'] as $nut) {
                                                                if(is_array($nut)) {
                                                                    if((array_key_exists('name', $nut))&&(array_key_exists('dosage', $nut)))  {
                                                                        $desc=$desc.$nut['name']." ".$nut['dosage']."\n";
                                                                    }
                                                                } else {
                                                                    $desc=$desc.$nut." ";
                                                                }
                                                            }
                                                        } 
                                            }
                                            $event[]=array(
                                                    "title" => $val['title'],
                                                    "start" => $timestart,
                                                    "end" => $timeend,
                                                    "description" => $desc,
                                                    "color" => $color,
                                                    "icon" => $icon,
                                                    "external" => "0"
                                                    //"allDay" => false
                                            );
                                        }   
                                }
                            }
                       }
                   } 
                } 
            }
            if(count($event)>0) {
                 if(insert_calendar($event,$main_error)) {
                    $main_info[]=__('VALID_ADD_PROGRAM');
                    $pop_up_message=$pop_up_message.popup_message(__('VALID_ADD_PROGRAM'));
                 } else {
                    $main_error[]=__('ERROR_ADD_CALENDAR_PROGRAM');
                    $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_ADD_CALENDAR_PROGRAM'));
                    
                }
            }
        }
    }
}


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) {
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
            set_historic_value(__('UPDATED_PROGRAM')." (".__('CALENDAR_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_PROGRAM');
    }
} else {
        $main_error[]=__('ERROR_SD_CARD');
}


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
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


//Get informations from XML files
$substrat=array();
$product=array();
if($handle = @opendir('main/xml')) {
    while (false !== ($entry = readdir($handle))) {
        if(($entry!=".")&&($entry!="..")) {
            $rss_file = file_get_contents("main/xml/".$entry);
            $xml =json_decode(json_encode((array) @simplexml_load_string($rss_file)), 1);

            foreach ($xml as $tab) {
                if(is_array($tab)) {
                    if((array_key_exists('substrat', $tab))&&(array_key_exists('marque', $tab))&&(array_key_exists('periode', $tab))) {
                        $substrat[]=ucwords(strtolower($tab['substrat']));
                        $product[]=ucwords(strtolower($tab['marque']))." - ".ucwords(strtolower($tab['periode']));
                    }
                }
            }
        }
    }
}

$substrat=array_unique($substrat);


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


if((isset($reset))&&(!empty($reset))) {
    if(reset_log("calendar")) {
        $main_info[]=__('VALID_DELETE_CALENDAR');
        $pop_up_message=$pop_up_message.popup_message(__('VALID_DELETE_CALENDAR'));
        set_historic_value(__('VALID_DELETE_CALENDAR')." (".__('CALENDAR_PAGE').")","histo_info",$main_error);
    }
}


// Part for the calendar: if a cultibox SD card is present, the 'calendar' is updated into this SD card
if((isset($sd_card))&&(!empty($sd_card))) {
   $data=create_calendar_from_database($main_error);
   if(!check_sd_card($sd_card)) {
            $main_error[]=__('ERROR_WRITE_CALENDAR');
   } else {
            clean_calendar($sd_card);
            if(count($data)>0) {
                write_calendar($sd_card,$data,$main_error);
            }
   } 
}



//Display the calendar template
include('main/templates/calendar.html');

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
