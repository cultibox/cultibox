<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
} else {
    return 0;
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
} else {
    return 0;
}





switch($type) {
    case 'short_time': if(!check_format_time("$value:00")) {
                            echo "error";
                        }
                        break;
    case 'alarm_value': if(!(check_numeric_value("$value"))||(!check_alarm_value("$value"))) {
                            echo "error";
                        }
                        break;
   case 'date': if(!check_format_date($value,"days")) {
                        echo "error";
                }
                break;
    case 'time': if(!check_format_time("$value")) {
                     echo "error";
                 }
                 break;
    case 'same_time': $value=explode('_',$value);
                      if(count($value)!=2) {
                        echo "error";
                        break;
                       }
                
                       if(!check_times($value[0],$value[1])) {
                            echo "error";
                       }
                       break;
    case 'cyclic_time';
                     if(!check_format_time("$value")) {
                        echo "error";
                     }
                     $tmp=str_replace(":","",$value);
                     if($tmp<500) {
                        echo "2"; 
                        return 0;
                     }
                     break;
    case 'value_program': if((isset($_GET['plug_type']))&&(!empty($_GET['plug_type']))) {
                            $plug_type=$_GET['plug_type'];
                            $check=0;
                            if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
                                $check=check_format_values_program($value,"temp");
                            } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"dehumidifier")==0)) {
                                $check=check_format_values_program($value,"humi");
                            } elseif(strcmp($plug_type,"pump")==0) {
                                $check=check_format_values_program($value,"cm");
                            } else {
                                $check=check_format_values_program($value,"other");
                            }
                            if($check!=1) {
                                echo "$check";
                                return 0;
                            }
                          } else {
                            echo "error";
                            return 0;
                          }
                          break;
    case 'month': if(!check_format_date("$value",$type)) {
                        echo "error";
                    }
                    break;
    case 'date_interval': $value=explode('_',$value);
                          if(count($value)!=2) {
                            echo "error";
                            break;
                          }

                          if(!check_date($value[0],$value[1])) {
                              echo "error";
                          }
                          break;
    case 'numeric': if(!check_numeric_value("$value")) {
                        echo "error";
                    }
                    break; 
    

    case 'tolerance': 
                        if((isset($_GET['plug']))&&(!empty($_GET['plug']))) {
                            $plug=$_GET['plug'];
                        } else {
                            return 0;
                        }

                        if(!check_tolerance_value($plug,$value)) {
                            echo "error";
                        }
                        break;

    case 'regulation': if(!check_regul_value("$value")) {
                            echo "error";
                       }
                       break;
    case 'ssid': 
                break;
    case 'password':
                $value=explode('____',$value);
                if(count($value)!=2) {
                    echo "error";
                    break;
                }

                if(strcmp(trim($value[0]),trim($value[1]))!=0) {
                    echo "error";
                    break;
                }
                break;
}
echo "1";

?>
