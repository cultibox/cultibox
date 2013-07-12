<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['nb_day']))&&(!empty($_GET['nb_day']))) {
    $nb_day=$_GET['nb_day'];
}


if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}


if((!isset($nb_day))||(empty($nb_day))) {   
    echo "NOK";
    return 0;
} 

if((!isset($type))||(empty($type))) {
    echo "NOK";
    return 0;
} 


if(strcmp($type,"power")==0) { 
    $file_type="pwr_";
} else {
    $file_type="";
}


    
// ************  Log and power file reading from SD card  ********************//
$log = array();
$power=array();

// Trying to find if a cultibox SD card is currently plugged and if it's the case, get the path to this SD card
if((!isset($sd_card))||(empty($sd_card))) {
   $sd_card=get_sd_card();
}

if((isset($sd_card))&&(!empty($sd_card))) {
    // Workaround to avoid timeout (60s)
    // Search only on 31 previous days
    $daySearch= date('j');
    $monthSearch = date('n');

    for($j=$nb_day;$j>0;$j--)  {
        $daySearch= $daySearch-1;
        if($daySearch==0) {
            $daySearch=31;
            $monthSearch=$monthSearch-1;
            if($monthSearch==0) {
                $monthSearch=12;
            }
        }
    }

    if(strlen($daySearch)<2) {
        $dday="0".$daySearch;
    } else {
        $dday=$daySearch;
    }

    if(strlen($monthSearch)<2) {
        $mmonth="0".$monthSearch;
    } else {
        $mmonth=$monthSearch;
    }

    // Search if file exists
    if(strcmp($type,"logs")==0) {
        if(file_exists("$sd_card/logs/$mmonth/$dday")) {
            // get log value
            if(is_file("$sd_card/logs/$mmonth/$dday")) {
                get_log_value("$sd_card/logs/$mmonth/$dday",$log);
            }

            if(!empty($log)) {
                if(db_update_logs($log,$main_error)) {
                    if(strcmp(date('md'),"${mmonth}${dday}")!=0) {
                        clean_log_file("$sd_card/logs/$mmonth/$dday"); 
                    } 
                } 
                unset($log) ;
                $log = array();
            }
        }
    } elseif(strcmp($type,"power")==0) {
        // get power values
        if(is_file("$sd_card/logs/$mmonth/pwr_$dday")) {
            get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power);
        }

        if(!empty($power)) {
            if(db_update_power($power,$main_error)) {
                clean_log_file("$sd_card/logs/$mmonth/pwr_$dday");
            }
            unset($power) ;
            $power = array();
        }
   }
}
echo "0";

if($nb_day==1) {
    if((!isset($_SESSION['LOAD_LOG']))||(empty($_SESSION['LOAD_LOG']))) {
        $_SESSION['LOAD_LOG']="True"; 
    }
}

?>
