<?php

    require_once('../../libs/config.php');

    exec("ls -l /dev/video* 2>/dev/null",$output,$err);
    if(count($output)==0) {
       echo json_encode("1");
    } else {
        $return=array();
        foreach($output as $device) {
            exec("sudo fswebcam -c ".$GLOBALS['BASE_PATH']."tmp/webcam".substr("$device", -1).".conf 2>&1",$output,$err);
            $return[]=substr("$device", -1);
        }
        echo json_encode($return);
    }
?>
