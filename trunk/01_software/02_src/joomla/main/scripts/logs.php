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

$error="";
$main_error="";
$info="";
$type="";
$temperature= array();
$humidity = array();
$check_tmp=array();
$check_humi=array();
$nb_plugs=get_configuration("NB_PLUGS",$error);
$plugs_infos=get_plugs_infos($nb_plugs,$error);
$data_temp="";
$data_humi="";
$plug_type="";
$select_plug="";
$select_power="";
$select_sensor="1";
$startday="";
$startmonth="";
$startyear="";
$hygro_axis=get_configuration("LOG_HYGRO_AXIS",$error);
$temp_axis=get_configuration("LOG_TEMP_AXIS",$error);
$power_axis=get_configuration("LOG_POWER_AXIS",$error);
$fake_log=false;
$program="";
$pop_up="";
$pop_up_error_message="";
$last_year=date('Y');
$datap="";
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);
$log_search=get_configuration("LOG_SEARCH",$error);
$previous=getvar('previous');
$next=getvar('next');


if((!isset($log_search))||(empty($log_search))) {
    $log_search=2;
}

if(isset($_SESSION['select_plug'])) {
   $select_plug=$_SESSION['select_plug'];
}


if(isset($_SESSION['select_power'])) {
   $select_power=$_SESSION['select_power'];
}

if(isset($_SESSION['select_sensor'])) {
   $select_sensor=$_SESSION['select_sensor'];
}

if(isset($_SESSION['reset_log'])) {
   $reset_log=$_SESSION['reset_log'];
}

if(isset($_SESSION['quick_load'])) {
   $quick_load=$_SESSION['quick_load'];
}

if(isset($_SESSION['reset_log_power'])) {
   $reset_log_power=$_SESSION['reset_log_power'];
}

if(isset($_SESSION['import_log'])) {
   $import_log=$_SESSION['import_log'];
}


if(!empty($reset_log)) { 
    if(reset_log("logs","1",$error)) {
        $info=$info.__('VALID_DELETE_LOGS');
        $pop_up_message=clean_popup_message(__('VALID_DELETE_LOGS'));
    }
}

if(!empty($reset_log_power)) {
    if(reset_log("power","0",$error)) {
        $info=$info.__('VALID_DELETE_LOGS');
        $pop_up_message=clean_popup_message(__('VALID_DELETE_LOGS'));
    }
}



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
      save_program_on_sd($sd_card,$program,$error);
   }
   check_and_copy_firm($sd_card,$error);
   check_and_copy_log($sd_card,$error);
} else {
   $tmp="";
   $tmp=__('ERROR_SD_CARD_LOGS');
   $tmp_title=__('TOOLTIP_WITHOUT_SD_LOG');
   $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
   $main_error=$main_error.$tmp;
}

if(isset($_SESSION['startday'])) {
   $startday=$_SESSION['startday'];
}

if(isset($_SESSION['startmonth'])) {
   $startmonth=$_SESSION['startmonth'];
}

if(isset($_SESSION['startyear'])) {
   $startyear=$_SESSION['startyear'];
}

unset($_SESSION['startyear']);
unset($_SESSION['startmonth']);
unset($_SESSION['startday']);
unset($_SESSION['select_power']);
unset($_SESSION['select_plug']);
unset($_SESSION['select_sensor']);
unset($_SESSION['reset_log']);
unset($_SESSION['reset_log_power']);
unset($_SESSION['quick_load']);
unset($_SESSION['import_log']);
unset($reset_log);
unset($reset_log_power);


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

