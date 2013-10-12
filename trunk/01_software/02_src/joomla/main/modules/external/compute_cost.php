<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_common.php');
require_once('../../libs/config.php');

if (!isset($_SESSION)) {
    session_start();
}

if((isset($_GET['startday']))&&(!empty($_GET['startday']))) {
    $startday=$_GET['startday'];
}

if((isset($_GET['select_plug']))&&(!empty($_GET['select_plug']))) {
    $select_plug=$_GET['select_plug'];
}

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}

if((isset($startday))&&(!empty($startday))&&(isset($select_plug))&&(!empty($select_plug))&&(isset($type))&&(!empty($type))) {
    $main_error=array();
    $cost_type = get_configuration("COST_TYPE",$main_error);
    $power=0;

    //Computing cost value:
    if(strcmp($select_plug,"distinct_all")!=0) {

        if(strcmp("$type","theorical")==0) {
            $power=get_theorical_power($select_plug,$cost_type,$main_error,$check);
        } else {
            $startTime = strtotime("$startday 12:00");
            $endTime = strtotime("$startday 12:00");

            for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
                $thisDate = date('Y-m-d', $i); // 2010-05-01, 2010-05-02, etc
                $data_power=get_data_power($thisDate,$thisDate,$select_plug,$main_error);
                $power=get_real_power($data_power,$cost_type,$main_error)+$real_power;
                unset($data_power);
            }
        }

        echo "$power";
} else {
    for($plugs=1;$plugs<=$nb_plugs;$plugs++) {
       $theorical_power=get_theorical_power($plugs,$cost_type,$main_error,$check);

       $startTime = strtotime("$startday 12:00");
       $endTime = strtotime("$endday 12:00");
       $real_power=0;

       for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
            $thisDate = date('Y-m-d', $i); // 2010-05-01, 2010-05-02, etc
            $data_power=get_data_power($thisDate,$thisDate,$plugs,$main_error);
            $real_power=get_real_power($data_power,$cost_type,$main_error)+$real_power;
            unset($data_power);
       }

       $data_price[]= array(
        "number" => $plugs,
        "real" => "$real_power",
        "theorical" => "$theorical_power",
        "title" => "$title",
        "color" => $GLOBALS['LIST_GRAPHIC_COLOR_PROGRAM'][$plugs-1]
       );
    }
}
}

