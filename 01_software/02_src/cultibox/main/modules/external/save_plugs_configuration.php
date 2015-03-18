<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');

$main_error=array();

$nb     = getvar("number");
$name   = html_entity_decode(getvar("plug_name${nb}"));
$type   = getvar("plug_type${nb}");
$tolerance  = getvar("plug_tolerance{$nb}");
$power      = getvar("plug_power${nb}");
$compute_method = getvar("plug_compute_method${nb}");
$second_regul   = get_configuration("ADVANCED_REGUL_OPTIONS",$main_error);
$module = getvar("plug_module${nb}");

// Regulation is not available for lamp and other
switch ($type) 
{
    case "extractor":
    case "intractor":
    case "ventilator":
    case "heating":
    case "pumpfiling":
    case "pumpempting":
    case "pump":
    case "humidifier":
    case "dehumidifier":
        $regul = "False";
        break;
    default:
        $regul       = getvar("plug_regul${nb}");
        $regul_senss = getvar("plug_senss${nb}");
        $regul_value = getvar("plug_regul_value${nb}");
        $regul_value = str_replace(',','.',$regul_value);
        $regul_value = str_replace(' ','',$regul_value);
        $second_tol  = getvar("plug_second_tolerance${nb}");
        $second_tol  = str_replace(',','.',$second_tol);
        break;
}

// retrieve max power
$power_max  = getvar("plug_power_max${nb}");
$num_module = getvar("plug_num_module${nb}"); 
$module_options = getvar("plug_module_options${nb}"); 
$module_output  = getvar("plug_module_output${nb}"); 

$sensor = "";
$plug_count_sensor[$nb] = 0;
for($j = 1 ; $j <= $GLOBALS['NB_MAX_SENSOR_PLUG'] ; $j++) { 
    $tmp_sensor = getvar("plug_sensor${nb}${j}");
    if($tmp_sensor == "True") {
        $plug_count_sensor[$nb] = $plug_count_sensor[$nb]+1;
        if($sensor != "") {
            $sensor = $sensor . "-" . $j;
        } else {
            $sensor = $j;
        }
    }
}

if($sensor=="") {
    $sensor="1";
}

$old_name       = get_plug_conf("PLUG_NAME",$nb,$main_error);
$old_type       = get_plug_conf("PLUG_TYPE",$nb,$main_error);
$old_tolerance  = get_plug_conf("PLUG_TOLERANCE",$nb,$main_error);
$old_power      = get_plug_conf("PLUG_POWER",$nb,$main_error);
$old_regul      = get_plug_conf("PLUG_REGUL",$nb,$main_error);
$old_senso      = get_plug_conf("PLUG_SENSO",$nb,$main_error);
$old_senss      = get_plug_conf("PLUG_SENSS",$nb,$main_error);
$old_regul_value= get_plug_conf("PLUG_REGUL_VALUE",$nb,$main_error);
$old_power_max  = get_plug_conf("PLUG_POWER_MAX",$nb,$main_error);
$old_sensor     = get_plug_conf("PLUG_REGUL_SENSOR",$nb,$main_error);
$old_second_tol = get_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$main_error);
$old_compute_method = get_plug_conf("PLUG_COMPUTE_METHOD",$nb,$main_error);
$old_module     = get_plug_conf("PLUG_MODULE",$nb,$main_error);
$old_num_module = get_plug_conf("PLUG_NUM_MODULE",$nb,$main_error);
$old_module_options = get_plug_conf("PLUG_MODULE_OPTIONS",$nb,$main_error);
$old_module_output  = get_plug_conf("PLUG_MODULE_OUTPUT",$nb,$main_error);

// Save the name of the plug
if( !empty($name) && isset($name) && $old_name != $name) {
    $name = mysql_escape_string($name);
    insert_plug_conf("PLUG_NAME",$nb,$name,$main_error);
}
   
// Save tolerance
switch ($type) {
    case "extractor":
    case "intractor":
    case "ventilator":
    case "heating":
    case "pumpfiling":
    case "pumpempting":
    case "pump":
    case "humidifier":
    case "dehumidifier":
        insert_plug_conf("PLUG_TOLERANCE",$nb,$tolerance,$main_error);
        break;
    default:
        return true;
        break;
}


