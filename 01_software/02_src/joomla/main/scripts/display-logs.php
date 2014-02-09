<?php

if (!isset($_SESSION)) {
	session_start();
}


/* Libraries requiered: 
        db_common.php : manage database requests
        utilfunc.php  : manage variables and files manipulations
*/

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');
require_once('main/libs/debug.php');

// Compute page time loading for debug option
$start_load = getmicrotime();


// Language for the interface, using a SESSION variable and the function __('$msg') from utilfunc.php library to print messages
$error=array();
$main_error=array();
$main_info=array();
$_SESSION['LANG'] = get_current_lang();
$_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
__('LANG');

$export_log=getvar('export_log');
$export_log_power=getvar('export_log_power');



// ================= VARIABLES ================= //
$type="";
$temperature= array();
$humidity = array();
$check_tmp=array();
$check_humi=array();
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$data_temp="";
$data_humi="";
$plug_type="";
$select_plug="";
$select_power=array();
$select_sensor="1";
$startday="";
$startmonth="";
$startyear="";
$fake_log=false;
$program="";
$pop_up=get_configuration("SHOW_POPUP",$main_error);
$pop_up_message="";
$pop_up_error_message="";
$last_year=date('Y');
$datap="";
$resume_regul="";
$update=get_configuration("CHECK_UPDATE",$main_error);
$version=get_configuration("VERSION",$main_error);
$previous=getvar('previous');
$next=getvar('next');
$active_plugs=get_active_plugs($nb_plugs,$main_error);
$second_regul=get_configuration("SECOND_REGUL",$main_error);
$anchor=getvar('anchor');
$select_plug=getvar('select_plug');
$startday=getvar('startday');
$import_load=getvar('import_load');
$resume_minmax="";
if((!isset($import_load))||(empty($import_load))) {
    $import_load=2;
}

$_SESSION['sensor_type']=get_sensor_db_type();

if(isset($_POST['select_power'])) {
    $select_power=getvar('select_power');
} elseif(isset($_POST['select_power_save'])) {
    $select_power=explode(",",getvar('select_power_save'));
}

if(isset($_SESSION['select_sensor'])) {
   $select_sensor=$_SESSION['select_sensor'];
} else {
    $select_sensor=getvar('select_sensor');
}


if(isset($_SESSION['startmonth'])) {
    $startmonth=$_SESSION['startmonth'];
} else {
    $startmonth=getvar('startmonth');
}

if(isset($_SESSION['startyear'])) {
    $startyear=$_SESSION['startyear'];
} else {
    $startyear=getvar('startyear');
}


if((isset($_POST['zoom_min_power']))&&(strcmp($_POST['zoom_min_power'],"-1")!=0)) {
    $zoom_min_power=$_POST['zoom_min_power'];
} 

if((isset($_POST['zoom_max_power']))&&(strcmp($_POST['zoom_max_power'],"-1")!=0)) {
    $zoom_max_power=$_POST['zoom_max_power'];
} 


if((isset($_POST['zoom_min_temp']))&&(strcmp($_POST['zoom_min_temp'],"-1")!=0)) {
    $zoom_min_temp=$_POST['zoom_min_temp'];
} 

if((isset($_POST['zoom_max_temp']))&&(strcmp($_POST['zoom_max_temp'],"-1")!=0)) {
    $zoom_max_temp=$_POST['zoom_max_temp'];
} 

if((isset($_POST['zoom_min_humi']))&&(strcmp($_POST['zoom_min_humi'],"-1")!=0)) {
    $zoom_min_humi=$_POST['zoom_min_humi'];
} 

if((isset($_POST['zoom_max_humi']))&&(strcmp($_POST['zoom_max_humi'],"-1")!=0)) {
    $zoom_max_humi=$_POST['zoom_max_humi'];
} 