// ************  Log and power file reading from SD card  ********************//
$log = array();
$power=array();
$load_log=false;
if((isset($sd_card))&&(!empty($sd_card))&&(!isset($quick_load))) {
   // Workaround to avoid timeout (60s)
   // Search only on two previous months

   if((isset($import_log))&&(!empty($import_log))) {
    $FirstMonthSearch = date('n') - $log_search +1;

    if($FirstMonthSearch == 0) {
        $FirstMonthSearch = 1;
    }

    if($FirstMonthSearch < 0) {
      $FirstMonthSearch = 12 + $FirstMonthSearch + 1;
    }

    $ListMonthSearch=array();
    $i=1;

    while($i<=$log_search) {
        if($FirstMonthSearch>12) {
            $FirstMonthSearch=1;
        }
        $ListMonthSearch[]=$FirstMonthSearch;  
        $FirstMonthSearch=$FirstMonthSearch+1;
        $i=$i+1;
    }

   } else {
        $ListMonthSearch[]=date('m'); 
   }

   // Foreach months present in the array search logs and power
   foreach ($ListMonthSearch as $month) {
        for ($day = 1; $day <= 31; $day++) {
      
         // Don't search for nexts days
         if ($month != date('n') || $day <= date('j') ) {
 
            // Convert date to be equivalent of directory
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
               if(is_file("$sd_card/logs/$mmonth/$dday")) {
                  get_log_value("$sd_card/logs/$mmonth/$dday",$log);
               }
                if(!empty($log)) {
                  if(db_update_logs($log,$error)) {
                    if(strcmp(date('md'),"${mmonth}${dday}")!=0) {
                        clean_log_file("$sd_card/logs/$mmonth/$dday");
                    }
                  }
                  unset($log) ;
                  $log = array();
                   $load_log=true;
               }

               // get power values
               if(is_file("$sd_card/logs/$mmonth/pwr_$dday")) {
                  get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power);
               }

               if(!empty($power)) {
                  if(db_update_power($power,$error)) {
                     clean_big_file("$sd_card/logs/$mmonth/pwr_$dday");
                  } 
                  unset($power) ;
                  $power = array();
                  $load_log=true;
               }
            }
         }
        }
   }
}

if($load_log) {
   if((isset($import_log))&&(!empty($import_log))) {
        $info=$info.__('VALID_LOAD_LOG');
        $pop_up_message=clean_popup_message(__('VALID_LOAD_LOG'));
   } else {
       $info=$info.__('VALID_CURRENT_LOAD_LOG');
       $pop_up_message=clean_popup_message(__('VALID_CURRENT_LOAD_LOG'));
   }
} 

if("$type"=="days") {
   $legend_date=$startday;
   $check_format=check_format_date($startday,$type);
   if(!$check_format) {
      $error=$error.__('ERROR_FORMAT_DATE_DAY');
      $pop_up_error_message=clean_popup_message($error);
      $startday=date('Y')."-".date('m')."-".date('d');
      $check_format=true;
   }
} else {
   $legend_date=date('Y')."-".$startmonth;
   $check_format=check_format_date($startmonth,$type);
   if(!$check_format) {
      $error=$error.__('ERROR_FORMAT_DATE_MONTH');
      $pop_up_error_message=clean_popup_message($error);
   }
}

