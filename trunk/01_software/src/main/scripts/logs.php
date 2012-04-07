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
$type="";
$temperature= array();
$humidity = array();

$startday = getvar('startday');
$startmonth=getvar('startmonth');

if((!isset($startday))||(empty($startday))) {
	$startday=date('Y')."-".date('m')."-".date('d');
} else {
	$type = "days";
}
$startday=str_replace(' ','',"$startday");


if((!isset($startmonth))||(empty($startmonth))) {
        $startmonth=date('m');
} else {
        $type = "month";
}

if((!isset($type))||(empty($type))){
	$type="days";
}


if("$type"=="days") {
	$legend_date=$startday;
	$check_format=check_format_date($startday,$type,$return);
} else {
	$legend_date=date('Y')."-".$startmonth;	
	$check_format=check_format_date($startmonth,$type,$return);
}


$log = array();
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


if("$type" == "days") {
	if($check_format) {
		$xlegend="XAXIS_LEGEND_DAY";
        	$styear=substr($startday, 0, 4);
        	$stmonth=substr($startday, 5, 2);
        	$stday=substr($startday, 8, 2);

		get_graph_array($temperature,"temperature/100",$startday,$return);
		get_graph_array($humidity,"humidity/100",$startday,$return);

		if(!empty($temperature)) {
			$data_temp=get_format_graph($temperature);
		} else {
			$return=$return.__('EMPTY_TEMPERATURE_DATA');
		}

		if(!empty($humidity)) {
			$data_humi=get_format_graph($humidity);
		} else {
			$return=$return.__('EMPTY_HUMIDITY_DATA');
		}
		$next=1;
	} 
} else {
	if($check_format) {
		$nb = date('t',mktime(0, 0, 0, $startmonth, 1, date('Y'))); 
		for($i=1;$i<=$nb;$i++) {
			if($i<10) {
				$i="0$i";
			}
			$ddate=date('Y')."-$startmonth-$i";
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
		$data_temp=get_format_month($data_temp);
		$data_humi=get_format_month($data_humi);
		$xlegend="XAXIS_LEGEND_MONTH";
		$styear=date('Y');
		$stmonth=$startmonth-1;
		$stday=1;
		$next=20;
	} 
}

$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$return);
$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$return);

include('main/templates/logs.html');


?>
