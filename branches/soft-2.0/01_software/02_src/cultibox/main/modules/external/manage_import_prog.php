<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');

$main_error=array();

if((isset($_GET['selected_plug']))&&(!empty($_GET['selected_plug']))&&(isset($_GET['program_index_id']))&&(!empty($_GET['program_index_id']))&&(isset($_GET['file']))&&(!empty($_GET['file']))) {
    $import_selected=$_GET['selected_plug'];
    $program_index_id=$_GET['program_index_id'];
    $file=$_GET['file'];
    $chk_insert=true;

    if((isset($_GET['type']))&&(!empty($_GET['type']))&&(strcmp($_GET['type'],"set")==0)) {
        $type="set";
    } else {
        $type="";
    }

    $program_index = program\get_field_from_program_index ("program_idx",$program_index_id);
    $name = program\get_field_from_program_index ("name",$program_index_id);

    $target_path="../../../tmp/import/".basename($file);
    if(!is_dir("../../../tmp/import")) @mkdir("../../../tmp/import");
    if(!is_dir("../../../tmp/export")) @mkdir("../../../tmp/export");

    if(strcmp("$type","set")==0) {
        $zip = new ZipArchive;
        if ($zip->open($target_path) === TRUE) {
            $zip->extractTo('../../../tmp/import');
            $zip->close();
        } 
        $plug=1;
        $nb_plugs=$import_selected;
    } else {
        $plug=$import_selected;
        $nb_plugs=$import_selected;
    }

    for($plug;$plug<=$nb_plugs;$plug++) {
       if(strcmp("$type","set")==0) {
           $data_prog=generate_program_from_file("../../../tmp/import/program_plug${plug}.csv",$plug,$program_index,$main_error);
       } else {
           $data_prog=generate_program_from_file("../../../tmp/import/".basename($file),$plug,$program_index,$main_error);
       }
       if(count($data_prog)>0) {
           program\export_program($plug,$program_index,"../../../tmp/export/program_plug${plug}_save.csv");
           clean_program($plug,$program_index,$main_error);

           if(!insert_program($data_prog,$main_error,$program_index)) {
               $data_prog=generate_program_from_file("../../../tmp/export/program_plug${plug}_save.csv",$plug,$program_index,$main_error);
               insert_program($data_prog,$main_error,$program_index);
               $chk_insert=false;
           } 
       } 
       unset($data_prog);
    }

    if(is_dir("../../../tmp/export")) {
        advRmDir("../../../tmp/export/");
    }

    if(is_dir("../../../tmp/import")) {
        advRmDir("../../../tmp/import/");
    }

    if(!$chk_insert) {
        echo json_encode("1");
    } else {
        echo json_encode("0");
    }
} else {
    echo json_encode("1");
}


?>
