<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

function get_log_value($sd_card,$month,$day,&$array_line,$sensor_type) {
   $file="$sd_card/logs/$month/$day";
   $buffer_array=array();

   if(!file_exists("$file")) return false;

   $buffer_array=file("$file");

   foreach($buffer_array as $buffer) {
         if(check_empty_string($buffer)) {
            $temp = explode("\t", $buffer);
            if(count($temp)==($GLOBALS['NB_MAX_SENSOR_LOG']*2+1)) {
                for($i=0;$i<count($temp);$i++) {
                    $temp[$i]=rtrim($temp[$i]);
                    $temp[$i]=str_replace(" ","",$temp[$i]);
                    $temp[$i]=str_replace("0000","",$temp[$i]);
                }

                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);


                if((!empty($date_catch))&&(!empty($time_catch))&&(!empty($temp[0]))&&(strlen($date_catch)==10)&&(strlen($time_catch)==6)&&(strlen($temp[0])==14)) {
                        for($i=0;$i<$GLOBALS['NB_MAX_SENSOR_LOG'];$i++) {
                            $sens_type="";
                            if(count($sensor_type)==0) {
                                $sens_type="2";
                            } else {
                                foreach($sensor_type as $sens) {
                                    if($sens["id"]==$i+1) {
                                        $sens_type=$sens["id"];
                                        break;
                                    }
                                 }

                                 if(strcmp("$sens_type","")==0) {
                                    $sens_type="2";
                                 }
                            }

                            if((!empty($temp[2*$i+1]))||(!empty($temp[2*$i+2]))) {
                                        $array_line[] = array(
                                            "timestamp" => $temp[0],
                                            "temperature" => $temp[1+2*$i],
                                            "humidity" => $temp[2+2*$i],
                                            "date_catch" => $date_catch,
                                            "time_catch" => $time_catch,
                                            "sensor_nb" => $i+1,
                                            "sensor_type" => $sens_type
                                        );
                            }
                        }
                }
            }
        }
    }
}


function get_power_value($file,&$array_line) {
   $check=true;
   if(!file_exists("$file")) return false;
   $buffer_array=file("$file");

   foreach($buffer_array as $buffer) {
        $buffer=trim($buffer);
        if(!check_empty_string($buffer)) {
            if($check) {
                $check=false;
            } else {
                break;
            }
         } else {
            if(!$check) $check=true;
            $temp=explode("\t", $buffer);

            if(count($temp)==17) {
                for($i=0;$i<count($temp);$i++) {
                    $temp[$i]=rtrim($temp[$i]);
                }

                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);

                if((!empty($date_catch))&&(!empty($time_catch))) {
                  for($i=1;$i<count($temp);$i++) {
                     if(strlen($temp[$i])!=4) {
                        return false;
                     }
                  }


                  for($i=1;$i<count($temp);$i++) {
                        if(is_numeric($temp[$i])) {
                            $array_line[] = array(
                                "timestamp" => $temp[0],
                                "power" => $temp[$i],
                                "plug_number" => $i,
                                "date_catch" => $date_catch,
                                "time_catch" => $time_catch
                            );
                        }
                  }
                }
            }
         }
    }
}






if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['nb_day']))&&(!empty($_GET['nb_day']))) {
    $nb_day=$_GET['nb_day'];
} else { 
    $nb_day=0;
}


if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}


if((isset($_GET['sd_card']))&&(!empty($_GET['sd_card']))) {
    $sd_card=$_GET['sd_card'];
}

if((isset($_GET['search']))&&(!empty($_GET['search']))) {
    $search=$_GET['search'];
}


if((!isset($type))||(empty($type))) {
    echo "NOK";
    return 0;
} 


if((!isset($sd_card))||(empty($sd_card))) {
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

if((isset($sd_card))&&(!empty($sd_card))) {
    // Workaround to avoid timeout (60s)
    // Search only on 31 previous days

    $daySearch= date('j');
    $monthSearch = date('n');

    if($nb_day!=0) {
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

    if((!isset($_SESSION['LOAD_LOG']))||(empty($_SESSION['LOAD_LOG']))) {
        $_SESSION['LOAD_LOG']="True";
    }
    
    if((isset($_SESSION['sensor_type']))&&(!empty($_SESSION['sensor_type']))) {
        $sensor_type=$_SESSION['sensor_type'];
    } else {
        $sensor_type=array();
    }

    // Search if file exists
    if(strcmp($type,"logs")==0) {
        if(file_exists("$sd_card/logs/$mmonth/$dday")) {
            // get log value
            if(is_file("$sd_card/logs/$mmonth/$dday")) {
                get_log_value("$sd_card","$mmonth","$dday",$log,$sensor_type);
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


   if(($nb_day==0)&&(strcmp($search,"auto")==0)) {
        $log=array();
        $power=array();
        $daySearch= date('j');
        $monthSearch = date('n');

        $nb_day=38;
        while($nb_day>31) {
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

            if(file_exists("$sd_card/logs/$mmonth/$dday")) {
                get_log_value("$sd_card","$mmonth","$dday",$log,$sensor_type);
            }

            if(!empty($log)) {
                echo "-2";
                return 0;
            }

            if(file_exists("$sd_card/logs/$mmonth/pwr_$dday")) {
                get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power);  
            }

            if(!empty($power)) {
                echo "-2";
                return 0;
            } 
            $nb_day=$nb_day-1;
        }
   }
   
}
echo "0";

?>
