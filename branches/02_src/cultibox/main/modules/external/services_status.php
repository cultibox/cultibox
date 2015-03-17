<?php 

    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    
    $ret = array();
    $ret_var="";
    $err="";
    
    switch ($action) {
        case "restart_cultipi" :
            exec("sudo /etc/init.d/cultipi force-reload >/dev/null 2>&1",$ret,$ret_var);
            break;
        case "status_cultipi" :
            exec("/etc/init.d/cultipi status >/dev/null 2>&1",$ret,$ret_var);
            break;
        case "restart_rpi" :
            exec("sudo /sbin/shutdown -r now >/dev/null 2>&1",$ret,$ret_var);
            break;
        default:
            break;
    }
    
    echo json_encode($ret_var);
 
?>
