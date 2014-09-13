<?php 

    if(strcmp($_COOKIE["PHPSESSID"],"")==0) {
        unset($_COOKIE["PHPSESSID"]);
    }


    $session_id = $_GET['session_id'];

    if (!isset($_SESSION)) {
        if(strcmp($session_id,"")!=0) {
            session_id($session_id);
        }
        session_start();
    }

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
        insert_configuration(strtoupper($_GET['variable']),"",$main_error);
    } else {
        // Save configuration
        insert_configuration(strtoupper($_GET['variable']),$_GET['value'],$main_error);
    }

    //Special configuration:
    if(isset($_GET['variable'])) {
        switch(strtoupper($_GET['variable'])) {
            case 'SHOW_COST': configure_menu("cost",$_GET['value']);
                     break;
            case 'WIFI': configure_menu("wifi",$_GET['value']);
                     break;
        }
    }

    if(count($main_error)>0) {
        foreach($main_error as $error) {
            echo json_encode($error);
        }
    } else {
        echo json_encode("");
    }

?>
