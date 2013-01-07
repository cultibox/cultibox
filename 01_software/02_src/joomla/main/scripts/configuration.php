<?php

if (!isset($_SESSION)) {
	session_start();
}

require_once('main/libs/config.php');
require_once('main/libs/db_common.php');
require_once('main/libs/utilfunc.php');



$error="";
$main_error="";

$color_humidity = getvar('color_humidity');
$color_temperature = getvar('color_temperature');
$color_program=getvar('color_program');
$color_power=getvar('color_power');
$color_cost=getvar('color_cost');
$record_frequency=getvar('record_frequency');
$power_frequency=getvar('power_frequency');
$update_frequency=getvar('update_frequency');
$nb_plugs=getvar('nb_plugs');
$lang=getvar('lang');
$update_conf=false;
$info="";
$temp_axis=getvar('temp_axis');
$hygro_axis=getvar('hygro_axis');
$power_axis=getvar('power_axis');
$pop_up=getvar('pop_up');
$pop_up_message="";
$pop_up_error_message="";
$alarm_enable=getvar('alarm_enable');
$alarm_value=getvar('alarm_value');
$alarm_senso=getvar('alarm_senso');
$alarm_senss=getvar('alarm_senss');
$cost_price=getvar('cost_price');
$cost_price_hp=getvar('cost_price_hp');
$cost_price_hc=getvar('cost_price_hc');
$cost_type=getvar('cost_type');
$update=getvar('update');
$program="";
$version=get_configuration("VERSION",$error);
$log_search=getvar("log_search",$error);
$start_hc=getvar("start_hc",$error);
$stop_hc=getvar("stop_hc",$error);
$menu=getvar("menu",$error);

if((!isset($menu))||(empty($menu))) {
        $menu="user_interface";
} 

if((isset($lang))&&(!empty($lang))) {
	insert_configuration("LANG",$lang,$error);
} else {
	$lang=get_configuration("LANG",$error);
}

set_lang($lang);
$_SESSION['LANG'] = get_current_lang();
__('LANG');


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
   $info=$info.__('INFO_SD_CARD').": $sd_card";
} else {

        $tmp="";
        $tmp=__('ERROR_SD_CARD_CONF');
        $tmp_title=__('TOOLTIP_WITHOUT_SD');
        $tmp=str_replace("</li>"," <img src=\"main/libs/img/infos.png\" alt=\"\" class=\"info-bulle-css\" title=\"$tmp_title\" /></li>",$tmp);
        $main_error=$main_error.$tmp;
}


if((isset($pop_up))&&(!empty($pop_up))) {
	    insert_configuration("SHOW_POPUP","$pop_up",$error);
        $update_conf=true;
} else {
        $pop_up = get_configuration("SHOW_POPUP",$error);
}

if((isset($update))&&(!empty($update))) {
   insert_configuration("CHECK_UPDATE",$update,$error);
      $update_conf=true;
} else {
   $update = get_configuration("CHECK_UPDATE",$error);
}

if((isset($color_humidity))&&(!empty($color_humidity))) {
	insert_configuration("COLOR_HUMIDITY_GRAPH",$color_humidity,$error);
    	$update_conf=true;
} else {
	$color_humidity = get_configuration("COLOR_HUMIDITY_GRAPH",$error);
}


if((isset($color_temperature))&&(!empty($color_temperature))) {
	insert_configuration("COLOR_TEMPERATURE_GRAPH",$color_temperature,$error);
	$update_conf=true;
} else {
	$color_temperature = get_configuration("COLOR_TEMPERATURE_GRAPH",$error);
}

if((isset($color_program))&&(!empty($color_program))) {
   insert_configuration("COLOR_PROGRAM_GRAPH",$color_program,$error);
   $update_conf=true;
} else {
   $color_program = get_configuration("COLOR_PROGRAM_GRAPH",$error);
}