if("$type" == "days") {
   if($check_format) {
      if((isset($select_plug))&&(!empty($select_plug))) {
         $data_plug=get_data_plug($select_plug,$error);
         $data_plug=format_axis_data($data_plug,$hygro_axis,$error);
          $data=format_program_highchart_data($data_plug,$startday);
         $plug_type=get_plug_conf("PLUG_TYPE",$select_plug,$error);
      }

      if((isset($select_power))&&(!empty($select_power))) {
         if($select_power!=9990) {
            $data_power=get_data_power($startday,"",$select_power,$error);
         } else {
            $data_power=get_data_power($startday,"","all",$error);
         }

         if(!empty($data_power)) {
            $datap=get_format_graph_power($data_power);
         } else {
            $error=$error.__('EMPTY_POWER_DATA');
         }
      }
      $xlegend="XAXIS_LEGEND_DAY";
      $styear=substr($startday, 0, 4);
      $stmonth=substr($startday, 5, 2)-1;
      $stday=substr($startday, 8, 2);
     

      get_graph_array($check_tmp,"temperature/100","%%","all","False",$error);
      get_graph_array($check_hum,"humidity/100","%%","all","False",$error);

      if((count($check_tmp)>0)||((count($check_hum)>0))||(!empty($datap))||(!empty($data))) {
        if(strcmp("$select_sensor","all")!=0) {
            get_graph_array($temperature,"temperature/100",$startday,$select_sensor,"False",$error);
            get_graph_array($humidity,"humidity/100",$startday,$select_sensor,"False",$error);

            if(!empty($temperature)) {
                $data_temp=get_format_graph($temperature);
            } else {
                $error=$error.__('EMPTY_TEMPERATURE_DATA');
            }

            if(!empty($humidity)) {
                $data_humi=get_format_graph($humidity);
            } else {
                $error=$error.__('EMPTY_HUMIDITY_DATA');
            }
        } else {
            $humi_err=false;
            $temp_err=false;
            for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR'];$i++) {
                get_graph_array($temperature,"temperature/100",$startday,$i,"False",$error);
                get_graph_array($humidity,"humidity/100",$startday,$i,"False",$error);

                if(!empty($temperature)) {
                    $data_temp[]=get_format_graph($temperature);
                } else {
                    $data_temp[]="";
                    if(!$temp_err) { 
                        $error=$error.__('EMPTY_TEMPERATURE_DATA_SENSOR');
                        $temp_err=true;
                    }
                }

                if(!empty($humidity)) {
                    $data_humi[]=get_format_graph($humidity);
                } else {
                    $data_humi[]="";
                    if(!$humi_err) {
                            $error=$error.__('EMPTY_HUMIDITY_DATA_SENSOR');
                            $humi_err=true;
                    }
                }
            }
        }
      } else {
        get_graph_array($temperature,"temperature/100","%%","1","True",$error);
        $tmp="";
        $tmp=__('EMPTY_TEMPERATURE_DATA');
        $tmp_title=__('TOOLTIP_FAKE_LOG_DATA');
        $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
        $error=$error.$tmp;

        if(!empty($temperature)) {
          $data_temp=get_format_graph($temperature);
          $fake_log=true;
        } 

        get_graph_array($humidity,"humidity/100","%%","1","True",$error);
        $tmp="";
        $tmp=__('EMPTY_HUMIDITY_DATA');
        $tmp_title=__('TOOLTIP_FAKE_LOG_DATA');
        $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
        $error=$error.$tmp;

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
         get_graph_array($temperature,"temperature/100","$ddate",$select_sensor,"False",$error);
         get_graph_array($humidity,"humidity/100","$ddate",$select_sensor,"False",$error);
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
$color_power=get_configuration("COLOR_POWER_GRAPH",$error);
$color_program=get_configuration("COLOR_PROGRAM_GRAPH",$error);

$sd_card="";
$sd_card=get_sd_card();
if((!empty($sd_card))&&(isset($sd_card))) {
   $info=$info.__('INFO_SD_CARD').": $sd_card";
}

if(strcmp("$update","True")==0) {
      $ret=array();
      check_update_available($ret,$error);
      foreach($ret as $file) {
         if(count($file)==4) {
               if(strcmp("$version","$file[1]")==0) {
                  $tmp="";
                  $tmp=__('INFO_UPDATE_AVAILABLE');
                  $tmp=str_replace("</li>","<a href=".$file[3]." target='_blank'>".$file[2]."</a></li>",$tmp);
                  $info=$info.$tmp;
               }
            }
      }
}


$informations = Array();
$informations["nb_reboot"]=0;
$informations["last_reboot"]="";
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["emeteur_version"]="";
$informations["sensor_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";



if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    if(strcmp($informations["log"],"")!=0) {
        clean_big_file("$sd_card/log.txt");
    }
}

if(strcmp($informations["nb_reboot"],"0")==0) {
        $informations["nb_reboot"]=get_informations("nb_reboot");
} else {
        insert_informations("nb_reboot",$informations["nb_reboot"]);
}

if(strcmp($informations["last_reboot"],"")==0) {
        $informations["last_reboot"]=get_informations("last_reboot");
} else {
        insert_informations("last_reboot",$informations["last_reboot"]);
}

if(strcmp($informations["cbx_id"],"")==0) {
        $informations["cbx_id"]=get_informations("cbx_id");
} else {
        insert_informations("cbx_id",$informations["cbx_id"]);
}

if(strcmp($informations["firm_version"],"")==0) {
        $informations["firm_version"]=get_informations("firm_version");
} else {
        insert_informations("firm_version",$informations["firm_version"]);
}

if(strcmp($informations["emeteur_version"],"")==0) {
        $informations["emeteur_version"]=get_informations("emeteur_version");
} else {
        insert_informations("emeteur_version",$informations["emeteur_version"]);
}

if(strcmp($informations["sensor_version"],"")==0) {
        $informations["sensor_version"]=get_informations("sensor_version");
} else {
        insert_informations("sensor_version",$informations["sensor_version"]);
}

if(strcmp($informations["log"],"")==0) {
        $informations["log"]=get_informations("log");
} else {
        insert_informations("log",$informations["log"]);
}




include('main/templates/logs.html');


?>
