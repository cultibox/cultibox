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
$lang=get_configuration("LANG",$main_error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$selected_plug=getvar('selected_plug');
$active_plugs=get_active_plugs($nb_plugs,$main_error);


if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=$active_plugs[0]['id'];
}

$exportid=getvar('exportid');
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
$version=get_configuration("VERSION",$main_error);
$resume="";
$add_plug=getvar('add_plug');
$remove_plug=getvar('remove_plug');
$stats=get_configuration("STATISTICS",$main_error);
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$apply=getvar('apply');
$start_time=getvar("start_time");
$end_time=getvar("end_time");
$reset_program=getvar("reset_old_program");
$regul_program=getvar("regul_program");
$plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error);
$cyclic=getvar("cyclic");
$value_program=getvar('value_program');


if(isset($cyclic)&&(!empty($cyclic))) {
    $repeat_time=getvar("repeat_time");
    $cyclic_ch=check_format_time($repeat_time);
    if(!$cyclic_ch) {
        $error['repeat_time']=__('ERROR_FORMAT_TIME');
        set_historic_value(__('ERROR_FORMAT_TIME')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_error",$main_error);
        $cyclic_ch="";
    }
}



if(empty($apply)||(!isset($apply))) {
    $value_program="";
    $regul_program="";
    
    if((strcmp($plug_type,"lamp")==0)||(strcmp($plug_type,"unknown")==0)) {
            $regul_program="on";
    } else {
            $regul_program="regul";
    }
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
                        $pop_up_message=$pop_up_message.clean_popup_message(__('PLUG_ADDED'));
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
                        }
                        set_historic_value(__('PLUG_REMOVED')." (".__('PROGRAM_PAGE').")","histo_info",$main_error);
                        $pop_up_message=$pop_up_message.clean_popup_message(__('PLUG_REMOVED'));
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


// Manage the plug: reset, import, export:
if((isset($action_prog))&&(!empty($action_prog))) {
	if((isset($exportid))&&(!empty($exportid))) {
         export_program($exportid,$main_error);
         $file="tmp/program_plug${exportid}.prg";
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
            set_historic_value(__('HISTORIC_EXPORT')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$exportid.")","histo_info",$main_error);
            exit();
         }
	} elseif((isset($reset))&&(!empty($reset))) {
		if(clean_program($selected_plug,$main_error)) {
			$pop_up_message=$pop_up_message.clean_popup_message(__('INFO_RESET_PROGRAM'));
            set_historic_value(__('INFO_RESET_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
      }
	} elseif((isset($import))&&(!empty($import))) {
      $target_path = "tmp/".basename( $_FILES['upload_file']['name']); 
      if(!move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_path)) {
         $main_error[]=__('ERROR_UPLOADED_FILE');
         $pop_up_error_message=$pop_up_error_message.clean_popup_message(__('ERROR_UPLOADED_FILE'));
         set_historic_value(__('ERROR_UPLOADED_FILE')." (".__('PROGRAM_PAGE')." - tmp/".basename( $_FILES['upload_file']['name']).")","histo_error",$main_error);
      } else {
         $chprog=true;
         $data_prog=array();
         $data_prog=generate_program_from_file("$target_path",$selected_plug,$main_error);
         if(count($data_prog)==0) { 
            $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');
            set_historic_value(__('ERROR_GENERATE_PROGRAM_FROM_FILE')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_error",$main_error);
            $pop_up_error_message=$pop_up_error_message.clean_popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));
            
         } else {
            clean_program($selected_plug,$main_error);
            export_program($selected_plug,$main_error); 
            
            foreach($data_prog as $val) {
               if(!insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$main_error)) $chprog=false;
         }
         if(!$chprog) {
               $main_error[]=__('ERROR_GENERATE_PROGRAM_FROM_FILE');        
               set_historic_value(__('ERROR_GENERATE_PROGRAM_FROM_FILE')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_error",$main_error);
               $pop_up_error_message=$pop_up_error_message.clean_popup_message(__('ERROR_GENERATE_PROGRAM_FROM_FILE'));

               $data_prog=generate_program_from_file("tmp/program_plug${selected_plug}.prg",$selected_plug,$main_error);
               if(count($data_prog)>0) {
                     foreach($data_prog as $val) {
                        insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$main_error);
                     }
               }
            } else {
                 $main_info[]=__('VALID_UPDATE_PROGRAM');
                 $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_PROGRAM'));
                 set_historic_value(__('VALID_UPDATE_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
            }
         }
      }
   }  
} 


$main_info[]=__('WIZARD_ENABLE_FUNCTION');


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}

