<?php 

    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    
    $ret = array();
    $ret[0]="";
    $ret[1]="";
    $err="";
    
    switch ($action) {
        case "logs_mysql" :
            exec("sudo cat /var/log/mysql/mysql.log 2>/dev/null | tail -100",$ret[0],$err);

            exec("sudo cat /var/log/mysql/mysql.err 2>/dev/null | tail -100",$ret[1],$err);
            break;
        case "logs_httpd" :
            $ret[0]="";
            exec("sudo cat /var/log/lighttpd/error.log 2>/dev/null | tail -100",$ret[1],$err);
            
            break;
        case "logs_cultipi":
            exec("sudo cat /var/log/cultipi/cultipi.log 2>/dev/null | tail -100",$ret[0],$err);
            $ret[1]="";
            break;
        case "logs_service":
            exec("sudo cat /var/log/cultipi/cultipi-service.log 2>/dev/null | tail -100",$ret[0],$err);
            $ret[1]="";
            break;
        default:
            break;
    }
    
    echo json_encode($ret);
 
?>
