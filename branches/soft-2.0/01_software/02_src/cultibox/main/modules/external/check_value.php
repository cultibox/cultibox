<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
} else {
    return 0;
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
} else {
    if(strcmp("$type","value_program")==0) {
        $value=0;
    } else  {
        return 0;
    }
}


switch($type) {
    case 'short_time':
        if(!check_format_time("$value:00")) {
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
                            if((!isset($_GET['plug_tolerance']))||(empty($_GET['plug_tolerance']))) {
                                $tolerance=0;
                            } else {
                                $tolerance=$_GET['plug_tolerance'];
                            }

                            $plug_type=$_GET['plug_type'];
                            $check=array();
                            if((strcmp($plug_type,"heating")==0)||(strcmp($plug_type,"ventilator")==0)) {
                                $check=check_format_values_program($value,"temp",$tolerance);
                            } elseif((strcmp($plug_type,"humidifier")==0)||(strcmp($plug_type,"dehumidifier")==0)) {
                                $check=check_format_values_program($value,"humi",$tolerance);
                            } elseif(strcmp($plug_type,"pump")==0) {
                                $check=check_format_values_program($value,"cm",$tolerance);
                            } else {
                                $check=check_format_values_program($value,"other",$tolerance);
                            }
                            if(count($check)==0) {
                                echo json_encode("error");
                            } else {
                                echo json_encode($check);
                            }
                          } else {
                            echo json_encode("error");
                          }
                          return 0;
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
    case 'ip':
        // Folowing code doesnot allow folowing adress : 192.001.001.001 ....
        // if(!filter_var($value, FILTER_VALIDATE_IP)) 
        $ipArray = explode("." , $value);
        if (count($ipArray) != 4)
            echo "error"; 
        break;
    case 'password_none':
        break;
    case 'password_wpa':
        if((strlen($value)>=8)&&(strlen($value)<=63)) {
            if(!ctype_alnum($value)) {
                echo "error";
            }
        } else {
            echo "error";
        }
        break;
    case 'password_wep':
        if((strlen($value)==5)||(strlen($value)==13)||(strlen($value)==29)) {
            if(!ctype_digit($value)) { 
                echo "error";
            }
        } else {
            echo "error";
        }
        break;
}
echo "1";

?>
