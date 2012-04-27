<?php

if (!isset($_SESSION)) {
	session_start();
}


require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$return);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

$return="";
$ret_plug=array();
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$return);
$selected_plug=getvar('selected_plug');
$plugs_infos=get_plugs_infos($nb_plugs,$return);
$exportid=getvar('exportid');

if((isset($exportid))&&(!empty($exportid))) {
	export_program($exportid);
}


if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}


if((!isset($sd_card))||(empty($sd_card))) {
        $return=$return.__('ERROR_SD_CARD_CONF');
} else {
        $info=$info.__('INFO_SD_CARD').": $sd_card";
}


if(!empty($selected_plug)&&(isset($selected_plug))) { 
	$start="start_time{$selected_plug}";
	$end="end_time{$selected_plug}";
	$value="value_program{$selected_plug}";
	$start_time=getvar($start);
	$end_time=getvar($end);
	$value_program=getvar($value);

        if(("$start_time"!="")&&("$end_time"!="")) {
		if(check_times($start_time,$end_time,$ret_plug[$selected_plug])) {
				if("$value_program"=="on") $value_program="1";
				if("$value_program"=="off") $value_program="0";
				if(insert_program($selected_plug,$start_time,$end_time,$value_program,$ret_plug[$selected_plug])) {
						$info_plug[$selected_plug]=$info_plug[$selected_plug].__(INFO_VALID_UPDATE_PROGRAM);
				}
		}
	} else {
		$ret_plug[$selected_plug]=$$ret_plug[$selected_plug].__('ERROR_MISSING_VALUE_TIME');
	}
}

for($i=0;$i<$nb_plugs;$i++) {
	$data_plug=get_data_plug($i+1,$return);
        $plugs_infos[$i][data]=format_program_highchart_data($data_plug);
	switch($plugs_infos[$i][PLUG_TYPE]) {
		case 'unknown': $plugs_infos[$i][translate]=__(PLUG_UNKNOWN);
			break;
		case 'ventilator': $plugs_infos[$i][translate]=__(PLUG_VENTILATOR);
                        break;
		case 'heating': $plugs_infos[$i][translate]=__(PLUG_HEATING);
                        break;	
		case 'lamp': $plugs_infos[$i][translate]=__(PLUG_LAMP);
                        break;
		case 'humidifier': $plugs_infos[$i][translate]=__(PLUG_HUMIDIFIER);
                        break;
                case 'dehumidifier': $plugs_infos[$i][translate]=__(PLUG_DESHUMIDIFIER);
                        break;	
	}
}

if((isset($sd_card))&&(!empty($sd_card))) {
        $info=$info.__(INFO_SD_CARD).": $sd_card";
        $program=create_program_from_database();
        save_program_on_sd($sd_card,$program,$return,$info);
}

include('main/templates/programs.html');

?>
