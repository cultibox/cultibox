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
$update=get_configuration("CHECK_UPDATE",$error);
$version=get_configuration("VERSION",$error);
$cost_type=get_configuration("COST_TYPE",$error);
$info="";

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

if(strcmp($select_plug,"distinct_all")!=0) {
    $theorical_power=get_theorical_power($select_plug,$cost_type,$error);
    $nb=get_nb_days($startday,$endday)+1;
    $theorical_power=$theorical_power*$nb;
    $theorical_power=number_format($theorical_power,2);

    $data_power=get_data_power($startday,$endday,$select_plug,$error);

    if(strcmp($select_plug,"all")==0) {
        $title=__('PRICE_SELECT_ALL_PLUG');
        $color_cost = get_configuration("COLOR_COST_GRAPH",$error);
    } else {
        $title=$plugs_infos[$select_plug-1]['PLUG_NAME'];
        $color_cost=$GLOBALS['LIST_GRAPHIC_COLOR'][$select_plug-1];
    }

    $data_power=get_data_power($startday,$endday,$select_plug,$error);

    $data_price[]= array( 
        "number" => "$select_plug",
        "theorical" => "$theorical_power",
        "real" => "$data_power", 
        "title" => "$title",
        "color" => "$color_cost"
    );
} else {
    $nb_plugs = get_configuration("NB_PLUGS",$error);
    $nb=get_nb_days($startday,$endday)+1;
    for($i=1;$i<=$nb_plugs;$i++) {
       $theorical_power=get_theorical_power($i,$cost_type,$error);
       $theorical_power=$theorical_power*$nb;
       $theorical_power=number_format($theorical_power,2);

       $data_power=get_data_power($startday,$endday,$i,$error);
       $title=$plugs_infos[$i-1]['PLUG_NAME'];

       $data_price[]= array(
        "number" => "$i",
        "real" => "$data_power",
        "theorical" => "$theorical_power",
        "title" => "$title",
        "color" => $GLOBALS['LIST_GRAPHIC_COLOR'][$i-1]
       ); 
    }
}


if((empty($data_power))||(!isset($data_power))||(empty($price))||(!isset($price))) {
      $compute=0;
} else {
      $price=($price/60)/1000;
      foreach($data_power as $val) {
         $compute=$compute+($val['record']*$price);
      }
}
$compute=number_format($compute,2);

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




include('main/templates/cost.html');

?>
