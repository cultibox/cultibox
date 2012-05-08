<?php

if (!isset($_SESSION)) {
	session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$color_humidity = getvar('color_humidity');
$color_temperature = getvar('color_temperature');
$record_frequency=getvar('record_frequency');
$update_frequency=getvar('update_frequency');
$nb_plugs=getvar('nb_plugs');
$lang=getvar('lang');
$update_conf=false;
$error="";
$info="";

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

if((!isset($sd_card))||(empty($sd_card))) {
        $error=$error.__('ERROR_SD_CARD_CONF');
} else {
        $info=$info.__('INFO_SD_CARD').": $sd_card";
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

if((isset($record_frequency))&&(!empty($record_frequency))) {
	insert_configuration("RECORD_FREQUENCY",$record_frequency,$error);
	$update_conf=true;
} else {
	$record_frequency = get_configuration("RECORD_FREQUENCY",$error);
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

if((empty($error))||(!isset($error))) {
	if($update_conf) {
		$info=$info.__('VALID_UPDATE_CONF');
	}
}

if((isset($sd_card))&&(!empty($sd_card))) {
	if("$update_frequency"=="-1") {
		write_sd_conf_file($sd_card,$record_frequency,"0",$error);
	} else {
		write_sd_conf_file($sd_card,$record_frequency,$update_frequency,$error);	
	}	
}


include('main/templates/configuration.html');

?>
