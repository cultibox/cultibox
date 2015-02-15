<?php

    require_once('../../libs/config.php');

    if((isset($_GET['width'])) && (!empty($_GET['width']))) {
        $width=$_GET['width'];
    } else {
        $width=640;
    }

    if((isset($_GET['height'])) && (!empty($_GET['height']))) {
        $height=$_GET['height'];
    } else {
        $height=480;
    }

    $options="";
    if((isset($_GET['brightness'])) && (!empty($_GET['brightness'])) && (strcmp($_GET['brightness'],"-1")!=0)) {
        $options=" -s brightness=".$_GET['brightness']."%";
    } 

    if((isset($_GET['contrast'])) && (!empty($_GET['contrast'])) && (strcmp($_GET['contrast'],"-1")!=0)) {
        $options="$options -s contrast=".$_GET['contrast']."%";
    }

    if((isset($_GET['palette'])) && (!empty($_GET['palette'])) && (strcmp($_GET['palette'],"-1")!=0)) {
       $options=" -p ".$_GET['palette']." $options";
    }



    exec("ls /dev/video* 2>/dev/null",$output,$err);
    if(count($output)==0) {
       echo json_encode("1");
    } else {
        exec("sudo fswebcam -r ".$width."x".$height." --no-banner $options ".$GLOBALS['BASE_PATH']."tmp/webcam.jpg 2>&1",$output,$err);
        echo json_encode("0");
    }
?>