if(!empty($power) && isset($power) && $old_power != $power) {
    insert_plug_conf("PLUG_POWER",$nb,$power,$main_error);
} else {
    if(empty($power) && !empty($reccord) && $old_power != $power) {
        insert_plug_conf("PLUG_POWER",$nb,"",$main_error);
    }
}

if(!empty($power_max) && isset($power_max) && $old_power_max != $power_max) {
    insert_plug_conf("PLUG_POWER_MAX",$nb,$power_max,$main_error);
} 


if(!empty($regul) && isset($regul) && $old_regul != $regul) {
    insert_plug_conf("PLUG_REGUL",$nb,$regul,$main_error);
}


if($regul == "True") {
    if(!empty($regul_senss) && isset($regul_senss) && $old_senss != $regul_senss) {
        insert_plug_conf("PLUG_SENSS",$nb,$regul_senss,$main_error);
    }
}

if(!empty($second_tol) && isset($second_tol) && $old_second_tol != $second_tol) {
    insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,$second_tol,$main_error);
}

switch ($type) {
    case "extractor":
    case "intractor":
    case "ventilator":
    case "heating":
        $regul_senso = "H";
        break;
    case "pumpfiling":
    case "pumpempting":
    case "pump":
        $regul_senso = "L";
        break;
    case "humidifier":
    case "dehumidifier":
        $regul_senso = "T";
        break;
    default:
        $regul_senso = getvar("plug_senso${nb}");
        break;
}

if(!empty($regul_senso) && isset($regul_senso) && $old_senso != $regul_senso) {
    insert_plug_conf("PLUG_SENSO",$nb,$regul_senso,$main_error);
}

if(!empty($regul_value) && isset($regul_value) && $old_regul_value != $regul_value) {
    insert_plug_conf("PLUG_REGUL_VALUE",$nb,$regul_value,$main_error);
}

if(!empty($sensor) && isset($sensor) && $old_sensor != $sensor) {
    insert_plug_conf("PLUG_REGUL_SENSOR",$nb,$sensor,$main_error);
}

if($plug_count_sensor[$nb]>1) {
    if(!empty($compute_method) && isset($compute_method) && $old_compute_method != $compute_method) {
        insert_plug_conf("PLUG_COMPUTE_METHOD",$nb,$compute_method,$main_error);
    }
} else {
    insert_plug_conf("PLUG_COMPUTE_METHOD",$nb,"M",$main_error);
}

if(!empty($type) && isset($type) && $old_type != $type) {
    insert_plug_conf("PLUG_TYPE",$nb,$type,$main_error);

    //If second regulation is deactivated but the type of plug change, we also change default value for second regulation:
    if($second_regul == "False") {
        switch ($type) {
            case "extractor":
            case "intractor":
            case "ventilator":
            case "heating":
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"70",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"H",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
                break;
            case "pumpfiling":
            case "pumpempting":
            case "pump":
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"22",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"L",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
                break;
            case "humidifier":
            case "dehumidifier":
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"35",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"T",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
                break;
            default:
                insert_plug_conf("PLUG_REGUL_VALUE",$nb,"70",$main_error);
                insert_plug_conf("PLUG_SENSO",$nb,"H",$main_error);
                insert_plug_conf("PLUG_SENSS",$nb,"+",$main_error);
                insert_plug_conf("PLUG_SECOND_TOLERANCE",$nb,"0",$main_error);
                break;
        }
    }
}

// Save module
if(!empty($module) && isset($module) && $old_module != $module) {
    insert_plug_conf("PLUG_MODULE",$nb,$module,$main_error);
}

// Save module number
if(!empty($num_module) && isset($num_module) && $old_num_module != $num_module) {
    insert_plug_conf("PLUG_NUM_MODULE",$nb,$num_module,$main_error);
}

// Save options
if(isset($module_options) && $old_module_options != $module_options) {
    insert_plug_conf("PLUG_MODULE_OPTIONS",$nb,$module_options,$main_error);
}

// Save options
if(!empty($module_output) && isset($module_output) && $old_module_output != $module_output) {
    insert_plug_conf("PLUG_MODULE_OUTPUT",$nb,$module_output,$main_error);
}

if(count($main_error)>0) {
    echo json_encode("0");
} else {
    echo json_encode("1");
}

?>
