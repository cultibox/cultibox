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
    
    $action = "";
    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    if((isset($_POST['action'])) && (!empty($_POST['action']))) {
        $action=$_POST['action'];
    }
    
    wifi\check_db();
    
    $ret_array = array();
    
    switch ($action) {
        case "getSensors" :
       
            $ret_array = wifi\getSensorOfSynoptic();
            
            break;
        case "getPlugs" :
            echo "i égal 1";
            break;
        case "getOtherItems" :
            echo "i égal 2";
            break;
        case "updatePosition" :
            $elem = "";
            if((isset($_POST['elem'])) && (!empty($_POST['elem']))) {
                $elem=$_POST['elem'];
            }
            $x = "";
            if((isset($_POST['x'])) && (!empty($_POST['x']))) {
                $x=$_POST['x'];
            }
            $y = "";
            if((isset($_POST['y'])) && (!empty($_POST['y']))) {
                $y=$_POST['y'];
            }
            wifi\updatePosition($elem,$x,$y);
            break;
        default:
            break;
    }
    
    // Return the array
    echo json_encode($ret_array);
 
?>
