<?php 

    $ret = array();
    $err="";

    exec("dpkg -s cultipi|grep Version|awk -F \"Version: \" '{print $2}'",$ret[0],$err);
    exec("dpkg -s cultibox|grep Version|awk -F \"Version: \" '{print $2}'",$ret[2],$err);
    exec("dpkg -s cultiraz|grep Version|awk -F \"Version: \" '{print $2}'",$ret[3],$err);
    exec("dpkg -s cultitime|grep Version|awk -F \"Version: \" '{print $2}'",$ret[4],$err);
    
    echo json_encode($ret);
 
?>