if((!isset($sd_card))||(empty($sd_card))) {
       	$main_error[]=__('ERROR_SD_CARD_PROGRAMS');
} else {
       	$main_info[]=__('INFO_SD_CARD').": $sd_card";
}



//Create a new program:
if(!empty($apply)&&(isset($apply))) { 
    if(!check_format_time($start_time)) {
        $error['start_time']=__('ERROR_FORMAT_TIME_START');
        $start_time="";
    }

    if(!check_format_time($end_time)) {
        $error['end_time']=__('ERROR_FORMAT_TIME_END'); 
        $end_time="";
    }

    if((!isset($error['start_time']))&&(!isset($error['end_time']))) {
        $chtime=check_times($start_time,$end_time);
        if(!$chtime) {
            $error['start_time']=__('ERROR_SAME_TIME');
        }
    } else {
        $chtime=false;
    }

    if("$regul_program"=="on") {
            $value_program="99.9";
            $check=true;
    } else if("$regul_program"=="off") {
            $value_program="0";
            $check=true;
    } else {
            if((strcmp($regul_program,"on")!=0)&&(strcmp($regul_program,"off")!=0)) {
                if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
                    $check=check_format_values_program($value_program,"temp");
                } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"deshumidifier")==0)) {
                    $check=check_format_values_program($value_program,"humi");
                } else {
                    $check=check_format_values_program($value_program,"unknown");
                }
            } else {
                $check="1";
            }
    }


    if((empty($cyclic)&&($chtime))||((!empty($cyclic))&&($cyclic_ch)&&($chtime))) {
        if(strcmp("$check","1")==0) {
            if($chtime==2) {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "23:59:59",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug"
                );

                $prog[]= array(
                    "start_time" => "00:00:00",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug"
                );
            } else {
                $prog[]= array(
                                "start_time" => "$start_time",
                                "end_time" => "$end_time",
                                "value_program" => "$value_program",
                                "selected_plug" => "$selected_plug"
                );
            }


            if(isset($cyclic)&&(!empty($cyclic))) {
                    date_default_timezone_set('UTC');
                    $cyclic_start= $start_time;
                    $cyclic_end=$end_time;
                    $rephh=substr($repeat_time,0,2);
                    $repmm=substr($repeat_time,3,2);
                    $repss=substr($repeat_time,6,2);
                    $step=$rephh*3600+$repmm*60+$repss;
                    $chk_start=mktime(0,0,0);
                    $chk_stop=mktime();
                    $chk_first=false;

                    while(($chk_stop-$chk_start)<86400) {
                            if($chk_first) {
                                $prog[]= array(
                                    "start_time" => "$cyclic_start",
                                    "end_time" => "$cyclic_end",
                                    "value_program" => "$value_program",
                                    "selected_plug" => "$selected_plug"
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

                            $chk_stop=$val_stop;

                            if(($chtime==2)&&(!$chk_first)) {
                                    if(((str_replace(":","",$cyclic_start)<=235959)&&((str_replace(":","",$cyclic_start))>=(str_replace(":","",$start_time))))||((str_replace(":","",$cyclic_start))<=(str_replace(":","",$end_time)))) {
                                        unset($prog);
                                        $prog[]= array(
                                                "start_time" => "00:00:00",
                                                "end_time" => "23:59:59",
                                                "value_program" => "$value_program",
                                                "selected_plug" => "$selected_plug"
                                        );
                                    }
                            }
                            $chk_first=true;
                    }
            } 


            //If the reset checkbox is checked
            if((isset($reset_program))&&(strcmp($reset_program,"Yes")==0)) {
                clean_program($selected_plug,$main_error);
            } 
                                                                  

            $ch_insert=true;
            foreach($prog as $val) {
                if(!insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$main_error))  $ch_insert=false;
            }

            if($ch_insert) {
                   $main_info[]=__('INFO_VALID_UPDATE_PROGRAM');
                   $pop_up_message=$pop_up_message.clean_popup_message(__('INFO_VALID_UPDATE_PROGRAM'));                    
                   set_historic_value(__('INFO_VALID_UPDATE_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);


                   if((isset($sd_card))&&(!empty($sd_card))) {
                            $main_info[]=__('INFO_PLUG_CULTIBOX_CARD');
                            $pop_up_message=$pop_up_message.clean_popup_message(__('INFO_PLUG_CULTIBOX_CARD'));
                   }
            } 
        } 

    }

    if(strcmp("$check","1")!=0) {
            $error['value']=$check;
            $value_program="";

        }

}


if((isset($error))&&(!empty($error))&&(count($error)>0)) {
    foreach($error as $err) {
        $pop_up_error_message=$pop_up_error_message.clean_popup_message($err);
    }
} else if((isset($info))&&(!empty($info))&&(count($info)>0)) {
        foreach($info as $inf) {
            $pop_up_message=$pop_up_message.clean_popup_message($inf);
    }
}

	
for($i=0;$i<$nb_plugs;$i++) {
    $data_plug=get_data_plug($i+1,$main_error);
    $plugs_infos[$i]["data"]=format_program_highchart_data($data_plug,"");

    switch($plugs_infos[$i]['PLUG_TYPE']) {
        case 'unknown': $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
        case 'ventilator': $plugs_infos[$i]['translate']=__('PLUG_VENTILATOR'); break;
        case 'heating': $plugs_infos[$i]['translate']=__('PLUG_HEATING'); break;	
        case 'lamp': $plugs_infos[$i]['translate']=__('PLUG_LAMP'); break;
        case 'humidifier': $plugs_infos[$i]['translate']=__('PLUG_HUMIDIFIER'); break;
        case 'dehumidifier': $plugs_infos[$i]['translate']=__('PLUG_DEHUMIDIFIER'); break;
        default: $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN'); break;
    }

    if(isset($selected_plug)&&(!empty($selected_plug))&&($i==$selected_plug-1)&&(isset($reset_program))&&(strcmp($reset_program,"Yes")==0)) {
        $plugs_infos[$i]["RESET"]='Yes';
    } else {
        unset($plugs_infos[$i]["RESET"]);
    }

    if(isset($selected_plug)&&(!empty($selected_plug))&&($i==$selected_plug-1)) {
            $resume=format_data_sumary($plugs_infos[$i]["data"],$plugs_infos[$i]['PLUG_NAME'],$i+1,$plugs_infos[$i]['PLUG_TYPE']);
    }
}

if((isset($sd_card))&&(!empty($sd_card))) {
      $program=create_program_from_database($main_error);
      if(!compare_program($program,$sd_card)) {
         if(((empty($selected_plug))||(!isset($selected_plug)))&&((!isset($reset))||(empty($reset)))) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.clean_popup_message(__('UPDATED_PROGRAM'));
            set_historic_value(__('UPDATED_PROGRAM')." (".__('PROGRAM_PAGE')." - ".__('WIZARD_CONFIGURE_PLUG_NUMBER')." ".$selected_plug.")","histo_info",$main_error);
         }
         save_program_on_sd($sd_card,$program,$main_error);
      }
      check_and_copy_firm($sd_card,$main_error);
      check_and_copy_log($sd_card,$main_error);
}

if((strcmp($regul_program,"on")==0)||(strcmp($regul_program,"off")==0)) {
        $value_program="";
} 


// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
            if(count($file)==3) {
                $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a href=".$file[2]." target='_blank'>".$file[1]."</a>";
            }
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

//Display the programs template
include('main/templates/programs.html');

?>
