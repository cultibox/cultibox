<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// ================= VARIABLES ================= //
if(!isset($submenu)) {
    $submenu        = getvar("submenu",$main_error);
}

// By default the expanded menu is the user interface menu
if((!isset($submenu))||(empty($submenu))) {
    $submenu="user_interface";
} 

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { 
    if((!isset($sd_card))||(empty($sd_card))) {
        $hdd_list=array();
        $sd_card=get_sd_card($hdd_list);
        $new_arr=array();
        foreach($hdd_list as $hdd) {
            if(disk_total_space($hdd)<=2200000000) 
                $new_arr[]=$hdd;
        }
        $hdd_list=$new_arr;
        sort($hdd_list);
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}


//============================== GET OR SET CONFIGURATION PART ====================
//update_conf sert à définir si la configuration impacte la carte SD
$conf_arr = array();
$conf_arr["RECORD_FREQUENCY"]       = array ("update_conf" => "1", "var" => "record_frequency");
$conf_arr["POWER_FREQUENCY"]        = array ("update_conf" => "1", "var" => "power_frequency");
$conf_arr["UPDATE_PLUGS_FREQUENCY"] = array ("update_conf" => "1", "var" => "update_frequency");
$conf_arr["NB_PLUGS"]               = array ("update_conf" => "0", "var" => "nb_plugs");
$conf_arr["STATISTICS"]             = array ("update_conf" => "0", "var" => "stats");
$conf_arr["SHOW_COST"]              = array ("update_conf" => "0", "var" => "show_cost");
$conf_arr["ADVANCED_REGUL_OPTIONS"] = array ("update_conf" => "1", "var" => "advanced_regul");
$conf_arr["RTC_OFFSET"]             = array ("update_conf" => "1", "var" => "rtc_offset");
$conf_arr["RESET_MINMAX"]           = array ("update_conf" => "1", "var" => "reset_minmax");
$conf_arr["ALARM_VALUE"]            = array ("update_conf" => "1", "var" => "alarm_value");
$conf_arr["VERSION"]                = array ("update_conf" => "0", "var" => "version");
$conf_arr["ALARM_ACTIV"]            = array ("update_conf" => "1", "var" => "alarm_activ");
$conf_arr["ENABLE_LED"]             = array ("update_conf" => "1", "var" => "enable_led");
$conf_arr["SHOW_WEBCAM"]            = array ("update_conf" => "0", "var" => "show_webcam");



foreach ($conf_arr as $key => $value) {
    ${$value['var']} = get_configuration($key,$main_error);
}

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
