<?php


// Compute page time loading for debug option
$start_load = getmicrotime();


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
$stats=get_configuration("STATISTICS",$main_error);
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

if(isset($_POST['select_power'])) {
    $select_power=getvar('select_power');
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


        if(!check_and_copy_log($sd_card)) {
            $main_error[]=__('ERROR_COPY_TPL');
            $error_copy=true;
        }

        
        if(!check_and_copy_index($sd_card)) {
            $main_error[]=__('ERROR_COPY_INDEX');
            $error_copy=true;
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
            foreach($select_power as $slt_pwr) {
                if(!check_configuration_power($slt_pwr,$main_error)) $check_pwr=$slt_pwr;
                break;
            }

            if(strcmp("$slt_pwr","")!=0) {
                $main_error[]=__('ERROR_POWER_PLUG')." ".$check_pwr." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='plugs-".$_SESSION['SHORTLANG']."?selected_plug=".$check_pwr."'>".__('HERE')."</a>";
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


// The informations part to send statistics to debug the cultibox: if the 'STATISTICS' variable into the configuration table from the database is set to 'True'
$informations = Array();
$informations["cbx_id"]="";
$informations["firm_version"]="";
$informations["id_computer"]=php_uname("a");
$informations["log"]="";



if((!empty($sd_card))&&(isset($sd_card))) {
    find_informations("$sd_card/log.txt",$informations);
    if(strcmp($informations["log"],"")!=0) {
        clean_log_file("$sd_card/log.txt");
    }
}


if((isset($stats))&&(!empty($stats))&&(strcmp("$stats","True")==0)) {
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

    if(strcmp($informations["log"],"")==0) {
        $informations["log"]=get_informations("log");
    } else {
        insert_informations("log",$informations["log"]);
    }
}


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
