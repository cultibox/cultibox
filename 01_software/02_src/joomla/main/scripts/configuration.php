<?php

if (!isset($_SESSION)) {
	session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');



$error="";
$main_error="";

$color_humidity = getvar('color_humidity');
$color_temperature = getvar('color_temperature');
$color_program=getvar('color_program');
$color_power=getvar('color_power');
$record_frequency=getvar('record_frequency');
$power_frequency=getvar('power_frequency');
$update_frequency=getvar('update_frequency');
$nb_plugs=getvar('nb_plugs');
$lang=getvar('lang');
$update_conf=false;
$info="";
$temp_axis=getvar('temp_axis');
$hygro_axis=getvar('hygro_axis');
$power_axis=getvar('power_axis');
$pop_up=getvar('pop_up');
$pop_up_message="";
$pop_up_error_message="";
$alarm_enable=getvar('alarm_enable');
$alarm_value=getvar('alarm_value');
$alarm_senso=getvar('alarm_senso');
$alarm_senss=getvar('alarm_senss');
$program="";

if((isset($lang))&&(!empty($lang))) {
	insert_configuration("LANG",$lang,$error);
} else {
	$lang=get_configuration("LANG",$error);
}

set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((!empty($sd_card))&&(isset($sd_card))) {
   $program=create_program_from_database($error);
   if(!compare_program($program,$sd_card)) {
      $info=$info.__('UPDATED_PROGRAM');
      $pop_up_message=clean_popup_message(__('UPDATED_PROGRAM'));
      save_program_on_sd($sd_card,$program,$error,$info);
   }
   check_and_copy_firm($sd_card,$error);
} else {

        $tmp="";
        $tmp=__('ERROR_SD_CARD_CONF');
        $tmp_title=__('TOOLTIP_WITHOUT_SD');
        $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
        $main_error=$main_error.$tmp;
}


if((isset($pop_up))&&(!empty($pop_up))) {
	insert_configuration("SHOW_POPUP","$pop_up",$error);
        $update_conf=true;
} else {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((isset($color_humidity))&&(!empty($color_humidity))) {
	insert_configuration("COLOR_HUMIDITY_GRAPH",$color_humidity,$error);
    	$update_conf=true;
} else {
	$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$error);
}


if((isset($color_temperature))&&(!empty($color_temperature))) {
	insert_configuration("COLOR_TEMPERATURE_GRAPH",$color_temperature,$error);
	$update_conf=true;
} else {
	$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$error);
}

if((isset($color_program))&&(!empty($color_program))) {
   insert_configuration("COLOR_PROGRAM_GRAPH",$color_program,$error);
   $update_conf=true;
} else {
   $color_program = get_configuration("COLOR_PROGRAM_GRAPH",$error);
}

if((isset($color_power))&&(!empty($color_power))) {
   insert_configuration("COLOR_POWER_GRAPH",$color_power,$error);
   $update_conf=true;
} else {
   $color_power = get_configuration("COLOR_POWER_GRAPH",$error);
}

if((isset($temp_axis))&&(!empty($temp_axis))) {
        insert_configuration("LOG_TEMP_AXIS",$temp_axis,$error);
        $update_conf=true;
} else {
        $temp_axis = get_configuration("LOG_TEMP_AXIS",$error);
}


if((isset($hygro_axis))&&(!empty($hygro_axis))) {
        insert_configuration("LOG_HYGRO_AXIS",$hygro_axis,$error);
        $update_conf=true;
} else {
        $hygro_axis = get_configuration("LOG_HYGRO_AXIS",$error);
}

if((isset($power_axis))&&(!empty($power_axis))) {
        insert_configuration("LOG_POWER_AXIS",$power_axis,$error);
        $update_conf=true;
} else {
        $power_axis = get_configuration("LOG_POWER_AXIS",$error);
}


if((isset($record_frequency))&&(!empty($record_frequency))) {
	insert_configuration("RECORD_FREQUENCY",$record_frequency,$error);
	$update_conf=true;
} else {
	$record_frequency = get_configuration("RECORD_FREQUENCY",$error);
}

if((isset($power_frequency))&&(!empty($power_frequency))) {
        insert_configuration("POWER_FREQUENCY",$power_frequency,$error);
        $update_conf=true;
} else {
        $power_frequency = get_configuration("POWER_FREQUENCY",$error);
}


if((isset($nb_plugs))&&(!empty($nb_plugs))) {
	insert_configuration("NB_PLUGS",$nb_plugs,$error);
	$update_conf=true;
} else {
	$nb_plugs = get_configuration("NB_PLUGS",$error);
}

if(!empty($update_frequency)) {
	insert_configuration("UPDATE_PLUGS_FREQUENCY",$update_frequency,$error);
	$update_conf=true;
} else {
	$update_frequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$error);
}


if(!empty($alarm_enable)) {
        insert_configuration("ALARM_ACTIV","$alarm_enable",$error);
        $update_conf=true;
} else {
        $alarm_enable = get_configuration("ALARM_ACTIV",$error);
}

if(!empty($alarm_value)) {
	if((check_numeric_value("$alarm_value"))&&(check_alarm_value("$alarm_value"))) {
           insert_configuration("ALARM_VALUE","$alarm_value",$error);
           $update_conf=true;
        } else {
           $alarm_value=get_configuration("ALARM_VALUE",$error);
           $error=$error.__('ERROR_ALARM_VALUE');
           $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
        }
} else {
        $alarm_value = get_configuration("ALARM_VALUE",$error);
}

if(!empty($alarm_senso)) {
        insert_configuration("ALARM_SENSO","$alarm_senso",$error);
        $update_conf=true;
} else {
        $alarm_senso = get_configuration("ALARM_SENSO",$error);
}

if(!empty($alarm_senss)) {
        insert_configuration("ALARM_SENSS","$alarm_senss",$error);
        $update_conf=true;
} else {
        $alarm_senss = get_configuration("ALARM_SENSS",$error);
}

if((empty($error))||(!isset($error))) {
	if($update_conf) {
        if((!empty($sd_card))&&(isset($sd_card))) {
			   $info=$info.__('VALID_UPDATE_CONF');
			   $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_CONF'));
         } else {
            $info=$info.__('VALID_UPDATE_CONF_WITHOUT_SD');
            $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_CONF_WITHOUT_SD'));
         }
	}
}

if((isset($sd_card))&&(!empty($sd_card))) {
	if("$update_frequency"=="-1") {
		write_sd_conf_file($sd_card,$record_frequency,"0",$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$error);
	} else {
		write_sd_conf_file($sd_card,$record_frequency,$update_frequency,$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$error);	
	}	
   $info=$info.__('INFO_SD_CARD').": $sd_card";
}


include('main/templates/configuration.html');

?>
