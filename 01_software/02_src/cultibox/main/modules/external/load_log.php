<?php

// Setting cokkie lifetime to 2hour before retrying the load log process:
setcookie("LOAD_LOG", "True", time()+7200,"/",false,false);


require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');


// Function to get log from the SD card
function get_log_value($sd_card,$month,$day,&$array_line,$sensors) {
    $file="$sd_card/logs/$month/$day";

    //Buffer to store data:
    $buffer_array=array();

    //Using file function to get all the data once:
    $buffer_array=file($file);

    //Data process:
    // For each line of the file
    foreach($buffer_array as $buffer) {
    
         //Check if a line has a valid format in the file, the function continues processing if it's the case:
         if(check_empty_string($buffer)) {
         
            //Each data is separated by the \t char, exploding in an array the data of a line:
            $temp = explode("\t", $buffer);


            //Check that the line has the right number of sensor records:
            if(count($temp)==(4*2+1)) {
                for($i=0;$i<count($temp);$i++) {
                    //Cleaning wrong value - deleting special char 
                    $temp[$i]=rtrim($temp[$i]);
                    $temp[$i]=str_replace(" ","",$temp[$i]);
                    if(strcmp($temp[$i],"0000")==0) {
                        $temp[$i]="";
                    }
                }

                //Setting other fild from the data line: date catch, time catch
                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);

                //If data are valid, continue processing:
                if(!empty($date_catch) && !empty($time_catch)
                    && !empty($temp[0]) && strlen($date_catch)==10 
                    && strlen($time_catch)==6 && strlen($temp[0])==14 ) {
                  
                    $column=1;
                    for($i=0; $i<count($sensors); $i++) {
                        //Creating data array which will be inserted into the database:
                        if(strcmp($sensors[$i]['type'],"0")!=0) {
                            if(strcmp($sensors[$i]['type'],"2")!=0) {
                                if(!empty($temp[$column])) {
                                    $array_line[] = array(
                                    "timestamp" => $temp[0],
                                    "record1" => $temp[$column],
                                    "record2" => "",
                                    "date_catch" => $date_catch,
                                    "time_catch" => $time_catch,
                                    "sensor_nb" => $sensors[$i]['sensor_nb'],
                                    );
                                }
                                $column=$column+1;
                            } else {
                                if((!empty($temp[$column]))&&(!empty($temp[$column+1]))) {
                                    $array_line[] = array(
                                    "timestamp" => $temp[0],
                                    "record1" => $temp[$column],
                                    "record2" => $temp[$column+1],
                                    "date_catch" => $date_catch,
                                    "time_catch" => $time_catch,
                                    "sensor_nb" => $sensors[$i]['sensor_nb'],
                                    );
                                    $column=$column+2;
                                } else {
                                    $column=$column+1;
                                } 
                                $i=$i+1;
                           }
                        } 
                    }
                }
            }
        }
    }
}

function get_power_value($file,&$array_line,$nb_plug=3) {
    $check=true;

    // If file does not exists, return
    if(!file_exists($file)) 
        return false;
        
    // Read the file
    $buffer_array=file($file);

    // For each line
    foreach($buffer_array as $buffer) {
    
        // Remove space
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
                for($i=0;$i<$nb_plug;$i++) {
                    $temp[$i]=rtrim($temp[$i]);
                }

                $date_catch="20".substr($temp[0], 0, 2)."-".substr($temp[0],2,2)."-".substr($temp[0],4,2);
                $date_catch=rtrim($date_catch);
                $time_catch=substr($temp[0], 8,6);
                $time_catch=rtrim($time_catch);

                if((!empty($date_catch))&&(!empty($time_catch))) {
                  for($i=1;$i<=$nb_plug;$i++) {
                     if(strlen($temp[$i])!=4) {
                        return false;
                     }
                  }


                  for($i=1;$i<=$nb_plug;$i++) {
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


if((isset($_GET['nb_day']))&&(!empty($_GET['nb_day']))) {
    $nb_day=trim($_GET['nb_day']);
} else { 
    $nb_day=0;
}

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=trim($_GET['type']);
}

if((isset($_GET['sd_card']))&&(!empty($_GET['sd_card']))) {
    $sd_card=trim($_GET['sd_card']);
}

if((isset($_GET['search']))&&(!empty($_GET['search']))) {
    $search=trim($_GET['search']);
}


if(!isset($type) || empty($type)) {
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

if(!isset($sd_card) || empty($sd_card)) {
    echo "NOK";
    return 0;
} 

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


$sensors = logs\get_sensor_db_type(); 

// Search if file exists
if($type == "logs") {

    if(file_exists("$sd_card/logs/$mmonth/$dday")) {
        // get log value
        if(is_file("$sd_card/logs/$mmonth/$dday")) {
            get_log_value($sd_card,$mmonth,$dday,$log,$sensors);
        }

        if(!empty($log)) {
            if(db_update_logs($log,$main_error)) {
                if(strcmp(date('md'),"${mmonth}${dday}")!=0) {
                    logs\save_log("$sd_card/logs/$mmonth/$dday","$mmonth","$dday","logs");
                    copy_template_file("empty_file_big.tpl","$sd_card/logs/$mmonth/$dday"); 
                } 
            }
            unset($log) ;
            $log = array();
        } 
    } 
} elseif($type == "power") {
    // get power values
    if(is_file("$sd_card/logs/$mmonth/pwr_$dday")) {
        get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power,get_configuration("NB_PLUGS",$main_error));
    }

    if(!empty($power)) {
        if(db_update_power($power,$main_error)) {
            logs\save_log("$sd_card/logs/$mmonth/$dday","$mmonth","$dday","power"); 
            copy_template_file("empty_file_big.tpl","$sd_card/logs/$mmonth/pwr_$dday");
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
            get_log_value($sd_card,$mmonth,$dday,$log,$sensors);
        }

        if(!empty($log)) {
            echo "-2";
            return 0;
        }

        if(file_exists("$sd_card/logs/$mmonth/pwr_$dday")) {
            get_power_value("$sd_card/logs/$mmonth/pwr_$dday",$power,get_configuration("NB_PLUGS",$main_error));  
        }

        if(!empty($power)) {
            echo "-2";
            return 0;
        } 
        $nb_day=$nb_day-1;
    }
}
   

echo "0";

?>
