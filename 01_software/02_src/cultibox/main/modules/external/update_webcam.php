<?php 


        if((isset($_GET['id']))&&(!empty($_GET['id']))) {
            $id=$_GET['id'];
        } else {
            $id=0;
        }

        if((isset($_GET['palette']))&&(!empty($_GET['palette']))) {
            $palette=$_GET['palette'];
        } else {
            $palette="MJPEG";
        }

        if((isset($_GET['brightness']))&&(!empty($_GET['brightness']))) {
            $brightness=$_GET['brightness'];
        } else {
            $brightness="0";
        }

        if((isset($_GET['contrast']))&&(!empty($_GET['contrast']))) {
            $contrast=$_GET['contrast'];
        } else {
            $contrast="0";
        }


        if((isset($_GET['resolution']))&&(!empty($_GET['resolution']))) {
            $resolution=$_GET['resolution'];
        } else {
            $resolution="400x300";
        }

        if((isset($_GET['title']))&&(!empty($_GET['title']))) {
            $title=$_GET['title'];
        } else {
            $title="Webcam ".$id;
        }

        $conf=array();

        $conf[]="device /dev/video".$id;
        $conf[]="resolution ".$resolution;
        $conf[]="set brightness=".$brightness."%";
        $conf[]="set contrast=".$contrast."%";
        $conf[]="skip 10";
        $conf[]="title \"".$title."\"";
        $conf[]="top-banner";
        $conf[]="font /usr/share/fonts/truetype/msttcorefonts/arial.ttf";
        $conf[]="timestamp \"%d-%m-%Y %H:%M:%S\"";
        $conf[]="save /var/www/cultibox/tmp/webcam${id}.jpg";
        $conf[]="palette $palette";

        if($f=fopen("/tmp/webcam".$id.".conf","w")) {
            foreach($conf as $myInf) {
                fputs($f,"$myInf\n");
            }   
            fclose($f);

            exec("sudo mv /tmp/webcam".$id.".conf /etc/culticam/",$output,$err);
        }

        echo json_encode("0");
?>
