<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');

$main_error=array();

if((isset($_GET['type']))&&(!empty($_GET['type']))) {
    $type=$_GET['type'];
}

if((!isset($type))||(empty($type))) {
    echo json_encode("0");
} else {
    if(is_dir("../../../tmp/export")) {
        advRmDir("../../../tmp/export");
    }
    @mkdir("../../../tmp/export");

     logs\export_table_csv("$type",$main_error);
     $file="../../../tmp/export/$type.csv";
     if (($file != "") && (file_exists("./$file"))) {
        echo json_encode($type.".csv");
     } else {
        echo json_encode("0");
    }
}


?>
