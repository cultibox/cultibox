<?php

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$color_humidity = getvar('color_humidity');
$color_temperature = getvar('color_temperature');
$record_frequency=getvar('record_frequency');
$update_frequency=getvar('update_frequency');
$nb_plugs=getvar('nb_plugs');
$update_conf="false";
$return="";
$var = array();

if((isset($color_humidity))&&(!empty($color_humidity))) {
	insert_configuration("COLOR_HUMIDITY_GRAPH",$color_humidity,$return);
    	$update_conf="true";
} else {
	$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$return);
}


if((isset($color_temperature))&&(!empty($color_temperature))) {
	insert_configuration("COLOR_TEMPERATURE_GRAPH",$color_temperature,$return);
	$update_conf="true";
} else {
	$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$return);
}

if((isset($record_frequency))&&(!empty($record_frequency))) {
	insert_configuration("RECORD_FREQUENCY",$record_frequency,$return);
	$update_conf="true";
} else {
	$record_frequency = get_configuration("RECORD_FREQUENCY",$return);
}


if((isset($nb_plugs))&&(!empty($nb_plugs))) {
	insert_configuration("NB_PLUGS",$nb_plugs,$return);
	$update_conf="true";
} else {
	$nb_plugs = get_configuration("NB_PLUGS",$return);
}

if(!empty($update_frequency)) {
	insert_configuration("UPDATE_PLUGS_FREQUENCY",$update_frequency,$return);
	$update_conf="true";
} else {
	$update_frequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$return);
}


include('main/templates/configuration.html');

?>
