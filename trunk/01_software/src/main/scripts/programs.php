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
$info="";
$nb_plugs=get_configuration("NB_PLUGS",$return);
$selected_plug=getvar('selected_plug');
$plugs_infos=get_plugs_names($nb_plugs,$return);



if((!isset($sd_card))||(empty($sd_card))) {
	$sd_card=get_sd_card();
}

if(!empty($selected_plug)&&(isset($selected_plug))) { 
	$start="start_time{$selected_plug}";
	$end="end_time{$selected_plug}";
	$value="value_program{$selected_plug}";
	$start_time=getvar($start);
	$end_time=getvar($end);
	$value_program=getvar($value);

        if(("$start_time"!="")||("$end_time"!="")) {
		if(check_times($start_time,$end_time,$return)) {
			if((!empty($value_program))&&(isset($value_program))) {
				if("$value_program"=="on") $value_program="1";
				if("$value_program"=="off") $value_program="0";
				insert_program($selected_plug,$start_time,$end_time,$value_program,$return);
			}
		}
	}
}

for($i=1;$i<=$nb_plugs;$i++) {
	$data_plug=get_data_plug($i,$return);
        $data{$i}=format_program_highchart_data($data_plug);
}

if((!isset($sd_card))||(empty($sd_card))) {
        $return=$return.__('ERROR_SD_CARD_PROGRAMS');
} else {
        $info=$info.__('INFO_SD_CARD').": $sd_card";
        $program=create_program_from_database();
        save_program_on_sd($sd_card,$program,$return,$info);
}



include('main/templates/programs.html');

?>
