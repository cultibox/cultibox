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
$version=get_configuration("VERSION",$main_error);
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');


// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$selected_plug=getvar('selected_plug');
$active_plugs=get_active_plugs($nb_plugs,$main_error);


if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=$active_plugs[0]['id'];
}

$import=getvar('import');
$export=getvar('export');
$reset=getvar('reset');
$action_prog=getvar('action_prog');
$chinfo=true;
$chtime="";
$pop_up_message="";
$pop_up_error_message="";
$regul_program="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$resume=array();
$add_plug=getvar('add_plug');
$remove_plug=getvar('remove_plug');
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$apply=getvar('apply');
$start_time=getvar("start_time");
$end_time=getvar("end_time");
$reset_program=getvar("reset_old_program");
$regul_program=getvar("regul_program");
$plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error);
$cyclic=getvar("cyclic");
$value_program=getvar('value_program');
$second_regul=get_configuration("SECOND_REGUL",$main_error);
$reset_selected=getvar("reset_selected_plug");
$start="";
$end="";
$rep="";
$resume_regul=array();
$tmp="";
$submit=getvar("submit_progs",$main_error);
$anchor=getvar('anchor');
$type="0";

$error_value[2]=__('ERROR_VALUE_PROGRAM','html');
$error_value[3]=__('ERROR_VALUE_PROGRAM_TEMP','html');
$error_value[4]=__('ERROR_VALUE_PROGRAM_HUMI','html');
$error_value[5]=__('ERROR_VALUE_PROGRAM_CM','html');
$error_value[6]=__('ERROR_VALUE_PROGRAM','html');

for($i=1;$i<=$nb_plugs;$i++) {
    format_regul_sumary("$i",$main_error,$tmp,$nb_plugs);
    $resume_regul[]="$tmp";
    $tmp="";
}



if((!isset($reset_selected))||(empty($reset_selected))) {
    $reset_selected=$selected_plug;
} else {
    if(strcmp("$reset_selected","all")==0) {
        $selected_plug=1;
    } else {
        $selected_plug=$reset_selected;
    }
}


if(isset($cyclic)&&(!empty($cyclic))) {
    $repeat_time=getvar("repeat_time");
    $start_time_cyclic=getvar('start_time_cyclic');
    $cyclic_duration=getvar('cyclic_duration');
    $cyclic_start=$start_time_cyclic;
}



if(empty($apply)||(!isset($apply))) {
    $value_program="";
    $regul_program="on";
}

// Add a plug dinamically to configure a new program, maximal plug is configured in config.php file by the variable $GLOBALS['NB_MAX_PLUG']
if((isset($add_plug))&&(!empty($add_plug))) {
    if((isset($nb_plugs))&&(!empty($nb_plugs))) {
            if($nb_plugs<$GLOBALS['NB_MAX_PLUG']) {
                    insert_configuration("NB_PLUGS",$nb_plugs+1,$main_error);
                    if((empty($main_error))||(!isset($main_error))) {
                        $nb_plugs=$nb_plugs+1;
                        $main_info[]=__('PLUG_ADDED');
                        $selected_plug=$nb_plugs;
                        $reset_selected=$nb_plugs;
                        
                        $pop_up_message=$pop_up_message.popup_message(__('PLUG_ADDED'));
                        set_historic_value(__('PLUG_ADDED')." (".__('PROGRAM_PAGE').")","histo_info",$main_error);
                        $active_plugs=get_active_plugs($nb_plugs,$main_error);
                    }
            } else {
                    $main_error[]=__('PLUG_MAX_ADDED');
                    set_historic_value(__('PLUG_MAX_ADDED')." (".__('PROGRAM_PAGE').")","histo_error",$main_error);
            }
    }
}


// Remove a plug dinamically to configure a new program, minimal plugs id 3
if((isset($remove_plug))&&(!empty($remove_plug))) {
    if((isset($nb_plugs))&&(!empty($nb_plugs))) {
            if($nb_plugs>3) {
                    insert_configuration("NB_PLUGS",$nb_plugs-1,$main_error);
                    if((empty($main_error))||(!isset($main_error))) {
                        $nb_plugs=$nb_plugs-1;
                        $main_info[]=__('PLUG_REMOVED');
                        if($selected_plug>$nb_plugs) {
                            $selected_plug=$nb_plugs;
                            $reset_selected=$nb_plugs;
                        }
                        set_historic_value(__('PLUG_REMOVED')." (".__('PROGRAM_PAGE').")","histo_info",$main_error);
                        $pop_up_message=$pop_up_message.popup_message(__('PLUG_REMOVED'));
                        $active_plugs=get_active_plugs($nb_plugs,$main_error);
                    }
            } else {
                    $main_error[]=__('PLUG_MIN_ADDED');
                    set_historic_value(__('PLUG_MIN_ADDED')." (".__('PROGRAM_PAGE').")","histo_error",$main_error);
            }
    }
}


