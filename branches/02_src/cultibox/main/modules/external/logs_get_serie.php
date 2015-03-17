<?php

// Load libraries
require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

// Init arrays
$main_error = array();
$retarray = array();

// Get and format input parameters
$startDay = strtotime ($_GET['startDate']);

if ($_GET['month'] == "day") {
    //$endDay = strtotime("+1 day", strtotime($_GET['startDate']));
    $endDay = strtotime($_GET['endDate']);
} else {
    $date = $_GET['startDate'] . "-01";
    $endDay = strtotime("+1 month", strtotime($date));
}


$plug_number = "";
if(isset($_GET['plug']))
    $plug_number = $_GET['plug'];
    
$datatype = $_GET['datatype'];

// If user want to see a plug program
switch ($datatype)
{
    case "program" :
        // Retrieve program curve
        $retarray['plug_1']['data'] = program\get_plug_programm($plug_number,$startDay,$endDay,$_GET['month']);

        // Read information
        $retInfo = program\get_curve_information("program",$plug_number - 1);
        
        // Init return array
        $retarray['plug_1']['name']     = $retInfo['name'] . " " . __('PLUG_MENU') . " " . $plug_number;
        $retarray['plug_1']['color']    = $retInfo['color'] ;
        $retarray['plug_1']['legend']   = $retInfo['legend'] ;
        $retarray['plug_1']['yaxis']    = $retInfo['yaxis'] ;
        $retarray['plug_1']['curveType'] = $retInfo['curveType'] ;
        $retarray['plug_1']['unit']     = $retInfo['unit'] ;
        $retarray['plug_1']['fake_log'] = "0" ;
        break;
        
    case "power" :
        // Retrieve power curve
        $retarray['plug_1']['data'] = program\get_plug_power($plug_number,$startDay,$endDay,$_GET['month']);
        
        // Read information
        $retInfo = program\get_curve_information("power",$plug_number - 1);
        
        // Init return array
        $retarray['plug_1']['name']     = $retInfo['name'] . " " . __('PLUG_MENU') . " " . $plug_number;
        $retarray['plug_1']['color']    = $retInfo['color'] ;
        $retarray['plug_1']['legend']   = $retInfo['legend'] ;
        $retarray['plug_1']['yaxis']    = $retInfo['yaxis'] ;
        $retarray['plug_1']['curveType'] = $retInfo['curveType'] ;
        $retarray['plug_1']['unit']     = $retInfo['unit'] ;
        $retarray['plug_1']['fake_log'] = "0" ;
        break;
        
    case "logs" :

        if ($_GET['month'] == "day") {
            $endDay = strtotime("+1 day", strtotime($_GET['startDate']));
        }

        // Gets type of each sensor logged
        $db_sensors = logs\get_sensor_db_type($_GET['sensor']);

        // Retrieve logs curve
        $logsValue = logs\get_sensor_log($_GET['sensor'],$startDay,$endDay,$_GET['month'],$db_sensors[0]['ratio']);
        
        // Search if there are fake
        $fake = logs\are_fake_logs($_GET['sensor'],$startDay,$endDay,$_GET['month']);

        // Read information about this sensor 
        // Todo : Super moche
        $retInfo = program\get_curve_information($db_sensors[0]['type'] . "1",$_GET['sensor'] - 1);
        
        $retarray['sensor_1']['data'] = $logsValue[0];
        
        // Init return array
        $retarray['sensor_1']['name']       = $retInfo['name'] . " (" . __('SENSOR') . " " . $_GET['sensor'] . " )";
        $retarray['sensor_1']['color']      = $retInfo['color'] ;
        $retarray['sensor_1']['legend']     = $retInfo['legend'] ;
        $retarray['sensor_1']['yaxis']      = $retInfo['yaxis'] ;
        $retarray['sensor_1']['curveType']  = $retInfo['curveType'] ;
        $retarray['sensor_1']['unit']       = $retInfo['unit'] ;
        $retarray['sensor_1']['fake_log']   = $fake ;
        
        // If there is a second sensor
        if ($db_sensors[0]['type'] == 2)
        {
        
            $retInfo = program\get_curve_information($db_sensors[1]['type'] . "2");
        
            $retarray['sensor_2']['data'] = $logsValue[1];
            
            $retarray['sensor_2']['name']       = $retInfo['name'] . " (" . __('SENSOR') . " " . $_GET['sensor'] . " )";
            $retarray['sensor_2']['color']      = $retInfo['color'] ;
            $retarray['sensor_2']['legend']     = $retInfo['legend'] ;
            $retarray['sensor_2']['yaxis']      = $retInfo['yaxis'] ;
            $retarray['sensor_2']['curveType']  = $retInfo['curveType'] ;
            $retarray['sensor_2']['unit']       = $retInfo['unit'] ;
            $retarray['sensor_2']['fake_log']   = $fake ;
        }

        break;        
}

// Encode in JSON format and return array
echo json_encode($retarray);

?>