if((isset($export_log))&&(!empty($export_log))) {
    export_table_csv("logs",$main_error);
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
     export_table_csv("power",$main_error);
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


// If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
if((!empty($sd_card))&&(isset($sd_card))) { 
    
    
    $program="";
    $conf_uptodate=true;
    $error_copy=false;
    if(check_sd_card($sd_card)) {


        /* TO BE DELETED */
        if(!compat_old_sd_card($sd_card)) { 
            $main_error[]=__('ERROR_COPY_FILE'); 
            $error_copy=true;
        }   
        /* ************* */


        $program=create_program_from_database($main_error);
        if(!compare_program($program,$sd_card)) {
            $conf_uptodate=false;
            if(!save_program_on_sd($sd_card,$program)) { 
                $main_error[]=__('ERROR_WRITE_PROGRAM'); 
                $error_copy=true;
            }
        }


        $ret_firm=check_and_copy_firm($sd_card);
        if(!$ret_firm) {
            $main_error[]=__('ERROR_COPY_FIRM');
            $error_copy=true;
        } else if($ret_firm==1) {
            $conf_uptodate=false;
        }


        if(!compare_pluga($sd_card)) {
            $conf_uptodate=false;
            if(!write_pluga($sd_card,$main_error)) {
                $main_error[]=__('ERROR_COPY_PLUGA');
                $error_copy=true;
            }
        }


        $plugconf=create_plugconf_from_database($GLOBALS['NB_MAX_PLUG'],$main_error);
        if(count($plugconf)>0) {
            if(!compare_plugconf($plugconf,$sd_card)) {
                $conf_uptodate=false;
                if(!write_plugconf($plugconf,$sd_card)) {
                    $main_error[]=__('ERROR_COPY_PLUG_CONF');
                    $error_copy=true;
                }
            }
        }


        if(!is_file("$sd_card/log.txt")) {
            if(!copy_empty_big_file("$sd_card/log.txt")) {
                $main_error[]=__('ERROR_COPY_TPL');
                $error_copy=true;
            }
        }

        
        if(!check_and_copy_index($sd_card)) {
            $main_error[]=__('ERROR_COPY_INDEX');
            $error_copy=true;
        }

        $wifi_conf=create_wificonf_from_database($main_error);
        if(!compare_wificonf($wifi_conf,$sd_card)) {
            $conf_uptodate=false;
            if(!write_wificonf($sd_card,$wifi_conf,$main_error)) {
                $main_error[]=__('ERROR_COPY_WIFI_CONF');
                $error_copy=true;
            }
        }

        $current_index=get_sensor_type($sd_card,date('m'),date('d'));
        $chk_value=false;

        foreach($current_index as $tst_index) {
            if($tst_index!=0) {
                $chk_value=true;
                break;
            }
        }

        if($chk_value) {            
            update_sensor_type($current_index); 
            clean_index_file($sd_card);
        } 


        $recordfrequency = get_configuration("RECORD_FREQUENCY",$main_error);
        $powerfrequency = get_configuration("POWER_FREQUENCY",$main_error);
        $updatefrequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$main_error);
        $alarmenable = get_configuration("ALARM_ACTIV",$main_error);
        $alarmvalue = get_configuration("ALARM_VALUE",$main_error);
        $resetvalue= get_configuration("RESET_MINMAX",$main_error);
        if("$updatefrequency"=="-1") {
            $updatefrequency="0";
        }


        if(!compare_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,$alarmenable,$alarmvalue,"$resetvalue")) {
            $conf_uptodate=false;
            if(!write_sd_conf_file($sd_card,$recordfrequency,$updatefrequency,$powerfrequency,"$alarmenable","$alarmvalue","$resetvalue",$main_error)) {
                $main_error[]=__('ERROR_WRITE_SD_CONF');
                $error_copy=true;
            }
        }

        if((!$conf_uptodate)&&(!$error_copy)) {
            $main_info[]=__('UPDATED_PROGRAM');
            $pop_up_message=$pop_up_message.popup_message(__('UPDATED_PROGRAM'));
            set_historic_value(__('UPDATED_PROGRAM')." (".__('LOGS_PAGE').")","histo_info",$main_error);
        }

        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    } else {
        $main_error[]=__('ERROR_WRITE_SD');
        $main_info[]=__('INFO_SD_CARD').": $sd_card";
    }
} else {
        $main_error[]=__('ERROR_SD_CARD');
}





