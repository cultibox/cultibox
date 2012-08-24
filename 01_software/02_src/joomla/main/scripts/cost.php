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

//echo $theorical_power;

if((empty($data_power))||(!isset($data_power))||(empty($price))||(!isset($price))) {
      $compute=0;
} else {
      $price=($price/60)/1000;
      foreach($data_power as $val) {
         $compute=$compute+($val['record']*$price);
      }
}

include('main/templates/cost.html');

?>
