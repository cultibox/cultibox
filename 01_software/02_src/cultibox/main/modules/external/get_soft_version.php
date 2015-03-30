<?php 

    $ret = array();
    $err="";

    exec("dpkg -s cultipi|grep Version|awk -F \"Version: \" '{print $2}'",$ret[0],$err);
    exec("dpkg -s cultibox|grep Version|awk -F \"Version: \" '{print $2}'",$ret[1],$err);
    exec("dpkg -s cultiraz|grep Version|awk -F \"Version: \" '{print $2}'",$ret[2],$err);
    exec("dpkg -s cultitime|grep Version|awk -F \"Version: \" '{print $2}'",$ret[3],$err);
    exec("dpkg -s culticonf|grep Version|awk -F \"Version: \" '{print $2}'",$ret[4],$err);
    exec("dpkg -s culticam|grep Version|awk -F \"Version: \" '{print $2}'",$ret[5],$err);
    if(is_file("/VERSION")) {
        exec("cat /VERSION",$ret[6],$err);
    } else {
        $ret[6]="000000";
    }

    echo json_encode($ret);
?>
