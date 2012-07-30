<?php


if (!isset($_SESSION)) {
	session_start();
}


require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

$lang=get_configuration("LANG",$error);
set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


echo <<<EOF
<!-- DEBUT DU SCRIPT -->
<STYLE TYPE="text/css">
<!--
#cache {
    position:absolute; top:200px; z-index:10; visibility:hidden;
}
-->
</STYLE>
<DIV ID="cache" align="center"><TABLE WIDTH="100%"  BGCOLOR=#000000 BORDER=0 CELLPADDING=2 CELLSPACING=0><TR><TD ALIGN="center" VALIGN=middle><TABLE WIDTH="100%" BGCOLOR=#FFFFFF BORDER=0 CELLPADDING=0 CELLSPACING=0><TR><TD ALIGN=center VALIGN=middle><FONT FACE="Verdana" SIZE=4 COLOR=#000000><BR>
EOF;
echo __('WAITING_LOG');

echo <<<EOF
<BR><BR></FONT></TD>  </TR></TABLE></TD>  </TR></TABLE></DIV>

<SCRIPT LANGUAGE="JavaScript">
var nava = (document.layers);
var dom = (document.getElementById);
var iex = (document.all);
if (nava) { cach = document.cache }
else if (dom) { cach = document.getElementById("cache").style }
else if (iex) { cach = cache.style }
largeur = screen.width;
cach.left = Math.round((largeur/2)-200);
cach.visibility = "visible";

function cacheOff()
        {
        cach.visibility = "hidden";
        }
window.onload = cacheOff
</SCRIPT>
EOF;

ob_flush();
flush();
ob_flush();
flush(); 
ob_implicit_flush();

$error="";
$info="";
$type="";
$temperature= array();
$humidity = array();
$nb_plugs=get_configuration("NB_PLUGS",$error);
$plugs_infos=get_plugs_infos($nb_plugs,$error);
$select_plug=getvar('select_plug');
$data_temp="";
$data_humi="";
$plug_type="";
$hygro_axis=get_configuration("LOG_HYGRO_AXIS",$error);
$temp_axis=get_configuration("LOG_TEMP_AXIS",$error);
$fake_log=false;
$program="";
$pop_up="";
$pop_up_error_message="";


if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}

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
   $error=$error.__('ERROR_SD_CARD_CONF');
}

$startday = getvar('startday');
$startmonth=getvar('startmonth');
$startyear=getvar('startyear');
$load_log=false;

if((!isset($startday))||(empty($startday))) {
	$startday=date('Y')."-".date('m')."-".date('d');
} else {
	$type = "days";
}
$startday=str_replace(' ','',"$startday");


if((!isset($startmonth))||(empty($startmonth))) {
        $startmonth=date('m');
	$startyear=date('Y');
} else {
        $type = "month";
}

if((!isset($type))||(empty($type))){
	$type="days";
}


$log = array();
$power=array();
$load_log=false;
if((isset($sd_card))&&(!empty($sd_card))) {
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
			if(file_exists("$sd_card/logs/$mmonth/$dday")) {
 				// get log value
 				get_log_value("$sd_card/logs/$mmonth/$dday",$log);

            if(!is_file("$sd_card/logs/$mmonth/pwr_$dday")) {
               copy("main/templates/data/empty_file_big.tpl", "$sd_card/logs/$mmonth/pwr_$dday");
            } else {
               get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power);
            }
 				if(!empty($log)) {
   				if(db_update_logs($log,$error)) {
                  clean_log_file("$sd_card/logs/$mmonth/$dday");
               }
   			   unset($log) ;
   				$log = array();
 				   $load_log=true;
   			}
            //if(!empty($power)) {
            //   if(db_update_power($power,$error)) {
           //       clean_power_file("$sd_card/logs/$mmonth/pwr_$dday");
            //   }
            //   unset($power) ;
            //   $power = array();
            //   $load_log=true;
            //}
			}
  		}
	}
}

if(($load_log)&&(empty($error))) {
	$info=$info.__('VALID_LOAD_LOG');
} 

if("$type"=="days") {
   $legend_date=$startday;
   $check_format=check_format_date($startday,$type,$error);
} else {
   $legend_date=date('Y')."-".$startmonth;
   $check_format=check_format_date($startmonth,$type,$error);
}

if("$type" == "days") {
	if($check_format) {
		if((isset($select_plug))&&(!empty($select_plug))) {
			$data_plug=get_data_plug($select_plug,$error);
 	      $data=format_program_highchart_data($data_plug,$startday);
			$plug_type=get_plug_conf("PLUG_TYPE",$select_plug,$error);
      }
      $xlegend="XAXIS_LEGEND_DAY";
     	$styear=substr($startday, 0, 4);
     	$stmonth=substr($startday, 5, 2)-1;
     	$stday=substr($startday, 8, 2);
      
      get_graph_array($temperature,"temperature/100",$startday,"False",$error);
      get_graph_array($humidity,"humidity/100",$startday,"False",$error);


      if(!empty($temperature)) {
         $data_temp=get_format_graph($temperature);
      } else {
       get_graph_array($temperature,"temperature/100",$startday,"True",$error);
       $error=$error.__('EMPTY_TEMPERATURE_DATA');
      
       if(!empty($temperature)) {
          $data_temp=get_format_graph($temperature);
          $fake_log=true;
       }
      }

      if(!empty($humidity)) {
       $data_humi=get_format_graph($humidity);
      } else {
       get_graph_array($humidity,"humidity/100",$startday,"True",$error);
       $error=$error.__('EMPTY_HUMIDITY_DATA');
      
         if(!empty($humidity)) {
            $fake_log=true;
            $data_humi=get_format_graph($humidity);
         } 
      }
      $next=1;
	} 
} else {
	if($check_format) {
		$nb = date('t',mktime(0, 0, 0, $startmonth, 1, $startyear)); 
		for($i=1;$i<=$nb;$i++) {
			if($i<10) {
				$i="0$i";
			}
			$ddate="$startyear-$startmonth-$i";
                        get_graph_array($temperature,"temperature/100","$ddate","False",$error);
                        get_graph_array($humidity,"humidity/100","$ddate","False",$error);
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
		if("$data_humi" == "" ) {
			$error=$error.__('EMPTY_HUMIDITY_DATA');
		}
		if("$data_temp" == "" ) {
			$error=$error.__('EMPTY_TEMPERATURE_DATA');
                }
	
		$data_temp=get_format_month($data_temp);
		$data_humi=get_format_month($data_humi);
		$xlegend="XAXIS_LEGEND_MONTH";
		$styear=$startyear;
		$stmonth=$startmonth-1;
		$stday=1;
		$next=20;
	} 
}

$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$error);
$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$error);

if((isset($sd_card))&&(!empty($sd_card))) {
   $info=$info.__('INFO_SD_CARD').": $sd_card";
}


include('main/templates/logs.html');


?>
