<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');

$error=array();
$main_error=array();
$main_info=array();

// ================= VARIABLES ================= //
$type=getvar("type");
$selected_plug=getvar('selected_plug');
$type_submit=getvar('type_submit');

if((empty($selected_plug))||(!isset($selected_plug))) {
    $selected_plug=1;
}

$chtime="";
$start_time="06:00:00";
$end_time="18:00:00";

if($selected_plug==1) {
    $plug_type="lamp";
} else {
    $plug_type=getvar('plug_type');
}

if($selected_plug>3) {
    $plug_power_max=getvar('plug_power_max');
    if(strcmp($plug_power_max,"VARIO")==0) {
       $plug_power_max=getvar("dimmer_canal");
       $type="2";
   }
} 
$value_program=getvar('value_program');


if((empty($value_program))||(!isset($value_program))) {
    if((!empty($plug_type))&&(isset($plug_type))) {
        switch ($plug_type) {
            case 'extractor':
            case 'intractor':
            case 'heating':
            case 'ventilator':
                $value_program=22.0;
                break;
            case 'humidifier':
            case 'dehumidifier':
                $value_program=55.0;
                break;
            case "pumpfiling":
            case "pumpempting":
            case "pump":
                $value_program=22.0;
                break;
            default :
                $value_program=0.0;
               break;
        }
    }
}


if((strcmp($type_submit,"submit_close")==0)||(strcmp($type_submit,"submit_next")==0)) {
    $old_plug_type=get_plug_conf("PLUG_TYPE",$selected_plug,$main_error);

    $program=getvar('program');

    if((isset($program))&&(!empty($program))) {
        if("$selected_plug"=="1") {
            $value_program="99.9";
            $plug_type="lamp";
            $start_time=getvar('start_time');
            $end_time=getvar('end_time');
        } else {
            $value_program=getvar('value_program');
            $plug_type=getvar('plug_type');
            $start_time=getvar('start_time');
            $end_time=getvar('end_time');
        }

        $chtime=check_times($start_time,$end_time); 

        switch ($plug_type) {
            case 'extractor':
            case 'intractor':
            case 'heating':
            case 'ventilator':
            case 'humidifier':
            case 'dehumidifier':
            case "pumpfiling":
            case "pumpempting":
            case "pump":
                if(!isset($type) || empty($type)) {
                    $type="1";
                }
                break;
            default :
                if(!isset($type) || empty($type))  {
                    $type="0";
                }
               break;
        }

        if((!isset($type))||(empty($type)))  {
            
        }

        if($chtime) {
            if($chtime==2) {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "23:59:59",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type",
                    "type" => "$type",
                    "number" => "1"
                );

                $prog[]= array(
                    "start_time" => "00:00:00",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type",
                    "type" => "$type",
                    "number" => "1" 
                ); 
            } else {
                $prog[]= array(
                    "start_time" => "$start_time",
                    "end_time" => "$end_time",
                    "value_program" => "$value_program",
                    "selected_plug" => "$selected_plug",
                    "plug_type" => "$plug_type",
                    "type" => "$type",
                    "number" => "1"
                );
            }

            // Clean current program
            clean_program($selected_plug,1,$main_error);
            
            insert_plug_conf("PLUG_TOLERANCE",$selected_plug,"0.0",$main_error);

            if(isset($plug_power_max)) {
                insert_plug_conf("PLUG_POWER_MAX",$selected_plug,$plug_power_max,$main_error);
            }
            $chinsert=true;
            if(!insert_program($prog,$main_error,"1"))
                $chinsert=false;

            if($chinsert) {
                insert_plug_conf("PLUG_TYPE",$prog[0]["selected_plug"],$prog[0]["plug_type"],$main_error);

                //Regulation customization if plug type changes:
                if(strcmp("$plug_type","$old_plug_type")!=0) {

                    $plug_regul=get_plug_conf("PLUG_REGUL",$selected_plug,$main_error);
                    
                    switch ($plug_type) {
                        case 'extractor':
                        case 'intractor':
                        case 'heating':
                        case 'ventilator':
                            insert_plug_conf("PLUG_SENSO",$prog[0]["selected_plug"],"H",$main_error);
                            insert_plug_conf("PLUG_REGUL_VALUE",$prog[0]["selected_plug"],"35",$main_error);
                            break;
                        case 'humidifier':
                        case 'dehumidifier':
                            insert_plug_conf("PLUG_SENSO",$prog[0]["selected_plug"],"T",$main_error);
                            insert_plug_conf("PLUG_REGUL_VALUE",$prog[0]["selected_plug"],"70",$main_error);
                            break;
                        case 'pumpfiling':
                        case 'pumpempting':
                        case 'pump':
                            insert_plug_conf("PLUG_REGUL",$prog[0]["selected_plug"],"False",$main_error);
                            insert_plug_conf("PLUG_SENSO",$prog[0]["selected_plug"],"T",$main_error);
                            insert_plug_conf("PLUG_SENSS",$prog[0]["selected_plug"],"+",$main_error);
                            insert_plug_conf("PLUG_REGUL_VALUE",$prog[0]["selected_plug"],"35",$main_error);
                            insert_plug_conf("PLUG_SECOND_TOLERANCE",$prog[0]["selected_plug"],"0",$main_error);
                            break;
                        default :
                            insert_plug_conf("PLUG_REGUL",$prog[0]["selected_plug"],"False",$main_error); 
                            insert_plug_conf("PLUG_REGUL_SENSOR",$prog[0]["selected_plug"],"1",$main_error);
                            insert_plug_conf("PLUG_SENSO",$prog[0]["selected_plug"],"T",$main_error);
                            insert_plug_conf("PLUG_SENSS",$prog[0]["selected_plug"],"+",$main_error);
                            insert_plug_conf("PLUG_REGUL_VALUE",$prog[0]["selected_plug"],"35",$main_error);
                            insert_plug_conf("PLUG_SECOND_TOLERANCE",$prog[0]["selected_plug"],"0",$main_error);
                            insert_plug_conf("PLUG_COMPUTE_METHOD",$prog[0]["selected_plug"],"M",$main_error);
                            break;
                    }
                }
            } else {
                unset($type_submit);
            }
        } 
    }

    echo json_encode($type_submit);
}

?>
