<?php


// Compute page time loading for debug option
$start_load = getmicrotime();


// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();

// ================= VARIABLES ================= //
$informations = Array();
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$wifi_ip=get_configuration("WIFI_IP",$main_error);

$type_sensor[]=__('NA','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_TEMPHUMI','raw');
$type_sensor[]=__('SENSOR_T_WATER','raw');
$type_sensor[]=__('NA','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');
$type_sensor[]=__('SENSOR_LEVEL_W','raw');


//Translate for Jquery:
$translate[]=__('NA');
$translate[]=__('DIMMER');


//Set plug type translation:
for($i=0; $i<count($plugs_infos);$i++) {
    $plugs_infos[$i]['translate'] = translate_PlugType($plugs_infos[$i]['PLUG_TYPE']);
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

$sensor=logs\get_sensor_db_type();

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