if((isset($color_power))&&(!empty($color_power))) {
   insert_configuration("COLOR_POWER_GRAPH",$color_power,$error);
   $update_conf=true;
} else {
   $color_power = get_configuration("COLOR_POWER_GRAPH",$error);
}

if((isset($color_cost))&&(!empty($color_cost))) {
   insert_configuration("COLOR_COST_GRAPH",$color_cost,$error);
   $update_conf=true;
} else {
   $color_cost = get_configuration("COLOR_COST_GRAPH",$error);
}

if((isset($temp_axis))&&(!empty($temp_axis))) {
        insert_configuration("LOG_TEMP_AXIS",$temp_axis,$error);
        $update_conf=true;
} else {
        $temp_axis = get_configuration("LOG_TEMP_AXIS",$error);
}


if((isset($hygro_axis))&&(!empty($hygro_axis))) {
        insert_configuration("LOG_HYGRO_AXIS",$hygro_axis,$error);
        $update_conf=true;
} else {
        $hygro_axis = get_configuration("LOG_HYGRO_AXIS",$error);
}

if((isset($power_axis))&&(!empty($power_axis))) {
        insert_configuration("LOG_POWER_AXIS",$power_axis,$error);
        $update_conf=true;
} else {
        $power_axis = get_configuration("LOG_POWER_AXIS",$error);
}


if((isset($record_frequency))&&(!empty($record_frequency))) {
	insert_configuration("RECORD_FREQUENCY",$record_frequency,$error);
	$update_conf=true;
} else {
	$record_frequency = get_configuration("RECORD_FREQUENCY",$error);
}

if((isset($power_frequency))&&(!empty($power_frequency))) {
        insert_configuration("POWER_FREQUENCY",$power_frequency,$error);
        $update_conf=true;
} else {
        $power_frequency = get_configuration("POWER_FREQUENCY",$error);
}


if((isset($nb_plugs))&&(!empty($nb_plugs))) {
	insert_configuration("NB_PLUGS",$nb_plugs,$error);
	$update_conf=true;
} else {
	$nb_plugs = get_configuration("NB_PLUGS",$error);
}

if(!empty($update_frequency)) {
	insert_configuration("UPDATE_PLUGS_FREQUENCY",$update_frequency,$error);
	$update_conf=true;
} else {
	$update_frequency = get_configuration("UPDATE_PLUGS_FREQUENCY",$error);
}


if(!empty($alarm_enable)) {
        insert_configuration("ALARM_ACTIV","$alarm_enable",$error);
        $update_conf=true;
} else {
        $alarm_enable = get_configuration("ALARM_ACTIV",$error);
}

if(!empty($alarm_value)) {
	if((check_numeric_value("$alarm_value"))&&(check_alarm_value("$alarm_value"))) {
           insert_configuration("ALARM_VALUE","$alarm_value",$error);
           $update_conf=true;
        } else {
           $alarm_value=get_configuration("ALARM_VALUE",$error);
           $error=$error.__('ERROR_ALARM_VALUE');
           $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
        }
} else {
        $alarm_value = get_configuration("ALARM_VALUE",$error);
}

if(!empty($alarm_senso)) {
        insert_configuration("ALARM_SENSO","$alarm_senso",$error);
        $update_conf=true;
} else {
        $alarm_senso = get_configuration("ALARM_SENSO",$error);
}

if(!empty($alarm_senss)) {
        insert_configuration("ALARM_SENSS","$alarm_senss",$error);
        $update_conf=true;
} else {
        $alarm_senss = get_configuration("ALARM_SENSS",$error);
}

if(!empty($cost_type)) {
        insert_configuration("COST_TYPE","$cost_type",$error);
        $update_conf=true;
} else {
        $cost_type = get_configuration("COST_TYPE",$error);
}