//Cleaning SESSION variables useless right now:
unset($_SESSION['startyear']);
unset($_SESSION['startmonth']);
unset($_SESSION['select_sensor']);



//More default values:
if((!isset($startday))||(empty($startday))) {
	$startday=date('Y')."-".date('m')."-".date('d');
} else {
	$type = "days";
}
$startday=str_replace(' ','',"$startday");
if(!check_format_date($startday,"days")) {
    $startday=date('Y')."-".date('m')."-".date('d');
}


if((!isset($select_sensor))||(empty($select_sensor))) {
    $select_sensor=1;
}


if((!isset($startmonth))||(empty($startmonth))) {
   $startmonth=date('m');
	$startyear=date('Y');
} else {
   $type = "month";
}

if((!isset($type))||(empty($type))){
	$type="days";
}

if(isset($error_clean_log_file)) {
    $main_error[]=__('ERROR_WRITE_SD');
}


if(strcmp("$second_regul","True")==0) {
    if(count($select_power)==$nb_plugs) {
        format_regul_sumary("all",$main_error,$resume_regul,$nb_plugs);
    } else {
        if((count($select_power)!=0)&&(strcmp("$select_plug","")!=0)) {
            if($select_power<$select_plug) {
                $first=$select_power;
                $second=$select_plug;
            } else {
                $first=$select_plug;
                $second=$select_power;
            }

            format_regul_sumary($first,$main_error,$resume_regul,$nb_plugs);
            if(strcmp("$first","$second")!=0) {
                format_regul_sumary($second,$main_error,$resume_regul,$nb_plugs);
            }
        } else {
            if(count($select_power)>0) {
                for($i=0;$i<count($select_power);$i++)  {
                    format_regul_sumary($select_power[$i],$main_error,$resume_regul,$nb_plugs);
                }
            }

            if(count($select_plug)!=0) {
                format_regul_sumary($select_plug,$main_error,$resume_regul,$nb_plugs);
            }
        }
    }
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


$check_log=check_export_table_csv("logs",$main_error);
$check_power=check_export_table_csv("power",$main_error);

if(strcmp("$type","days")!=0) {
   $legend_date=$startyear."-".$startmonth;
} else {
   $legend_date=$startday;
}

if("$type" == "days") {
      if((isset($select_plug))&&(!empty($select_plug))) {
         $data_plug=get_data_plug($select_plug,$main_error);
         $data_plug=format_axis_data($data_plug,100,$main_error);
         $data=format_program_highchart_data($data_plug,$startday);
         $plug_type=get_plug_conf("PLUG_TYPE",$select_plug,$main_error);
      }

      if(count($select_power)>0) {
         if(count($select_power)!=$nb_plugs) {
            $data_power=get_data_power($startday,"",$select_power,$main_error);

            $check_pwr="";
            $several=false;
            foreach($select_power as $slt_pwr) {
                if(!check_configuration_power($slt_pwr,$main_error)) {
                    if(strcmp("$check_pwr","")==0) {
                        $check_pwr=$slt_pwr;
                    } else {
                        $check_pwr=$check_pwr.", ".$slt_pwr;
                        $several=true;
                    }
                }
            }
        

            if(strcmp("$check_pwr","")!=0) {
                if($several) {
                    $main_error[]=__('ERROR_POWER_PLUGS')." ".$check_pwr." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=all'>".__('HERE')."</a>";
                } else {
                    $main_error[]=__('ERROR_POWER_PLUG')." ".$check_pwr." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=".$check_pwr."'>".__('HERE')."</a>";
                }
            }
         } else {
            $data_power=get_data_power($startday,"","all",$main_error);

            $nb=array();

            for($plugs=1;$plugs<=$nb_plugs;$plugs++) {
                if(!check_configuration_power($plugs,$main_error)) {
                    $nb[]=$plugs;
                }
            }

            if(count($nb)>0) {
                if(count($nb)==1) {
                    $main_error[]=__('ERROR_POWER_PLUG')." ".$nb[0]." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=".$nb[0]."'>".__('HERE')."</a>";
                } else {
                    $tmp_number="";
                    foreach($nb as $number) {
                        if(strcmp($tmp_number,"")!=0) {
                            $tmp_number=$tmp_number.", ";
                        }
                        $tmp_number=$tmp_number.$number;
                    }
                    $main_error[]=__('ERROR_POWER_PLUGS')." ".$tmp_number." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=all'>".__('HERE')."</a>";
                }
            }
         }

         if(!empty($data_power)) {
            $power_format=format_data_power($data_power);
            $datap=get_format_graph($power_format,"power");
         } else {
            $main_error[]=__('EMPTY_POWER_DATA');
         }
      }
      $xlegend="XAXIS_LEGEND_DAY";
      $styear=substr($startday, 0, 4);
      $stmonth=substr($startday, 5, 2)-1;
      $stday=substr($startday, 8, 2);

      if(($check_log)||(!empty($datap))||(!empty($data))) {

        if(strcmp("$select_sensor","all")!=0) {
            get_graph_array($temperature,"temperature/100",$startday,$select_sensor,"False","0",$main_error);

            if(!empty($temperature)) {
                $data_temp=get_format_graph($temperature,"log");
                get_graph_array($humidity,"humidity/100",$startday,$select_sensor,"False","0",$main_error);
                if(!empty($humidity)) {
                    $data_humi=get_format_graph($humidity,"log");
                } 
                format_minmax_sumary($startday,$main_error,$resume_minmax,$select_sensor);
                if(!empty($resume_minmax)) {
                    $resume_minmax="<p align='center'><b><i>".__('SUMARY_RESUME_MINMAX')." ".$startday.":</i></b></p>".$resume_minmax."<br />";
                }
            } else {
                $main_error[]=__('EMPTY_DATA');
            }
        } else {
            for($i=1;$i<=$GLOBALS['NB_MAX_SENSOR_LOG'];$i++) {
                format_minmax_sumary($startday,$main_error,$resume_minmax,$i);
                get_graph_array($temperature,"temperature/100",$startday,$i,"False","0",$main_error);

                if(!empty($temperature)) {
                    $data_temp[]=get_format_graph($temperature,"log");
                    get_graph_array($humidity,"humidity/100",$startday,$i,"False","0",$main_error);
                    if(!empty($humidity)) {
                        $data_humi[]=get_format_graph($humidity,"log");
                    }
                } else {
                    if(!isset($mess)) {
                        $mess=": $i";
                    } else {
                        $mess=$mess.", $i";
                    }
                    $data_temp[]="";
                    $data_humi[]="";
                }
            }
            if(isset($mess)) { 
                $main_error[]=__('EMPTY_DATA_SENSOR').$mess;
            }
            if(!empty($resume_minmax)) {
                $resume_minmax="<p align='center'><b><i>".__('SUMARY_RESUME_MINMAX')." ".$startday." (".__('ALL_SENSOR')."):</i></b></p>".$resume_minmax."<br />";
            }
        }
      } else {
        get_graph_array($temperature,"temperature/100","%%","1","True","0",$main_error);
        $main_error[]=__('EMPTY_DATA')." <img src=\"main/libs/img/infos.png\" alt=\"\" title=\"".__('TOOLTIP_FAKE_LOG_DATA').".\" />";

        if(!empty($temperature)) {
          $data_temp=get_format_graph($temperature,"log");
          $fake_log=true;
          get_graph_array($humidity,"humidity/100","%%","1","True","0",$main_error);
          if(!empty($humidity)) {
            $data_humi=get_format_graph($humidity,"log");
          }
        } 
     }
     $next=1;
} else {
    $nb = date('t',mktime(0, 0, 0, $startmonth, 1, $startyear)); 
    for($i=1;$i<=$nb;$i++) {
        if($i<10) {
            $i="0$i";
         }
         $ddate="$startyear-$startmonth-$i";
         get_graph_array($temperature,"temperature/100","$ddate",$select_sensor,"False","0",$main_error);

         if("$data_temp" != "" ) {
            $data_temp="$data_temp, ".get_format_graph($temperature,"log");
         } else {
            $data_temp=get_format_graph($temperature,"log");
         }
         $temperature=array();
    }

    if(str_replace("null, ","","$data_temp")=="null") {
         $main_error[]=__('EMPTY_DATA');
         $data_humi=$data_temp;
    } else {
        $nb = date('t',mktime(0, 0, 0, $startmonth, 1, $startyear));
        for($i=1;$i<=$nb;$i++) {
         if($i<10) {
            $i="0$i";
         }
         $ddate="$startyear-$startmonth-$i";
         get_graph_array($humidity,"humidity/100","$ddate",$select_sensor,"False","0",$main_error);

         if("$data_humi" != "" ) {
            $data_humi="$data_humi, ".get_format_graph($humidity,"log");
         } else {
            $data_humi=get_format_graph($humidity,"log");
         }
         $humidity=Array();
        }
    }

    $data_temp=get_format_month($data_temp);
    $data_humi=get_format_month($data_humi);
    $xlegend="XAXIS_LEGEND_MONTH";
    $styear=$startyear;
    $stmonth=$startmonth-1;
    $stday=1;
    $next=20;
}


$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error);
$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$main_error);
$color_power=get_configuration("COLOR_POWER_GRAPH",$main_error);

