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
$ret_plug=array();
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$error);
$selected_plug=getvar('selected_plug');
$exportid=getvar('exportid');
$import=getvar('import');
$info_plug=array();
$export=getvar('export');
$import=getvar('import');
$reset=getvar('reset');
$action_prog=getvar('action_prog');
$chinfo=true;
$chtime="";
$pop_up_message="";
$pop_up_error_message="";
$regul_program="";
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);
$resume=array();
$add_plug=getvar('add_plug');
$remove_plug=getvar('remove_plug');

for($i=0;$i<=$nb_plugs;$i++) {
        $info_plug[]="";
        $ret_plug[]="";
}

if((isset($add_plug))&&(!empty($add_plug))) {
    if((isset($nb_plugs))&&(!empty($nb_plugs))) {
            if($nb_plugs<16) {
                    insert_configuration("NB_PLUGS",$nb_plugs+1,$error);
                    if((empty($error))||(!isset($error))) {
                        $nb_plugs=$nb_plugs+1;
                        $info_plug[$nb_plugs]=__('PLUG_ADDED');
                    }
            } else {
                    $error=__('PLUG_MAX_ADDED');
            }
    }
}

if((isset($remove_plug))&&(!empty($remove_plug))) {
    if((isset($nb_plugs))&&(!empty($nb_plugs))) {
            if($nb_plugs>3) {
                    insert_configuration("NB_PLUGS",$nb_plugs-1,$error);
                    if((empty($error))||(!isset($error))) {
                        $nb_plugs=$nb_plugs-1;
                        $info_plug[$nb_plugs]=__('PLUG_REMOVED');
                    }
            } else {
                    $error=__('PLUG_MIN_ADDED');
            }
    }
}
$plugs_infos=get_plugs_infos($nb_plugs,$error);



if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}




	

if((isset($action_prog))&&(!empty($action_prog))) {
	if((isset($exportid))&&(!empty($exportid))) {
         export_program($exportid,$error);
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
            exit();
         }
	} elseif((isset($reset))&&(!empty($reset))) {
		if(clean_program($action_prog,$error)) {
			$info_plug[$action_prog]=$info_plug[$action_prog].__('INFO_RESET_PROGRAM');
      }
	} elseif((isset($import))&&(!empty($import))) {
      $target_path = "tmp/".basename( $_FILES['upload_file']['name']); 
      if(!move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_path)) {
         $error=$error.__('ERROR_UPLOADED_FILE');
      } else {
         $data_prog=array();
         $data_prog=generate_program_from_file("$target_path",$action_prog,$error);
         if(count($data_prog)==0) { $ret_plug[$action_prog]=$ret_plug[$action_prog].__('ERROR_GENERATE_PROGRAM_FROM_FILE'); } else {
            clean_program($action_prog,$error);
            export_program($action_prog,$error); 
            foreach($data_prog as $val) {
               insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$ret_plug[$action_prog]);
            }
            if(!empty($ret_plug[$action_prog])) {
                  $ret_plug[$action_prog]=$ret_plug[$action_prog].__('ERROR_GENERATE_PROGRAM_FROM_FILE');
                  $data_prog=generate_program_from_file("tmp/program_plug${action_prog}.prg",$action_prog,$error);
                  if(count($data_prog)>0) {
                     foreach($data_prog as $val) {
                        insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$ret_plug[$action_prog]);
                     }
                  }
            } else {
                 $info_plug[$action_prog]=$info_plug[$action_prog].__('VALID_UPDATE_PROGRAM');
            }
         }
      }
   }  
} 


$info=$info.__('WIZARD_ENABLE_FUNCTION');


if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}

if((!isset($sd_card))||(empty($sd_card))) {
       	$error=$error.__('ERROR_SD_CARD_PROGRAMS');
} else {
       	$info=$info.__('INFO_SD_CARD').": $sd_card";
}