// Retrieve plug's informations from the database
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);


// Manage the plug: reset, import, export, reset_all:
if((isset($export))&&(!empty($export))) {
     export_program($selected_plug,$main_error);
     $file="tmp/program_plug${selected_plug}.prg";
     if (($file != "") && (file_exists("./$file"))) {
        $size = filesize("./$file");
        header("Content-Type: application/force-download; name=\"$file\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $size");
        header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        readfile("./$file");    
        set_historic_value(__('HISTORIC_EXPORT')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
        exit();
     }
} elseif((isset($reset))&&(!empty($reset))) {
    if(strcmp("$reset_selected","all")==0) {
        $status=true;
        foreach($active_plugs as $aplugs) {
            if(!clean_program($aplugs['id'],$main_error)) $status=false;
        }
        if($status) {
            $pop_up_message=$pop_up_message.popup_message(__('INFO_RESET_PROGRAM'));
            $main_info[]=__('INFO_RESET_PROGRAM');
            set_historic_value(__('INFO_RESET_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$reset_selected.")","histo_info",$main_error);
        } else {
            $pop_up_message=$pop_up_message.popup_message(__('ERROR_RESET_PROGRAM'));
            $main_info[]=__('ERROR_RESET_PROGRAM');
            set_historic_value(__('ERROR_RESET_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$reset_selected.")","histo_error",$main_error);
        }
        $reset_selected=1;
    } else {
        if(clean_program($reset_selected,$main_error)) {
            $pop_up_message=$pop_up_message.popup_message(__('INFO_RESET_PROGRAM'));
            $main_info[]=__('INFO_RESET_PROGRAM');
            set_historic_value(__('INFO_RESET_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$reset_selected.")","histo_info",$main_error);
        }
    }
} elseif((isset($import))&&(!empty($import))) {
      $target_path = "tmp/".basename( $_FILES['upload_file']['name']); 
      if(!move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_path)) {
         $main_error[]=__('ERROR_UPLOADED_FILE');
         $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_UPLOADED_FILE'));
         set_historic_value(__('ERROR_UPLOADED_FILE')." (".__('PROGRAM_PAGE')." - tmp/".basename( $_FILES['upload_file']['name']).")","histo_error",$main_error);
      } else {
         $chprog=true;
         $data_prog=array();
         $data_prog=generate_program_from_file("$target_path",$selected_plug,$main_error);
         if(count($data_prog)==0) { 
            $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');
            set_historic_value(__('ERROR_GENERATE_PROGRAM_FROM_FILE')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_error",$main_error);
            $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));
            
         } else {
            clean_program($selected_plug,$main_error);
            export_program($selected_plug,$main_error); 
         
            if(!insert_program($data_prog,$main_error)) $chprog=false;

            if(!$chprog) {
               $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');        
               set_historic_value(__('ERROR_GENERATE_PROGRAM_FROM_FILE')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_error",$main_error);
               $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));

               $data_prog=generate_program_from_file("tmp/program_plug${selected_plug}.prg",$selected_plug,$main_error);
               if(!insert_program($data_prog,$main_error)) $chprog=false;
            } else {
                 $main_info[]=__('VALID_IMPORT_PROGRAM');
                 $pop_up_message=$pop_up_message.popup_message(__('VALID_IMPORT_PROGRAM'));
                 set_historic_value(__('VALID_IMPORT_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
            }
        }
    }
} 


$main_info[]=__('WIZARD_ENABLE_FUNCTION').": <a href='wizard-".$_SESSION['SHORTLANG']."'><img src='../../main/libs/img/wizard.png' alt='".__('WIZARD')."' title='' id='wizard' /></a>";




// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}


//Create a new program:
if(!empty($apply)&&(isset($apply))) { 
    if(isset($cyclic)&&(!empty($cyclic))&&(strcmp("$repeat_time","00:00:00")!=0)) {
        $tmpstamp_start=strtotime($cyclic_start);
        list($hours, $mins, $secs) = explode(':', $cyclic_duration);
        $tmpstamp_end=($hours * 3600 )+($mins * 60 )+$secs;
        $cyclic_end=date('H:i:s', $tmpstamp_start+$tmpstamp_end);
        $chtime=check_times($cyclic_start,$cyclic_end);
    } else {
        $chtime=check_times($start_time,$end_time);
    }

    if("$regul_program"=="on") {
            $value_program="99.9";
            $type="0";
            $check=true;
    } else if("$regul_program"=="off") {
            $value_program="0";
            $check=true;
    } else if("$regul_program"=="dimmer") {
            $type="2";
            $check=true;
    } else {
            if((strcmp($regul_program,"on")!=0)&&(strcmp($regul_program,"off")!=0)) {
                if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)||(strcmp($plug_type,"pump")==0)) {
                    $check=check_format_values_program($value_program,"temp");
                } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"dehumidifier")==0)) {
                    $check=check_format_values_program($value_program,"humi");
                } else {
                    $check=check_format_values_program($value_program,"other");
                }
            } else {
                $check="1";
            }
            $type="1";
    }

        

        if(strcmp("$check","1")==0) {
            if(isset($cyclic)&&(!empty($cyclic))&&(strcmp("$repeat_time","00:00:00")!=0)) {
                 $start_time=$cyclic_start;
                 $end_time=$cyclic_end;
            }

            if($chtime==2) {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "23:59:59",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "type" => "$type"
                );

                $prog[]= array(
                    "start_time" => "00:00:00",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "type" => "$type"
                );
            } else {
                $prog[]= array(
                                "start_time" => "$start_time",
                                "end_time" => "$end_time",
                                "value_program" => "$value_program",
                                "selected_plug" => "$selected_plug",
                                "type" => "$type"
                );
            }
            $start=$start_time;
            $end=$end_time;
            $ch_insert=true;

        
            if(isset($cyclic)&&(!empty($cyclic))&&(strcmp("$repeat_time","00:00:00")!=0)) {
                    date_default_timezone_set('UTC');
                    $rephh=substr($repeat_time,0,2);
                    $repmm=substr($repeat_time,3,2);
                    $repss=substr($repeat_time,6,2);
                    $step=$rephh*3600+$repmm*60+$repss;
                    if($chtime==2) {
                        $chk_start=mktime(0,0,0);
                    } else {
                        $chk_start=mktime(0,0,0);
                    }
                    $chk_stop=mktime();
                    $chk_first=false;

                    $start_check=str_replace(":","",$start_time);
                    $stop_check=str_replace(":","",$end_time);
                    $repeat_check=str_replace(":","",$repeat_time);
                    $optimize=false;


                    if($chtime!=2) {
                        if($stop_check-$start_check>=$repeat_check) {
                            $optimize=true;
                            unset($prog);
                            $prog[]= array(
                                    "start_time" => "$cyclic_start",
                                    "end_time" => "23:59:59",
                                    "value_program" => "$value_program",
                                    "selected_plug" => "$selected_plug",
                                    "type" => "$type"
                                );
                        }
                    } else {
                        if((235959-$start_check)+$stop_check>=$repeat_check) {
                            $optimize=true;
                            unset($prog);
                            $prog[]= array(
                                    "start_time" => "00:00:00",
                                    "end_time" => "23:59:59",
                                    "value_program" => "$value_program",
                                    "selected_plug" => "$selected_plug",
                                     "type" => "$type"
                                );
                        }
                    }

                    if(!$optimize) {
                        while(($chk_stop-$chk_start)<86400) {
                            if($chk_first) {
                                $prog[]= array(
                                    "start_time" => "$cyclic_start",
                                    "end_time" => "$cyclic_end",
                                    "value_program" => "$value_program",
                                    "selected_plug" => "$selected_plug",
                                    "type" => "$type"
                                );
                            }

                            $hh=substr($cyclic_start,0,2);
                            $mm=substr($cyclic_start,3,2);
                            $ss=substr($cyclic_start,6,2);

                            $shh=substr($cyclic_end,0,2);
                            $smm=substr($cyclic_end,3,2);
                            $sss=substr($cyclic_end,6,2);

                            $val_start=mktime($hh,$mm,$ss)+$step;
                            $val_stop=mktime($shh,$smm,$sss)+$step;

                            $cyclic_start=date('H:i:s', $val_start);
                            $cyclic_end=date('H:i:s', $val_stop);


                            //Pour eviter que la partie cyclique reboucle:
                            if(($chtime==2)&&(str_replace(":","",$cyclic_end)>str_replace(":","",$start_time))) {
                                break;
                            }

                            $chk_stop=$val_stop;
                            $chk_first=true;
                        }

                        // Si le dernier évènement cyclique dépasse, on le raccourcis pour qu'il finisse a 23:59:59
                        if((strcmp("$cyclic_end","00:00:00")==0)||(str_replace(":","",$cyclic_end)<str_replace(":","",$cyclic_start))) {
                            $prog[]= array(
                                    "start_time" => "$cyclic_start",
                                    "end_time" => "23:59:59",
                                    "value_program" => "$value_program",
                                    "selected_plug" => "$selected_plug",
                                    "type" => "$type"
                                );
                        }
                }
                $rep=$repeat_time;
                $start_time="00:00:00";
                $end_time="00:00:00";
            }
        
            //If the reset checkbox is checked
            if((isset($reset_program))&&(strcmp($reset_program,"Yes")==0)) {
                clean_program($selected_plug,$main_error);
                unset($reset_program);
            } 


            // To compute the number of action for the plugv file limited to 250 actions:
            // To do: enregistrer toutes les actions puis regarder à combien on est: si > 250, on supprime la dernière action jusqu'à descendre en dessous de 250
            // Autre possibilité, laisser le programme enregistré mais indiquer sur les programmes à partir de quelle heure les prises ne changent plus d'état et gerdent leur état...
            $base=create_program_from_database($main_error);
            $nb_prog=count($base);  
            $count=-1;
            $tmp_prog=array();

            foreach($prog as $program) {
               if($nb_prog>=250) {
                    break;
               }
               if(find_new_line($base,$program['start_time'])) {
                    $nb_prog=$nb_prog+1;
               } 
        
               if(find_new_line($base,$program['end_time'])) {
                    $nb_prog=$nb_prog+1;
               }

               $count=$count+1;
            }

            $ch_insert=true;
            if($nb_prog>=250) {
                if($nb_prog>250) {
                   $count=$count-1;
                }
                $main_error[]=__('ERROR_MAX_PROGRAM');
                $pop_up_error_message=$pop_up_error_message.popup_message(__('ERROR_MAX_PROGRAM'));
                $ch_insert=false;
            } 

           
            if($count>-1) {
                if($count+1!=count($prog)) {
                    $tmp=array_chunk($prog, $count+1);
                    $tmp_prog=$tmp[0];
                } else {
                    $tmp_prog=$prog;
                }  

                if(!insert_program($tmp_prog,$main_error)) $ch_insert=false;
            } else {
                $ch_insert=false;
            }

            if(!insert_program($tmp_prog,$main_error)) $ch_insert=false;

            if($ch_insert) {
                   $main_info[]=__('INFO_VALID_UPDATE_PROGRAM');
                   $pop_up_message=$pop_up_message.popup_message(__('INFO_VALID_UPDATE_PROGRAM'));                    
                   set_historic_value(__('INFO_VALID_UPDATE_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);


                   if((isset($sd_card))&&(!empty($sd_card))) {
                            $main_info[]=__('INFO_PLUG_CULTIBOX_CARD');
                            $pop_up_message=$pop_up_message.popup_message(__('INFO_PLUG_CULTIBOX_CARD'));
                   }
            }  

    }
}

for($i=0;$i<$nb_plugs;$i++) {
    $data_plug=get_data_plug($i+1,$main_error);
    $plugs_infos[$i]["data"]=format_program_highchart_data($data_plug,"");

    switch($plugs_infos[$i]['PLUG_TYPE']) {
        case 'other': $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
        case 'ventilator': $plugs_infos[$i]['translate']=__('PLUG_VENTILATOR'); break;
        case 'heating': $plugs_infos[$i]['translate']=__('PLUG_HEATING'); break;	
        case 'pump': $plugs_infos[$i]['translate']=__('PLUG_PUMP'); break;
        case 'lamp': $plugs_infos[$i]['translate']=__('PLUG_LAMP'); break;
        case 'humidifier': $plugs_infos[$i]['translate']=__('PLUG_HUMIDIFIER'); break;
        case 'dehumidifier': $plugs_infos[$i]['translate']=__('PLUG_DEHUMIDIFIER'); break;
        default: $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
    }
}


$resume=format_data_sumary($plugs_infos);
$tmp_resume[]="";
foreach($resume as $res) {
      $tmp_res=explode("<br />",$res);
      if(count($tmp_res)>40) {
          $tmpr=array_chunk($tmp_res,39);
          $tmpr[0][]="[...]";
          $tmp_resume[]=implode("<br />", $tmpr[0]);
      } else {
          $tmp_resume[]=$res;
      }
}

if(count($tmp_resume)>0) {
    unset($resume);
    $resume=$tmp_resume;
}


if((!empty($sd_card))&&(isset($sd_card))) {
    if(check_sd_card($sd_card)) {
        if((isset($submit))&&(!empty($submit))) {
            $program=create_program_from_database($main_error);
            if(!compare_program($program,$sd_card)) {
                if(!save_program_on_sd($sd_card,$program,$main_error)) {
                    $main_error[]=__('ERROR_WRITE_PROGRAM');
                }
            }
        }
    }
}



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
            set_historic_value(__('UPDATED_PROGRAM')." (".__('PROGRAM_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_SD');
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    }
} else {
        $main_error[]=__('ERROR_SD_CARD_PROGRAMS');
}


if((strcmp($regul_program,"on")==0)||(strcmp($regul_program,"off")==0)) {
        $value_program="";
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


//Display the programs template
include('main/templates/programs.html');

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
