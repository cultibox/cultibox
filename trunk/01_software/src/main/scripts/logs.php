<?php

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$return);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

$return="";

$startdate = getvar('startdate');
$type = getvar('type');
if((!isset($startdate))||(empty($startdate))) {
  $startdate=date('Y')."-".date('m')."-".date('d');
} else if("$type" == "month") { 
  $legend_date=date('Y')."-".$startdate;
  $bmonth=$startdate;
  $startdate=date('Y')."-".$startdate."-%";
}

if((!isset($legend_date))||(empty($legend_date))) {
  $legend_date=$startdate;
}

// load logs
$log = array();
$return = "";
for ($month = 1; $month <= 12; $month++) {
  for ($day = 1; $day <= 31; $day++) {
    if($day<10) {
      $dday="0".$day;
    } else {
      $dday=$day;
    }
    if($month<10) {
         $mmonth="0".$month;
      } else {
      $mmonth=$month;
    }
      // Search if file exists
      if(file_exists($GLOBALS['DATE_DIR_PATH']."/logs/$mmonth/$dday")) {
       // get log value
       get_log_value($GLOBALS['DATE_DIR_PATH']."/logs/$mmonth/$dday",$log);
       if(!empty($log)) {
            db_update_logs($log,$return);
            unset($log) ;
            $log = array();
         }
      }
  }
}

$temperature= array();
$humidity = array();
$axis= array();

get_graph_array($temperature,"temperature/100",$startdate,$return);
get_graph_array($humidity,"humidity/100",$startdate,$return);

if("$type" != "month") {
  get_graph_array($axis,"time_catch",$startdate,$return);
  get_format_hours($axis);
  $xlegend="XAXIS_LEGEND_DAY";
} else {
  $xlegend="XAXIS_LEGEND_MONTH";
  $axis = array();
  get_format_month($axis,5,$bmonth,date('Y'));
}

if("$type" == "month") {
         $startdate=date('Y')."-".date('m')."-".date('d');
}

if((!isset($bmonth))||(empty($bmonth))) {
  $bmonth=date('m');
}

$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$return);
$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$return);

include('main/templates/logs.html');

?>