if(!empty($selected_plug)&&(isset($selected_plug))) { 
	$start="start_time{$selected_plug}";
	$end="end_time{$selected_plug}";
	$value="value_program{$selected_plug}";
	$regul="regul_program{$selected_plug}";

	$start_time=getvar($start);
	$end_time=getvar($end);
        $regul_program=getvar($regul);

        $plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$ret_plug[$selected_plug]);



	if(strcmp($regul_program,"on")==0) {
        	$value_program="99.9";
        } elseif(strcmp($regul_program,"off")==0) {
                $value_program="0";
        } else {
                $value_program=getvar($value);
        }

        if(("$start_time"!="")&&("$end_time"!="")) {
		$chtime=check_times($start_time,$end_time,$ret_plug[$selected_plug]);
		if((isset($ret_plug[$selected_plug]))&&(!empty($ret_plug[$selected_plug]))) {
			   $pop_up_error_message=clean_popup_message($ret_plug[$selected_plug]);
		}
		if($chtime) {
				if("$value_program"=="on") {
					$value_program="99.9";
					$check=true;
				} else if("$value_program"=="off") {
					$value_program="0";
					$check=true;
				} else {
					if((strcmp($regul_program,"on")!=0)&&(strcmp($regul_program,"off")!=0)) {
                                                if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
                                                   $check=check_format_values_program($value_program,$ret_plug[$selected_plug],"temp");
                                                } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"deshumidifier")==0)) {
                                                   $check=check_format_values_program($value_program,$ret_plug[$selected_plug],"humi");
                                                } else {
                                                   $check=check_format_values_program($value_program,$ret_plug[$selected_plug],"unknown");
                                                }

					} else {
						$check=true;
					}
				}
			
				if($check) {
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

					foreach($prog as $val) {
						if(insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$ret_plug[$selected_plug])) {
							if(empty($info_plug[$selected_plug])) {
								$info_plug[$selected_plug]=$info_plug[$selected_plug].__('INFO_VALID_UPDATE_PROGRAM');
								if((isset($sd_card))&&(!empty($sd_card))) {
									$info_plug[$selected_plug]=$info_plug[$selected_plug].__('INFO_PLUG_CULTIBOX_CARD');
								}
							}
						}
					}
					if((isset($pop_up_message))&&(!empty($pop_up_message))) {
						unset($pop_up_message);
					} else {
						if(count($info_plug)>0) {
                                                        $pop_up_message=clean_popup_message(__('INFO_VALID_UPDATE_PROGRAM'));
							if((isset($sd_card))&&(!empty($sd_card))) {
								$pop_up_message=$pop_up_message.clean_popup_message(__('INFO_PLUG_CULTIBOX_CARD'));
							}
						}
					}
				} else {
					$ret_plug[$selected_plug]=$ret_plug[$selected_plug].__('ERROR_VALUE_PROGRAM');
                                }
			}
		} else {
			$ret_plug[$selected_plug]=$ret_plug[$selected_plug].__('ERROR_MISSING_VALUE_TIME');
			$pop_up_error_message=clean_popup_message(__('ERROR_MISSING_VALUE_TIME'));
	}
}
	
for($i=0;$i<$nb_plugs;$i++) {
	$data_plug=get_data_plug($i+1,$error);
       	$plugs_infos[$i]["data"]=format_program_highchart_data($data_plug,"");

	switch($plugs_infos[$i]['PLUG_TYPE']) {
		case 'unknown': $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN');
						break;
		case 'ventilator': $plugs_infos[$i]['translate']=__('PLUG_VENTILATOR');
                       	break;
		case 'heating': $plugs_infos[$i]['translate']=__('PLUG_HEATING');
                       	break;	
		case 'lamp': $plugs_infos[$i]['translate']=__('PLUG_LAMP');
                       	break;
		case 'humidifier': $plugs_infos[$i]['translate']=__('PLUG_HUMIDIFIER');
                       	break;
                case 'dehumidifier': $plugs_infos[$i]['translate']=__('PLUG_DEHUMIDIFIER');
                       	break;
		default: $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN');
			break;
					
	}
    $resume[$i+1]=format_data_sumary($plugs_infos[$i]["data"],$plugs_infos[$i]['PLUG_NAME'],$i+1,$plugs_infos[$i]['PLUG_TYPE']);

}

if((isset($sd_card))&&(!empty($sd_card))) {
      $program=create_program_from_database($error);
      if(!compare_program($program,$sd_card)) {
         if((empty($selected_plug))||(!isset($selected_plug))) {
            $info=$info.__('UPDATED_PROGRAM');
         }
         save_program_on_sd($sd_card,$program,$error);
      }
      check_and_copy_firm($sd_card,$error);
      check_and_copy_log($sd_card,$error);
}

if((strcmp($regul_program,"on")==0)||(strcmp($regul_program,"off")==0)) {
        $value_program="";
} 


if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                  $tmp="";
                  $tmp=__('INFO_UPDATE_AVAILABLE');
                  $tmp=str_replace("</li>","<a href=".$file[3]." target='_blank'>".$file[2]."</a></li>",$tmp);
                  $info=$info.$tmp;
               }
            }
      }
}


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
        clean_big_file("$sd_card/log.txt");    
    }
}

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


include('main/templates/programs.html');

?>
