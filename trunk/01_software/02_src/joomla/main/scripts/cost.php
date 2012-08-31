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
$startday=getvar('startday');
$endday=getvar('endday');
$nb_plugs=get_configuration("NB_PLUGS",$error);
$price=get_configuration("COST_PRICE",$error);
$plugs_infos=get_plugs_infos($nb_plugs,$error);
$select_plug=getvar('select_plug');
$compute=0;
$pop_up_error_message="";
$pop_up="";
$color_cost = get_configuration("COLOR_COST_GRAPH",$error);
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);
$info="";

if(!isset($pop_up)) {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}


if((!isset($select_plug))||(empty($select_plug))) {
   $select_plug="all";
}

if((!isset($startday))||(empty($startday))) {
   $startday=date('Y')."-".date('m')."-".date('d');
} 
$startday=str_replace(' ','',"$startday");

if((!isset($endday))||(empty($endday))) {
   $endday=date('Y')."-".date('m')."-".date('d');
} 
$endday=str_replace(' ','',"$endday");

$check_format=check_format_date($startday,"days");
if(!$check_format) {
      $error=$error.__('ERROR_FORMAT_DATE_DAY');
      $pop_up_error_message=clean_popup_message($error);
      $startday=date('Y')."-".date('m')."-".date('d');
      $endday=$startday;
      $check_format=true;
} else {
   $check_format=check_format_date($endday,"days");
   if(!$check_format) {
      $error=$error.__('ERROR_FORMAT_DATE_DAY');
      $pop_up_error_message=clean_popup_message($error);
      $startday=date('Y')."-".date('m')."-".date('d');
      $endday=$startday;
      $check_format=true;
   }
}

if(!check_date("$startday,","$endday")) {
      $startday=date('Y')."-".date('m')."-".date('d');
      $endday=$startday;
      $error=$error.__('ERROR_DATE_INTERVAL');
      $pop_up_error_message=clean_popup_message($error);
}

$data_power=get_data_power($startday,$endday,$select_plug,$error);
$theorical_power=get_theorical_power($select_plug,$price,$error);
$nb=get_nb_days($startday,$endday)+1;
$theorical_power=$theorical_power*$nb;



if((empty($data_power))||(!isset($data_power))||(empty($price))||(!isset($price))) {
      $compute=0;
} else {
      $price=($price/60)/1000;
      foreach($data_power as $val) {
         $compute=$compute+($val['record']*$price);
      }
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

include('main/templates/cost.html');

?>
