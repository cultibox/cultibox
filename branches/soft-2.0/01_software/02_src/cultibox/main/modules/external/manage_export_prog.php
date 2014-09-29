<?php

require_once('../../libs/config.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc.php');

if((isset($_GET['selected_plug']))&&(!empty($_GET['selected_plug']))&&(isset($_GET['program_index']))&&(!empty($_GET['program_index']))) {
    $selected_plug=$_GET['selected_plug'];
    $program_index=$_GET['program_index'];

    if((isset($_GET['type']))&&(!empty($_GET['type']))&&(strcmp($_GET['type'],"set")==0)) {   
        $type="set";
    } else {
        $type="";
    }


    if(is_dir("../../../tmp/export")) {
        advRmDir("../../../tmp/export");
    }
    @mkdir("../../../tmp/export");
        

    if(strcmp("$type","set")==0) {
        if(is_dir("../../../tmp/export/programs")) advRmDir("../../../tmp/export/programs");
        @mkdir("../../../tmp/export/programs");
        $plug=1;
        $nb_plugs=$selected_plug;
        $path="../../../tmp/export/programs";
    } else {
        $plug=$selected_plug;
        $nb_plugs=$selected_plug;
        $path="../../../tmp/export";
    }


    $name=program\get_field_from_program_index("name",$program_index);
    for($plug;$plug<=$nb_plugs;$plug++) {
        if(strcmp("$type","set")==0) { 
            program\export_program($plug,$program_index,$path."/program_plug{$plug}.csv");
        } else {
            program\export_program($plug,$program_index,$path."/program_plug{$plug}_{$name}.csv");
        }
    }


    if(strcmp("$type","set")==0) {
        $source_dir = "../../../tmp/export/programs";
        $zip_file = "../../../tmp/export/programs_{$name}.zip";
        $file_list = scandir($source_dir);

        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
            foreach ($file_list as $file) {
                if (($file!=$zip_file)&&($file!=".")&&($file!="..")) {
                    $zip->addFile($source_dir."/".$file,$file);
                } 
            }
            $zip->close();
            advRmDir("../../../tmp/export/programs");
            echo json_encode(basename($zip_file));
        } else {
            echo json_encode("0");
        }
    } else {
        echo json_encode("program_plug{$selected_plug}_{$name}.csv");
    }
} else {
    echo json_encode("0");
}




?>
