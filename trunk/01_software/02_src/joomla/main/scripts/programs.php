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
$wzd="";
$nb_plugs=get_configuration("NB_PLUGS",$error);
$selected_plug=getvar('selected_plug');
$plugs_infos=get_plugs_infos($nb_plugs,$error);
$exportid=getvar('exportid');
$finish=getvar('finish');
$wzd=getvar('wzd');
$step=getvar('step');
$info_plug=array();

$export=getvar('export');
$import=getvar('import');
$reset=getvar('reset');
$action_prog=getvar('action_prog');
$chinfo=true;
$chtime="";
$pop_up_message="";
$pop_up_error_message="";

if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}


for($i=0;$i<=$nb_plugs;$i++) {
		export_program($i,$error);
}


for($i=0;$i<=$nb_plugs;$i++) {
        $info_plug[]="";
        $ret_plug[]="";
}

	

if((isset($action_prog))&&(!empty($action_prog))) {
	if((isset($import))&&(!empty($import))) {


	} else if((isset($reset))&&(!empty($reset))) {
		if(clean_program($action_prog,$error)) {
			$info_plug[$action_prog]=$info_plug[$action_prog].__('INFO_RESET_PROGRAM');
                }
	} 
} 

if((isset($finish))&&(!empty($finish))&&($step==3)) {
        $program=getvar('program');

	if((isset($program))&&(!empty($program))) {
		$value_program="99.9";
		$selected_plug=1;
		$plug_type="lamp";
                $start_time=getvar('start_time');
                $end_time=getvar('end_time');

              	$chtime=check_times($start_time,$end_time,$error); 
		if((isset($error))&&(!empty($error))) {
                           $pop_up_error_message=clean_popup_message($error);
                }
		//$chval=check_format_values_program($value_program);
		$chval=true;
	
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
                        foreach($prog as $val) {	
                                if(insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$error)) {
				       insert_program($val["selected_plug"],$val["start_time"],$val["end_time"],$val["value_program"],$error);
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
				unset($wzd);
                                header('Location: programs');	
			}
                } 
        }
}

if((!isset($wzd))||(empty($wzd))) {
		$wzd="False";
}

if((((empty($finish))&&($step==3))||("$wzd"=="True"))&&("$wzd"!="False")) {
	$info=$info.__('WIZARD_DISABLE_FUNCTION');

	$step=getvar('step');
	$next=getvar('next');
	$previous=getvar('previous');
	$start_time="00:00:00";
	$end_time="00:00:00";

	if((!isset($step))||(empty($step))||(!is_numeric($step))||($step<0)) {
		$step=1;
	} else if((isset($next))&&(!empty($next))) {
		$step=$step+1;	
	} else if((isset($previous))&&(!empty($previous))) {
		$step=$step-1;
	}

	include('main/templates/wizard.html');
} else {

	unset($finish);
	unset($wzd);
	unset($step);

	$info=$info.__('WIZARD_ENABLE_FUNCTION');

	if((isset($exportid))&&(!empty($exportid))) {
		export_program($exportid);
	}

	if((!isset($sd_card))||(empty($sd_card))) {
		$sd_card=get_sd_card();
	}

	if((!isset($sd_card))||(empty($sd_card))) {
        	$error=$error.__('ERROR_SD_CARD_CONF');
	} else {
        	$info=$info.__('INFO_SD_CARD').": $sd_card";
	}


	if(!empty($selected_plug)&&(isset($selected_plug))) { 
	$start="start_time{$selected_plug}";
	$end="end_time{$selected_plug}";
	$value="value_program{$selected_plug}";
	$start_time=getvar($start);
	$end_time=getvar($end);
	$force="force_on{$selected_plug}";
	$force_on=getvar($force);



	if((isset($force_on))&&(!empty($force_on))) {
        	$value_program=99.9;
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
					if((!isset($force_on))||(empty($force_on))) {
						$check=check_format_values_program($value_program);
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
							}
						}
					}
					if((isset($pop_up_message))&&(!empty($pop_up_message))) {
						unset($pop_up_message);
					} else {
						if(count($info_plug)>0) {
                                                        $pop_up_message=clean_popup_message(__('INFO_VALID_UPDATE_PROGRAM'));
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
            case 'dehumidifier': $plugs_infos[$i]['translate']=__('PLUG_DESHUMIDIFIER');
                        	break;
			default: $plugs_infos[$i]['translate']=__('PLUG_UNKNOWN');
							break;
					
		}
	}

	if((isset($sd_card))&&(!empty($sd_card))) {
        	$program=create_program_from_database($error);
        	save_program_on_sd($sd_card,$program,$error,$info);
	        check_and_copy_firm($sd_card,$error);
	}

	if((isset($force_on))&&(!empty($force_on))) {
                $value_program="";
	}

	include('main/templates/programs.html');
}

?>
