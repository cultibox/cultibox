<?php

if (!isset($_SESSION)) {
	session_start();
}


/* Libraries requiered: 
        db_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
*/
require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once('main/libs/db_set_common.php');
require_once('main/libs/utilfunc.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');


// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

$export_log=getvar('export_log');
$export_log_power=getvar('export_log_power');

// ================= VARIABLES ================= //
$type="";
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);

$fake_log=false;

$second_regul=get_configuration("SECOND_REGUL",$main_error);
$select_plug=getvar('select_plug');
$startday=getvar('startday');
$import_load=getvar('import_load');

if((!isset($import_load))||(empty($import_load))) {
    $import_load=2;
}

// Get is user want to display a day or a month
$type = "day";
if(isset($_POST['type_select'])) {
    $type = getvar('type_select');
}

// Check if there are logs recorded, delete fake logs if it's the case:
if(!logs\check_export_table_csv("logs",$main_error)) {
     logs\reset_fake_log();
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
$conf_arr["COLOR_POWER_GRAPH"]      = array ("update_conf" => "0", "var" => "color_power");

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

if((isset($export_log))&&(!empty($export_log))) {
    logs\export_table_csv("logs",$main_error);
    $file="tmp/logs.csv";
     if (($file != "") && (file_exists("./$file"))) {
        $size = filesize("./$file");
        header("Content-Type: application/force-download; name=\"$file\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $size");
        header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        ob_clean();
        flush();
        ob_end_flush();
        readfile("./$file");
        exit();
     }
}

if((isset($export_log_power))&&(!empty($export_log_power))) {
     logs\export_table_csv("power",$main_error);
     $file="tmp/power.csv";
     if (($file != "") && (file_exists("./$file"))) {
        $size = filesize("./$file");
        header("Content-Type: application/force-download; name=\"$file\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $size");
        header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        ob_clean();
        flush();
        ob_end_flush();
        readfile("./$file");
        exit();
     }
}

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}


if((!isset($sd_card))||(empty($sd_card))) {
   $main_error[]=__('ERROR_SD_CARD');
}


// Read and update DB with index file
$sensor_type = array(); 
if (get_sensor_type($sd_card,$sensor_type))
{
    // Update database with sensors
    update_sensor_type($sensor_type);
}

// Clean index file
clean_index_file($sd_card);


//More default values:
if(!isset($startday) || empty($startday)) {
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

$startmonth = "";
if(isset($_POST['startmonth'])) {
    $startmonth=$_POST['startmonth'];
} else {
    $startmonth=date('m');
}

$startyear = "";
if(isset($_POST['startyear'])) {
    $startyear=$_POST['startyear'];
} else {
    $startyear=date('Y');
}


// Search previous selected curve
$select_sensor = array();
if(isset($_POST['select_sensor'])) {
    $select_sensor=getvar('select_sensor');
    if(!is_array($select_sensor)) {
        $select_sensor=explode(",",$select_sensor);
    }
} else {
    $select_sensor[]="1";
}


$select_power = array();
if(isset($_POST['select_power'])) {
    $select_power = getvar('select_power');
    if(!is_array($select_power)) {
        $select_power=explode(",",$select_power);
    }
} 

$select_program = array();
if(isset($_POST['select_program'])) {
    $select_program = getvar('select_program');
    if(!is_array($select_program)) {
        $select_program=explode(",",$select_program);
    }
}


// If there is a second regul add tooltip
$resume_regul="";
if($second_regul == "True") {

    // Create an array with all plug
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
    
    // Add summary for power
    foreach ($plugsShow as $plugShow)
    {
        format_regul_sumary($plugShow,$main_error,$resume_regul,$nb_plugs);
    }

    if(!empty($resume_regul)) {
        $resume_regul="<p align='center'><b><i>".__('SUMARY_REGUL_TITLE')."</i></b></p><br />".$resume_regul;
    } else {
        if((count($select_power)==0)&&(empty($select_plug))) {
            $resume_regul="<p align='center'><b><i>".__('SUMARY_REGUL_TITLE')."</i></b><br /><br />".__('SUMARY_EMPTY_SELECTION')."</p>";
        } else {
            $resume_regul="<p align='center'><b><i>".__('SUMARY_REGUL_TITLE')."</i></b><br /><br />".__('SUMARY_EMPTY_REGUL')."</p>";
        }
    }
    $resume_regul=$resume_regul."<br />";
} 

if($type == "month") {
    $legend_date=$startyear."-".$startmonth;
    $xlegend="XAXIS_LEGEND_MONTH";
} else {
    $legend_date = $startday;
    $xlegend="XAXIS_LEGEND_DAY";
}

// Include in html pop up and message
include('main/templates/post_script.php');

//Display the logs template
include('main/templates/display-logs.html');

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
