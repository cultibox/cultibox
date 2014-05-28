<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');


    $startDay = strtotime ($_GET['startDate']);
    
    if ($_GET['month'] == "day") {
        $endDay = strtotime($_GET['startDate']);
    } else {
        $date = $_GET['startDate'] . "-01";
        $endDay = strtotime("+1 month", strtotime($date));
    }
    
    $plug_number = $_GET['plug'];
        
    $data_powers = program\get_plug_programm($plug_number,$startDay,$endDay,$_GET['month']);
        
    $retarray['data'] = $data_powers;
    $retarray['name'] = "Prise " . $plug_number;
    echo json_encode($retarray);

?>
