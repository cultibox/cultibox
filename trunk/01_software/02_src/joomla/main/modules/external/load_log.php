<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['month']))&&(!empty($_GET['month']))) {
    $month=$_GET['month'];
}


if((isset($_GET['progress']))&&(!empty($_GET['progress']))) {
    $progress=$_GET['progress'];
}

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}


if((!isset($month))||(empty($month))) {
    echo 100;
    return 0;
} 

if((!isset($type))||(empty($type))) {
    echo -1;
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
    $monthSearch = date('n');
    $daySearch= date('j');

    $monthSearch=$monthSearch-$month;
    if($monthSearch<0) {
        $monthSearch=12-abs($monthSearch);
    }

    $nb_days=31;
    $count=0;

    if((!isset($_SESSION['LOAD_LOG']))||(empty($_SESSION['LOAD_LOG']))) {
        $_SESSION['LOAD_LOG']="True"; 
    }


    while($count!=$nb_days) {
        if(!isset( $_SESSION['LOAD_LOG'])) {
            $_SESSION['LOAD_LOG'] = "True";
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

        $count=$count+1;
        $daySearch=$daySearch+1;
        if($daySearch==32) {
            $daySearch=1;
            $monthSearch=$monthSearch+1;
            if($monthSearch==13) {
                $monthSearch=1;
            }
        }
    }
    $progress=100-($month*5);
    echo $progress;
    return 0;
}
echo 100;
return 0;

?>
