<?php 

    // Include libraries
    if (file_exists('../../libs/db_get_common.php') === TRUE)
    {
        // Script call by Ajax
        require_once('../../libs/config.php');
        require_once('../../libs/db_get_common.php');
        require_once('../../libs/db_set_common.php');
        require_once('../../libs/utilfunc.php');
        require_once('../../libs/utilfunc_sd_card.php');
        require_once('../../libs/debug.php');
    }
    
    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    
    $ret = array();
    
    switch ($action) {
        case "mysql_logs" :
            if(is_file("/var/logs/mysql.log")) {
                exec("cat /var/logs/mysql.log");
            } else {
                $ret[0]="";
            }

            if(is_file("/var/logs/mysql.err")) {
                exec("cat /var/logs/mysql.err");
            } else {
                $ret[1]="";
            }
            break;
        case "httpd_logs" :
            
            break;
        case "cultipi_logs":
            break;
        default:
            break;
    }
    
    echo json_encode($ret);
 
?>
