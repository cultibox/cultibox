<?php

// Compute page time loading for debug option
$start_load = getmicrotime();

// Language for the interface, using a COOKIE variable and the function __('$msg') from utilfunc.php library to print messages
$main_error=array();
$main_info=array();

// ================= VARIABLES ================= //
$sd_card=""; //Path of the SD card


// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($GLOBALS['MODE']))||(strcmp($GLOBALS['MODE'],"cultipi")!=0)) { 
    if((!isset($sd_card))||(empty($sd_card))) {
        $sd_card=get_sd_card();
    }
} else {
    $sd_card = $GLOBALS['CULTIPI_CONF_TEMP_PATH'];
    if((!is_dir($sd_card."/serverAcqSensor"))||(!is_dir($sd_card."/serverHisto"))||(!is_dir($sd_card."/serverPlugUpdate"))||(!is_dir($sd_card."/serverLog"))) {
            check_and_update_sd_card($sd_card,$info,$error,false);
    }
}


if((!isset($sd_card))||(empty($sd_card))) {
    setcookie("CHECK_SD", "False", time()+1800,"/",false,false);
}

$screen="";
$webcam_conf=webcam\get_webcam_conf();



for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
    if(is_file($GLOBALS['BASE_PATH']."tmp/webcam$i.jpg")) {
        $screen{$i}="/cultibox/tmp/webcam$i.jpg";
    }
}


//Compute time loading for debug option
$end_load = getmicrotime();

if($GLOBALS['DEBUG_TRACE']) { //If the debug option is activated, we print generation time of the page, variables sizes...
    echo __('GENERATE_TIME').": ".round($end_load-$start_load, 3) ." s.<br />";
    echo "---------------------------------------";
    aff_variables();
    echo "---------------------------------------<br />";
    memory_stat();
}


?>
