<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

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
    if(strcmp("$type","theorical")==0) {
        $power=get_theorical_power($select_plug,$cost_type,$main_error,$check);
    } else {
        $startTime = strtotime("$startday 12:00");

        $thisDate = date('Y-m-d', $startTime); // 2010-05-01, 2010-05-02, etc
        $data_power=get_data_power($thisDate,$thisDate,$select_plug,$main_error,"short");

        $power=get_real_power($data_power,$cost_type,$main_error);

        unset($data_power);
    }

    echo "$power";
}

