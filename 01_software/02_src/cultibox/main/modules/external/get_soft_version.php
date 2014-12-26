<?php 

    $ret = array();
    $err="";

    exec("dpkg -s cultipi|grep Version|awk -F \"Version: \" '{print $2}'",$ret[0],$err);
    exec("dpkg -s cultinet|grep Version|awk -F \"Version: \" '{print $2}'",$ret[1],$err);
    exec("dpkg -s cultibox|grep Version|awk -F \"Version: \" '{print $2}'",$ret[2],$err);
    
    echo json_encode($ret);
 
?>