switch($color_humidity) {
    case 'blue': $grid_humidity=$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
        break;
    case 'black': $grid_humidity=$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
        break;
    case 'green': $grid_humidity=$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
        break;
    case 'red': $grid_humidity=$GLOBALS['GRAPHIC_COLOR_GRID_RED'];
        break;
    case 'purple': $grid_humidity=$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
        break;
}    


switch($color_temperature) {
     case 'blue': $grid_temperature=$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
         break;
     case 'black': $grid_temperature=$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
         break;
     case 'green': $grid_temperature=$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
         break;
     case 'red': $grid_temperature=$GLOBALS['GRAPHIC_COLOR_GRID_RED'];
         break;
     case 'purple': $grid_temperature=$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
         break;
} 


switch($color_power) {
     case 'blue': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
         break;
     case 'black': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
         break;
     case 'green': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
         break;
     case 'red': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_RED'];
         break;
     case 'purple': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
         break;
} 








// Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
if(strcmp("$update","True")==0) {
    if((!isset($_SESSION['UPDATE_CHECKED']))||(empty($_SESSION['UPDATE_CHECKED']))) {
        if($sock=@fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
            if(check_update_available($version,$main_error)) {
                $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
                $_SESSION['UPDATE_CHECKED']="True";
            } else {
                $_SESSION['UPDATE_CHECKED']="False";
            }
        } else {
            $main_error[]=__('ERROR_REMOTE_SITE');
            $_SESSION['UPDATE_CHECKED']="";
        }
    } else if(strcmp($_SESSION['UPDATE_CHECKED'],"True")==0) {
        $main_info[]=__('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
    }
} 


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True' informations will be send for debug
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["log"]="";

if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    copy_empty_big_file("$sd_card/log.txt");
}

if(strcmp($informations["cbx_id"],"")!=0) insert_informations("cbx_id",$informations["cbx_id"]);
if(strcmp($informations["firm_version"],"")!=0) insert_informations("firm_version",$informations["firm_version"]);
if(strcmp($informations["log"],"")!=0) insert_informations("log",$informations["log"]);


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
