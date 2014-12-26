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
            if(is_file("/var/logs/mysql.log")) {
                exec("cat /var/logs/mysql.log",$ret[0],$err);
            } else {
                $ret[0]="";
            }

            if(is_file("/var/logs/mysql.err")) {
                exec("cat /var/logs/mysql.err",$ret[1],$err);
            } else {
                $ret[1]="";
            }
            break;
        case "logs_httpd" :
            $ret[0]="";

            if(is_file("/var/log/lighttpd/error.log")) {
                exec("cat /var/log/lighttpd/error.log",$ret[1],$err);
            } else {
                $ret[1]="";
            }
            
            break;
        case "logs_cultipi":
            if(is_file("/var/logs/cultipi.log")) {
                exec("cat /var/logs/cultipi.log",$ret[0],$err);
            } else {
                $ret[0]="";
            }

            $ret[1]="";
            break;
        default:
            break;
    }
    
    echo json_encode($ret);
 
?>
