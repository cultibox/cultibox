<?php 


    if((isset($_GET['id']))&&(!empty($_GET['id']))) {
        $id=$_GET['id'];

        if((isset($_GET['palette']))&&(!empty($_GET['palette']))) {
            $palette=$_GET['palette'];
        } else {
            $palette="MJPEG";
        }

        if((isset($_GET['brightness']))&&(!empty($_GET['brightness']))) {
            $palette=$_GET['brightness'];
        } else {
            $palette="50";
        }

        if((isset($_GET['contrast']))&&(!empty($_GET['contrast']))) {
            $palette=$_GET['contrast'];
        } else {
            $palette="50";
        }


        if((isset($_GET['resolution']))&&(!empty($_GET['resolution']))) {
            $palette=$_GET['resolution'];
        } else {
            $palette="320x240";
        }

        $conf=array();
        
        $conf[]="device /dev/video".$id;
        $conf[]="background";
        $conf[]="resolution ".$resolution;
        $conf[]="set brightness=$brightness%";
        $conf[]="set contrast=$contrast%";
        $conf[]="top-banner";
        $conf[]="font /usr/share/fonts/truetype/msttcorefonts/arial.ttf"
        $conf[]="timestamp \"%d-%m-%Y %H:%M:%S (%Z)\"";
        $conf[]="save /var/www/cultibox/tmp/webcam${id}.jpg"
        $conf[]="palette $palette";

        echo json_encode("0");
    } else {
        echo json_encode("0");
    }

?>
