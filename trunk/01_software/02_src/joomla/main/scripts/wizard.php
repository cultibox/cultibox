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
$finish=getvar('finish');
$info_plug=array();
$nb_plugs=get_configuration("NB_PLUGS",$error);
$selected_plug=getvar('selected_plug');
$next_plug=getvar('next_plug');
$close=getvar('close');
$program="";
$pop_up="";
$pop_up_message="";
$pop_up_error_message="";
$main_error="";
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);

if((!isset($pop_up))||(empty($pop_up))) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}

$info=$info.__('WIZARD_DISABLE_FUNCTION');


if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   if(!compare_program($program,$sd_card)) {
      $info=$info.__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$error);
   }
   check_and_copy_firm($sd_card,$error);
   check_and_copy_log($sd_card,$error);
} else {
   $tmp="";
   $tmp=__('ERROR_SD_CARD_CONF');
   $tmp_title=__('TOOLTIP_WITHOUT_SD');
   $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
   $main_error=$main_error.$tmp;
}

if((isset($close))&&(!empty($close))) {
        header('Location: plugs');
}

if((empty($selected_plug))||(!isset($selected_plug))) {
     $selected_plug=1;
}



$chinfo=true;
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
	$plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$error); 
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
		case 'unknown' :
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



              	$chtime=check_times($start_time,$end_time,$error); 
		if(strcmp($plug_type,"lamp")!=0) {
                   if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
		      $chval=check_format_values_program($value_program,$error,"temp");
                   } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"deshumidifier")==0)) {
                      $chval=check_format_values_program($value_program,$error,"humi");
                   } else {
                      $chval=check_format_values_program($value_program,$error,"unknown");
                   }
                   $plug_tolerance="1.0";
 		} else {
                     $chval=true;
			$plug_tolerance="0.0";
                }

                if(($chtime)&&($chval)) {
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


			clean_program($selected_plug,$error);	
			if(isset($plug_tolerance)) {
                                   insert_plug_conf("PLUG_TOLERANCE",$selected_plug,$plug_tolerance,$error);
			}
         foreach($prog as $val) {	
            if(insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$error)) {
				       insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$error);
                   if((!empty($sd_card))&&(isset($sd_card))) {
                        $program=create_program_from_database($error);
                        $info=$info.__('VALID_UPDATE_PROGRAM');
                        save_program_on_sd($sd_card,$program,$error);
                  } 
                   if($chinfo) {
                             $chinfo=true;
                   }
				       insert_plug_conf("PLUG_TYPE",$val["selected_plug"],$val["plug_type"],$error);
            } else {
					$chinfo=false;
					unset($finish);
                                }
                       }

		      if($chinfo) {
                if(($selected_plug==$nb_plugs)||((isset($finish))&&(!empty($finish)))) {
                        header('Location: programs');	
                }
			   }
                } else {
			if((isset($error))&&(!empty($error))) {
			   $pop_up_error_message=clean_popup_message($error);
                        }
               }
        }
        if((isset($next_plug))&&(!empty($next_plug))&&(empty($error))) {
			      $selected_plug=$selected_plug+1;
			      $step=2;
        }
        
}

if((!empty($selected_plug))&&(isset($selected_plug))) {
   $plug_name=get_plug_conf("PLUG_NAME",$selected_plug,$error);
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

if((isset($sd_card))&&(!empty($sd_card))) {
   $info=$info.__('INFO_SD_CARD').": $sd_card";
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


include('main/templates/wizard.html');

?>
