<?php

    require_once('../../libs/config.php');

    exec("ls -l /dev/video* 2>/dev/null",$output,$err);
    if(count($output)==0) {
       echo json_encode("1");
    } else {
        $return=array();
        $webcam=false;
        for($i=0;$i<$GLOBALS['MAX_WEBCAM'];$i++) {
            foreach($output as $device) {
                if($i==substr(trim($device), -1)) {
                    $webcam=true;
                }
            }
            if($webcam) {
                $return[$i]="1";
                $webcam=false;
            } else {
                $return[$i]="0";
            }
        } 
        echo json_encode($return);
    }
?>
