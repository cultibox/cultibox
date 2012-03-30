<?php

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');

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
	echo $GLOBALS['DATE_DIR_PATH']."/logs/$mmonth/$dday";
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

if((!empty($humidity))||(!empty($temperature))) {
  $max_humi=get_max_value("humidity",$startdate,$return);
  $max_temp=get_max_value("temperature",$startdate,$return);

  $largeur = 640;
  $hauteur = 600;

  $graph = new Graph($largeur, $hauteur);
  $graph->SetMargin(50,50,40,60);
  $graph->SetMarginColor('white');
  $graph->img->SetAntiAliasing(false);
  $graph->SetBox(false);
  $graph->SetScale("textlin",0,$max_temp+20);
  $graph->img->SetAntiAliasing();
  $histo_temp = new LinePlot($temperature);
  $histo_temp->SetLegend(__('LEGEND_TEMP'));
  $histo_temp->SetColor($_SESSION['COLOR_TEMPERATURE_GRAPH']);
  $histo_humi = new LinePlot($humidity);
  $histo_humi->SetLegend(__('LEGEND_HUMI'));
  $histo_humi->SetColor($_SESSION['COLOR_HUMIDITY_GRAPH']);
  $graph->add($histo_temp);
  $graph->addY2($histo_humi);
  $graph->legend->Pos(0.5,0.95,"center");
  $graph->legend->SetLayout(LEGEND_HOR); 
  $graph->SetY2Scale("lin",0,$max_humi); 
  $graph->title->set(__('HISTO_GRAPH')." (".$legend_date.")");
  $graph->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->ygrid->SetColor('blue');
  $graph->xgrid->SetColor('red');
  $graph->xaxis->title->Set(__($xlegend));
  $graph->yaxis->title->Set(__('YAXIS_LEGEND'));
  $graph->y2axis->title->Set(__('Y2AXIS_LEGEND'));
  $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->y2axis->title->SetFont(FF_FONT1,FS_BOLD);
  $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

  if("$type" == "month") {
    $graph->xaxis->SetTextTickInterval(288);  
  } else {
    $graph->xaxis->SetTextTickInterval(20);
  }
  $graph->xaxis->SetTickLabels($axis);
  $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
  $fileName = "../tmp/graph.png";
  $graph->img->Stream("../tmp/graph.png");

}

if("$type" == "month") {
         $startdate=date('Y')."-".date('m')."-".date('d');
} 

if((!isset($bmonth))||(empty($bmonth))) {
  $bmonth=date('m');
}

include('main/templates/logs.html');

?>
