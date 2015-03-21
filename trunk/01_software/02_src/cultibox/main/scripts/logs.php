<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

$export_log=getvar('export_log');
$export_log_power=getvar('export_log_power');

// ================= VARIABLES ================= //
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);

$fake_log=false;

$second_regul=get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);

if(!isset($select_plug)) {
    $select_plug=getvar('select_plug');
}

if(!isset($startday)) {
    $startday=getvar('startday');
}

if(!isset($import_load)) {
    $import_load=getvar('import_load');
}

if(!isset($reload_import)) {
    $reload_import=getvar('reload_import');
}

if((!isset($import_load))||(empty($import_load))) {
    $import_load=2;
}

// Get is user want to display a day or a month
if(!isset($type)) {
    if(isset($_GET['type'])) {
        $type = getvar('type');
    }


    if((!isset($type))||(empty($type))) {
            $type = "day";
    }
}

// Check if there are logs recorded, delete fake logs if it's the case:
if(logs\check_export_table_csv("logs",$main_error) == true) {
    if(logs\are_fake_logs("1","","",$main_error)) {
        logs\reset_fake_log();
    }
}




//============================== GET OR SET CONFIGURATION PART ====================
//update_conf is used to define if there is an impact on SD card
$conf_arr["COLOR_HUMIDITY_GRAPH"]   = array ("update_conf" => "0", "var" => "color_humidity");
$conf_arr["COLOR_TEMPERATURE_GRAPH"]= array ("update_conf" => "0", "var" => "color_temperature");
$conf_arr["COLOR_WATER_GRAPH"]      = array ("update_conf" => "0", "var" => "color_water");
$conf_arr["COLOR_LEVEL_GRAPH"]      = array ("update_conf" => "0", "var" => "color_level");
$conf_arr["COLOR_PH_GRAPH"]         = array ("update_conf" => "0", "var" => "color_ph");
$conf_arr["COLOR_EC_GRAPH"]         = array ("update_conf" => "0", "var" => "color_ec");
$conf_arr["COLOR_OD_GRAPH"]         = array ("update_conf" => "0", "var" => "color_od");
$conf_arr["COLOR_ORP_GRAPH"]        = array ("update_conf" => "0", "var" => "color_orp");
$conf_arr["COLOR_PROGRAM_GRAPH"]    = array ("update_conf" => "0", "var" => "color_program");

foreach ($conf_arr as $key => $value) {
    ${$value['var']} = get_configuration($key,$main_error);
}

// Build array for y axis
$yaxis_array = array();
$yaxis_array[0] = program\get_curve_information('temperature');
$yaxis_array[1] = program\get_curve_information('humidity');
$yaxis_array[2] = program\get_curve_information('water');
$yaxis_array[3] = program\get_curve_information('level');
$yaxis_array[4] = program\get_curve_information('ph');
$yaxis_array[5] = program\get_curve_information('ec');
$yaxis_array[6] = program\get_curve_information('od');
$yaxis_array[7] = program\get_curve_information('orp');
$yaxis_array[8] = program\get_curve_information('power');
$yaxis_array[9] = program\get_curve_information('program');

// Used to alert user if export is could be done
$check_log  = logs\check_export_table_csv("logs",$main_error);
$check_power= logs\check_export_table_csv("power",$main_error);




// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { 
    if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}

// Read and update DB with index file
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) {
    $sensor_type = array(); 
    if (get_sensor_type($sd_card,$sensor_type))
    {
        // Update database with sensors
        update_sensor_type($sensor_type);
    }
    // Clean index file
    clean_index_file($sd_card);
}




//More default values:
if(!isset($startday) || empty($startday) || $reload_import) {
	$startday = program\get_last_day_with_logs();
} 
$startday=str_replace(' ','',"$startday");

if($startday == "") {
    $startday=date('Y')."-".date('m')."-".date('d');
    $fake_log=true;
}else {
    if(!check_format_date($startday,"days")) {
        $startday=date('Y')."-".date('m')."-".date('d');
    }
}


if((!isset($startmonth))||(empty($startmonth))) {
    $startmonth = "";
    if(isset($_GET['startmonth'])) {
        $startmonth=$_GET['startmonth'];
    } else {
        $startmonth=date('m');
    }
}


if((!isset($startyear))||(empty($startyear))) {
    $startyear = "";
    if(isset($_GET['startyear'])) {
        $startyear=$_GET['startyear'];
    } else {
        $startyear=date('Y');
    }
}


// Search previous selected curve
if(!isset($select_sensor)) {
    if(isset($_GET['select_sensor'])) {
        $select_sensor=getvar('select_sensor');
        if(!is_array($select_sensor)) $select_sensor=explode(",",$select_sensor);
    } else {
        $select_sensor[]="1";
    }
} else {
     $select_sensor=explode(",",$select_sensor);
}


if(!isset($select_power)) {
    if(isset($_GET['select_power'])) {
        $select_power = getvar('select_power');
        if(!is_array($select_power)) {
            $select_power=explode(",",$select_power);
        }
    } else {
        $select_power=array();
    }
} else {
    $select_power=explode(",",$select_power);
}



if(!isset($select_program)) {
    if(isset($_GET['select_program'])) {
        $select_program = getvar('select_program');
        if(!is_array($select_program)) {
            $select_program=explode(",",$select_program);
        }
    } else {
        $select_program=array();
    }
} else  {
    $select_program=explode(",",$select_program);
}


// If there is a second regul add tooltip
$resume_regul=array();
if($second_regul == "True") {
    // Create an array with all plug
    $resume_regul[0]="<p align='center'><b><i>".__('SUMARY_REGUL_TITLE')."</i></b></p><br />";
    for($i=1;$i<=$nb_plugs;$i++) {
        $resume_regul[$i]=format_regul_sumary($i,$main_error);
    }

    $plugsShow = array();
    foreach ($select_power as $sel_power)
    {
        $plugsShow[] = $sel_power;
    }
    foreach ($select_program as $sel_program)
    {
        $plugsShow[] = $sel_program;
    }
    
    // Remove doublon and sort
    $plugsShow = array_unique($plugsShow);
    asort($plugsShow);
} 

if($type == "month") {
    $legend_date=$startyear."-".$startmonth;
    $xlegend="XAXIS_LEGEND_MONTH";
} else {
    $legend_date = $startday;
    $xlegend="XAXIS_LEGEND_DAY";
}

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