if(!empty($cost_price)||("$cost_price"=="0")) {
         $cost_price=str_replace(",",".","$cost_price");
         if(check_numeric_value("$cost_price")) {
            insert_configuration("COST_PRICE","$cost_price",$error);
            $update_conf=true;
         } else {
            $cost_price = get_configuration("COST_PRICE",$error);
            $error=$error.__('ERROR_PRICE_VALUE');
            $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
         }
} else {
        $cost_price = get_configuration("COST_PRICE",$error);
}


if(!empty($cost_price_hc)||("$cost_price_hc"=="0")) {
         $cost_price_hc=str_replace(",",".","$cost_price_hc");
         if(check_numeric_value("$cost_price_hc")) {
            insert_configuration("COST_PRICE_HC","$cost_price_hc",$error);
            $update_conf=true;
         } else {
            $cost_price_hc = get_configuration("COST_PRICE_HC",$error);
            $error=$error.__('ERROR_PRICE_VALUE');
            $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
         }
} else {
        $cost_price_hc = get_configuration("COST_PRICE_HC",$error);
}

if(!empty($cost_price_hp)||("$cost_price_hp"=="0")) {
         $cost_price_hp=str_replace(",",".","$cost_price_hp");
         if(check_numeric_value("$cost_price_hp")) {
            insert_configuration("COST_PRICE_HP","$cost_price_hp",$error);
            $update_conf=true;
         } else {
            $cost_price_hp = get_configuration("COST_PRICE_HP",$error);
            $error=$error.__('ERROR_PRICE_VALUE');
            $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
         }
} else {
        $cost_price_hp = get_configuration("COST_PRICE_HP",$error);
}

if((isset($start_hc))&&(!empty($start_hc))) {
        if(strlen($start_hc)==4) {
                $start_hc="0$start_hc";
        }

        if((strlen($start_hc)==5)&&(preg_match('#^[0-9][0-9]:[0-9][0-9]$#', $start_hc))) {
                insert_configuration("START_TIME_HC","$start_hc",$error);
                $update_conf=true;        
        } else {
                $start_hc = get_configuration("START_TIME_HC",$error);
                $error=$error.__('ERROR_TIME_VALUE');
                $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
        }
} else {
        $start_hc = get_configuration("START_TIME_HC",$error);
}


if((isset($stop_hc))&&(!empty($stop_hc))) {
        if(strlen($stop_hc)==4) {
                $stop_hc="0$stop_hc";
        }

        if((strlen($stop_hc)==5)&&(preg_match('#^[0-9][0-9]:[0-9][0-9]$#', $stop_hc))) {
                insert_configuration("STOP_TIME_HC","$stop_hc",$error);
                $update_conf=true;
        } else {
                $stop_hc = get_configuration("STOP_TIME_HC",$error);
                $error=$error.__('ERROR_TIME_VALUE');
                $pop_up_error_message=$pop_up_error_message.clean_popup_message($error);
        } 
} else {
        $stop_hc = get_configuration("STOP_TIME_HC",$error);
}


if(!empty($log_search)) {
        insert_configuration("LOG_SEARCH","$log_search",$error);
        $update_conf=true;
} else {
        $log_search = get_configuration("LOG_SEARCH",$error);
}

if((empty($error))||(!isset($error))) {
	if($update_conf) {
        if((!empty($sd_card))&&(isset($sd_card))) {
			   $info=$info.__('VALID_UPDATE_CONF');
			   $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_CONF'));
         } else {
            $info=$info.__('VALID_UPDATE_CONF_WITHOUT_SD');
            $pop_up_message=$pop_up_message.clean_popup_message(__('VALID_UPDATE_CONF_WITHOUT_SD'));
         }
	}
}

if((isset($sd_card))&&(!empty($sd_card))) {
	if("$update_frequency"=="-1") {
		write_sd_conf_file($sd_card,$record_frequency,"0",$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$error);
	} else {
		write_sd_conf_file($sd_card,$record_frequency,$update_frequency,$power_frequency,"$alarm_enable","$alarm_value","$alarm_senso","$alarm_senss",$error);	
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

include('main/templates/configuration.html');

?>
