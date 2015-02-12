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

    if((!isset($_GET['value']))||(empty($_GET['value']))) {
        insert_webcam(strtolower($_GET['variable']),"",$main_error);
    } else {
        insert_webcam(strtolower($_GET['variable']),$_GET['value'],$main_error);
    }

    if(count($main_error)>0) {
        foreach($main_error as $error) {
            echo json_encode($error);
        }
    } else {
        echo json_encode("");
    }

?>
