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
$nb_plugs=get_configuration("NB_PLUGS",$main_error);
$plugs_infos=get_plugs_infos($nb_plugs,$main_error);
$plug_type="";
$select_plug="";
$select_power=array();
$select_sensor=array();
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
$unselected_graph=array();

if((!isset($import_load))||(empty($import_load))) {
    $import_load=2;
}

if(isset($_POST['select_power'])) {
    $select_power=getvar('select_power');
} elseif(isset($_POST['select_power_save'])&&(!empty($_POST['select_power_save']))) {
    $select_power=explode(",",getvar('select_power_save'));
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


if((isset($_POST['unselected_graph']))&&(!empty($_POST['unselected_graph']))) {
    $unselected_graph=explode(",",getvar('unselected_graph'));
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
check_and_update_sd_card($sd_card,$main_info,$main_error);

// Search and update log information form SD card
sd_card_update_log_informations($sd_card);

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
if((!isset($startday))||(empty($startday))) {
	$startday=date('Y')."-".date('m')."-".date('d');
} else {
	$type = "days";
}
$startday=str_replace(' ','',"$startday");
if(!check_format_date($startday,"days")) {
    $startday=date('Y')."-".date('m')."-".date('d');
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


if(isset($_SESSION['select_sensor'])) {
   $select_sensor[]=$_SESSION['select_sensor'];
} else {

   if(isset($_POST['select_sensor'])) {
       $select_sensor=getvar('select_sensor');
    } elseif(isset($_POST['select_sensor_save'])) {
       $select_sensor=explode(",",getvar('select_sensor_save'));
    }
}

if((!isset($select_sensor))||(empty($select_sensor))||(count($select_sensor)==0)) {
    $select_sensor[]="1";
}


//Cleaning SESSION variables useless right now:
unset($_SESSION['startyear']);
unset($_SESSION['startmonth']);
unset($_SESSION['select_sensor']);


if(isset($error_clean_log_file)) {
    $main_error[]=__('ERROR_WRITE_SD');
}


if($second_regul == "True") {
    if(count($select_power)==$nb_plugs) {
        format_regul_sumary("all",$main_error,$resume_regul,$nb_plugs);
    } else {
        if(count($select_power)!=0 && $select_plug != "") {
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

if($type != "days") {
   $legend_date=$startyear."-".$startmonth;
} else {
   $legend_date=$startday;
}

if($type == "days") {
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
        

            if($check_pwr != "") {
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
            $max_power_compute=max(explode(",",str_replace("null","0",$datap)));
            if($max_power_compute==0) {
                unset($max_power_compute);
            }
         } else {
            $main_error[]=__('EMPTY_POWER_DATA');
         }
      }
      $xlegend="XAXIS_LEGEND_DAY";
      $styear=substr($startday, 0, 4);
      $stmonth=substr($startday, 5, 2)-1;
      $stday=substr($startday, 8, 2);


      if(($check_log)||(!empty($datap))||(!empty($data))) {
            $db_sensors=get_sensor_db_type();
            foreach($select_sensor as $sens) { 
                $data_log[$sens]=array('sensor_nb' => $sens,
                                  'sensor_type' => "",
                                  'sensor_name_type' => "",
                                  'record1' => "",
                                  'record2' => "",
                                  'color_record1' => "",
                                  'color_record2' => "",
                                  'color_grid1' => "",
                                  'color_grid2' => "",
                                  'yaxis_record1' => -1,
                                  'yaxis_record2' => -1,
                                  'type_graph' => "area",
                                  'yaxis1_legend' => "",
                                  'yaxis2_legend' => "",
                                  'ratio' => "",
                                  'unity' => ""
                );
                
                for($i=0;$i<count($db_sensors);$i++) {
                    if($db_sensors[$i]['sensor_nb']==$sens) {
                        $data_log[$sens]['sensor_type']=$db_sensors[$i]['type'];
                        $data_log[$sens]['ratio']=$db_sensors[$i]['ratio'];
                        $data_log[$sens]['unity']=$db_sensors[$i]['unity'];

                        switch($db_sensors[$i]['type']) {
                            case '2': $name[]=__('TEMP_SENSOR');
                                      $name[]=__('HUMI_SENSOR'); 
                                      $color1=get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error);
                                      $color2=get_configuration("COLOR_HUMIDITY_GRAPH",$main_error);    
                                      $legend1=__('TEMP_LEGEND');
                                      $legend2=__('HUMI_LEGEND');
                                      break;
                            case '3': $name=__('WATER_SENSOR'); 
                                      $color1=get_configuration("COLOR_WATER_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('WATER_LEGEND');
                                      $legend2="";
                                      break;
                            case '6': 
                            case '7': $name=__('LEVEL_SENSOR'); 
                                      $color1=get_configuration("COLOR_LEVEL_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('LEVEL_LEGEND');
                                      $legend2="";
                                      break;
                            case '8': $name=__('PH_SENSOR'); 
                                      $color1=get_configuration("COLOR_PH_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('PH_LEGEND');
                                      $legend2="";
                                      break;
                            case '9': $name=__('EC_SENSOR'); 
                                      $color1=get_configuration("COLOR_EC_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('EC_LEGEND');
                                      $legend2="";
                                      break;
                            case ':': $name=__('OD_SENSOR'); 
                                      $color1=get_configuration("COLOR_OD_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('OD_LEGEND');
                                      $legend2="";
                                      break;
                            case ';': $name=__('ORP_SENSOR'); 
                                      $color1=get_configuration("COLOR_ORP_GRAPH",$main_error);
                                      $color2="";
                                      $legend1=__('ORP_LEGEND');
                                      $legend2="";
                                      break; 
                        }

                        $data_log[$sens]['sensor_name_type']=$name;
                        $data_log[$sens]['color_record1']=$color1;
                        $data_log[$sens]['color_record2']=$color2;
                        $data_log[$sens]['yaxis1_legend']=$legend1;
                        $data_log[$sens]['yaxis2_legend']=$legend2;
                        unset($name);
                        unset($color1);
                        unset($color2);
                        unset($legend1);
                        unset($legend2);

                        get_graph_array($record1,"record1/".$db_sensors[$i]['ratio'],$startday,$sens,"False","0",$main_error);
                        
                        if(!empty($record1)) {
                            $data_log[$sens]['record1']=get_format_graph($record1,"log");
                            if(strcmp($db_sensors[$i]['type'],"2")==0) {
                                get_graph_array($record2,"record2/".$db_sensors[$i]['ratio'],$startday,$sens,"False","0",$main_error);
                                if(!empty($record2)) {
                                    $data_log[$sens]['record2']=get_format_graph($record2,"log");
                                }
                            }
                        } 
                    }
                }

            }
    
            foreach($data_log as $datalog) {
                if(strcmp($datalog['record1'],"")==0) {
                         if(!isset($mess)) {
                              $mess=": ".$datalog['sensor_nb'];
                         } else {
                             $mess=$mess.", ".$datalog['sensor_nb'];
                         }            
                }
            }
            
            if(isset($mess)) {
                    $main_error[]=__('EMPTY_DATA_SENSOR').$mess;
             }

            if(count($data_log)!=0) {
                $filled=array();
                $yaxis=0;
                foreach($select_sensor as $sens) {
                        if(strcmp($data_log[$sens]['sensor_type'],"2")!=0) { 
                            if(in_array($data_log[$sens]['sensor_type'],$filled)) {
                                $data_log[$sens]['yaxis_record1']=array_search($data_log[$sens]['sensor_type'], $filled);
                            } else {
                                $data_log[$sens]['yaxis_record1']=$yaxis;
                                $yaxis=$yaxis+1;
                                $filled[]=$data_log[$sens]['sensor_type'];
                            }
                        } else {
                           if(in_array($data_log[$sens]['sensor_type']."-1",$filled)) {
                                $tmp=array_search($data_log[$sens]['sensor_type']."-1", $filled);
                                $data_log[$sens]['yaxis_record1']=$tmp;
                                $data_log[$sens]['yaxis_record2']=$tmp+2;
                            } else {
                                $data_log[$sens]['yaxis_record1']=$yaxis;
                                $data_log[$sens]['yaxis_record2']=$yaxis+1;
                                $yaxis=$yaxis+2;
                                $filled[]=$data_log[$sens]['sensor_type']."-1";
                                $filled[]=$data_log[$sens]['sensor_type']."-2";
                            }
                        }

                    switch($data_log[$sens]['color_record1']) {
                        case 'blue': $color_grid1=$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
                                     break;
                        case 'black': $color_grid1=$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
                                      break;
                        case 'green': $color_grid1=$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
                                      break;
                        case 'red': $color_grid1=$GLOBALS['GRAPHIC_COLOR_GRID_RED'];
                                      break;
                        case 'purple': $color_grid1=$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
                                      break;
                        default: $color_grid1="";

                    }

                    switch($data_log[$sens]['color_record2']) {
                        case 'blue': $color_grid2=$GLOBALS['GRAPHIC_COLOR_GRID_BLUE'];
                                     break;
                        case 'black': $color_grid2=$GLOBALS['GRAPHIC_COLOR_GRID_BLACK'];
                                      break;
                        case 'green': $color_grid2=$GLOBALS['GRAPHIC_COLOR_GRID_GREEN'];
                                      break;
                        case 'red': $color_grid2=$GLOBALS['GRAPHIC_COLOR_GRID_RED'];
                                      break;
                        case 'purple': $color_grid2=$GLOBALS['GRAPHIC_COLOR_GRID_PURPLE'];
                                      break;
                        default: $color_grid2="";

                    }
                    $data_log[$sens]['color_grid1']=$color_grid1;
                    $data_log[$sens]['color_grid2']=$color_grid2;
                }
            }

            format_minmax_sumary($startday,$main_error,$resume_minmax,$data_log);

            if(isset($datap)&&(!empty($datap))) {
                    $color_power=get_configuration("COLOR_POWER_GRAPH",$main_error);
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
                        case 'yellow': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_YELLOW'];
                                     break;
                        case 'orange': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_ORANGE'];
                                     break;
                        case 'brown': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_BROWN'];
                                     break;
                        case 'pink': $grid_power=$GLOBALS['GRAPHIC_COLOR_GRID_PINK'];
                                     break;
                    }

                    $data_log[]=array(
                                  'sensor_nb' => "1",
                                  'sensor_type' => "POWER",
                                  'sensor_name_type' => __('POWER'),
                                  'record1' => $datap,
                                  'record2' => "",
                                  'color_record1' => $color_power,
                                  'color_record2' => "",
                                  'color_grid1' => $grid_power,
                                  'color_grid2' => "",
                                  'yaxis_record1' => $yaxis,
                                  'yaxis_record2' => -1,
                                  'type_graph' => 'spline',
                                  'yaxis1_legend' => __('POWER_LEGEND'),
                                  'yaxis2_legend' => "",
                                  'ratio' => "",
                                  'unity' => "W"
                    );
                    $yaxis=$yaxis+1;
                }

                if((isset($data))&&(!empty($data))) { 
                    $data_log[]=array(
                                  'sensor_nb' => "1",
                                  'sensor_type' => "PROGRAM",
                                  'sensor_name_type' => clean_highchart_message($plugs_infos[$select_plug-1]["PLUG_NAME"]),
                                  'record1' => $data,
                                  'record2' => "",
                                  'color_record1' => $GLOBALS["LIST_GRAPHIC_COLOR_PROGRAM"][$select_plug-1],
                                  'color_record2' => "",
                                  'color_grid1' => $GLOBALS["LIST_GRAPHIC_COLOR_PROGRAM"][$select_plug-1],
                                  'color_grid2' => "",
                                  'yaxis_record1' => $yaxis,
                                  'yaxis_record2' => -1,
                                  'type_graph' => 'spline',
                                  'yaxis1_legend' => clean_highchart_message($plugs_infos[$select_plug-1]["PLUG_NAME"]),
                                  'yaxis2_legend' => "",
                                  'ratio' => "",
                                  'unity' => "PROGRAM"
                    );
                }
      } else {
        get_graph_array($record1,"record1/100","%%","1","True","0",$main_error);
        $main_error[]=__('EMPTY_DATA')." <img src='main/libs/img/infos.png' alt='' title='".__('TOOLTIP_FAKE_LOG_DATA')."' />";

        if(!empty($record1)) {
            unset($select_sensor);
            $select_sensor[]=1;
            $data_record1=get_format_graph($record1,"log");
            $fake_log=true;
            get_graph_array($record2,"record2/100","%%","1","True","0",$main_error);
            if(!empty($record2)) {
                $data_record2=get_format_graph($record2,"log");
            }

          $name[]=__('TEMP_SENSOR');
          $name[]=__('HUMI_SENSOR');
          $data_log[]=array(
                          'sensor_type' => "2",
                          'sensor_nb' => 1,
                          'sensor_name_type' => $name,
                          'record1' => $data_record1,
                          'record2' => $data_record2,
                          'color_record1' => get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error),
                          'color_record2' => get_configuration("COLOR_HUMIDITY_GRAPH",$main_error),
                          'color_grid1' => get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error),
                          'color_grid2' => get_configuration("COLOR_HUMIDITY_GRAPH",$main_error),
                          'yaxis_record1' => 0,
                          'yaxis_record2' => 1,
                          'type_graph' => 'area',
                          'yaxis1_legend' => __('TEMP_LEGEND'),
                          'yaxis2_legend' => __('HUMI_LEGEND'),
                          'ratio' => "",
                          'unity' => ""
                    );
        } else {
            $data_log[]=array(
                      'sensor_type' => "",
                      'sensor_nb' => "",
                      'sensor_name_type' => "",
                      'record1' => "",
                      'record2' => "",
                      'color_record1' => "",
                      'color_record2' => "",
                      'color_grid1' => "",
                      'color_grid2' => "",
                      'yaxis_record1' => 0,
                      'yaxis_record2' => "",
                      'type_graph' => "",
                      'yaxis1_legend' => "",
                      'yaxis2_legend' => "",
                      'ratio' => "",
                      'unity' => ""
                );
        }
     }
     $next=1;
} else {
    $nb = date('t',mktime(0, 0, 0, $startmonth, 1, $startyear)); 
    $data_record1="";
    $data_record2="";
    $db_sensors=get_sensor_db_type();
    $ratio=0;
    foreach($db_sensors as $sens) {
        if($sens['sensor_nb']==$select_sensor[0]) {
           switch($sens['type']) {
                case '2': $name[]=__('TEMP_SENSOR');
                          $name[]=__('HUMI_SENSOR');
                          $color1=get_configuration("COLOR_TEMPERATURE_GRAPH",$main_error);
                          $color2=get_configuration("COLOR_HUMIDITY_GRAPH",$main_error);
                          $legend1=__('TEMP_LEGEND');
                          $legend2=__('HUMI_LEGEND');
                          break;
                case '3': $name=__('WATER_SENSOR');
                          $color1=get_configuration("COLOR_WATER_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('WATER_LEGEND');
                          $legend2="";
                          break;
                case '6':
                case '7': $name=__('LEVEL_SENSOR');
                          $color1=get_configuration("COLOR_LEVEL_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('LEVEL_LEGEND');
                          $legend2="";
                          break;
                case '8': $name=__('PH_SENSOR');
                          $color1=get_configuration("COLOR_PH_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('PH_LEGEND');
                          $legend2="";
                          break;
                case '9': $name=__('EC_SENSOR');
                          $color1=get_configuration("COLOR_EC_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('EC_LEGEND');
                          $legend2="";
                          break;
                case ':': $name=__('OD_SENSOR');
                          $color1=get_configuration("COLOR_OD_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('OD_LEGEND');
                          $legend2="";
                          break;
                case ';': $name=__('ORP_SENSOR');
                          $color1=get_configuration("COLOR_ORP_GRAPH",$main_error);
                          $color2="";
                          $legend1=__('ORP_LEGEND');
                          $legend2="";
                          break;
           }

           $data_log[]=array(
              'sensor_type' => $sens['type'],
              'sensor_nb' => $sens['sensor_nb'],
              'sensor_name_type' => $name,
              'record1' => "",
              'record2' => "",
              'color_record1' => $color1,
              'color_record2' => $color2,
              'color_grid1' => $color1,
              'color_grid2' => $color2,
              'yaxis_record1' => 0,
              'yaxis_record2' => 1,
              'type_graph' => 'area',
              'yaxis1_legend' => $legend1,
              'yaxis2_legend' => $legend2,
              'ratio' => $sens['ratio'],
              'unity' => ""
           );
           break;
        }
    }

    for($i=1;$i<=$nb;$i++) {
        if($i<10) {
            $i="0$i";
         }
         $ddate="$startyear-$startmonth-$i";
         get_graph_array($record1,"record1/".$data_log[0]['ratio'],"$ddate",$select_sensor[0],"False","0",$main_error);

         if($data_record1 != "" ) {
            $data_record1="$data_record1, ".get_format_graph($record1,"log");
         } else {
            $data_record1=get_format_graph($record1,"log");
         }
         $record1=array();
    }
    $data_log[0]['record1']=get_format_month($data_record1);


    if(strcmp($data_log[0]['sensor_type'],"2")==0) {
        if(str_replace("null, ","","$data_record1")=="null") {
            $main_error[]=__('EMPTY_DATA');
            $data_record2=$data_record1;
        } else {
            $nb = date('t',mktime(0, 0, 0, $startmonth, 1, $startyear));
            for($i=1;$i<=$nb;$i++) {
                if($i<10) {
                    $i="0$i";
                }
                $ddate="$startyear-$startmonth-$i";
                get_graph_array($record2,"record2/".$data_log[0]['ratio'],"$ddate",$select_sensor[0],"False","0",$main_error);

                if("$data_record2" != "" ) {
                    $data_record2="$data_record2, ".get_format_graph($record2,"log");
                } else {
                    $data_record2=get_format_graph($record2,"log");
                }
                $record2=Array();
            }
        }
        $data_log[0]['record2']=get_format_month($data_record2);
    }

    $xlegend="XAXIS_LEGEND_MONTH";
    $styear=$startyear;
    $stmonth=$startmonth-1;
    $stday=1;
    $next=20;
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
