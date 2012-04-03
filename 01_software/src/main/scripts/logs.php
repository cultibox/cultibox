<?php

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$return);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');

$return="";
$type="";

$startdate = getvar('startdate');
$type = getvar('type');
if((!isset($startdate))||(empty($startdate))) {
  $startdate=date('Y')."-".date('m')."-".date('d');
  $bmonth=date('m');
} else if("$type" == "month") { 
  $legend_date=date('Y')."-".$startdate;
  $bmonth=$startdate;
  $startdate=date('Y')."-".date('m')."-".date('d');
} else {
	$bmonth=date('m');
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

if("$type" != "month") {
	$xlegend="XAXIS_LEGEND_DAY";
        $styear=substr($startdate, 0, 4);
        $stmonth=substr($startdate, 5, 2)-1;
        $stday=substr($startdate, 8, 2);
	get_graph_array($temperature,"temperature/100",$startdate,$return);
	get_graph_array($humidity,"humidity/100",$startdate,$return);
	$data_temp=get_format_graph($temperature);
	$data_humi=get_format_graph($humidity);
} else {
	$nb = date('t',mktime(0, 0, 0, $bmonth, 1, date('Y'))); 
	for($i=1;$i<=$nb;$i++) {
		if($i<10) {
			$i="0$i";
		}
		$ddate=date('Y')."-$bmonth-$i";
		get_graph_array($temperature,"temperature/100","$ddate",$return);
		get_graph_array($humidity,"humidity/100","$ddate",$return);
		if("$data_temp" != "" ) {
			$data_temp="$data_temp, ".get_format_graph($temperature);
		} else {
			$data_temp=get_format_graph($temperature);
		}

		if("$data_humi" != "" ) {
                        $data_humi="$data_humi, ".get_format_graph($humidity);
                } else {
                        $data_humi=get_format_graph($humidity);
                }
		
		$temperature = array();
		$humidity=array();
	}
	$xlegend="XAXIS_LEGEND_MONTH";
	$axis = array();
	get_format_month($axis,5,$bmonth,date('Y'));
	$styear=date('Y');
	$stmonth=$bmonth-1;
	$stday=1;
}

$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$return);
$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$return);

include('main/templates/logs.html');

?>
