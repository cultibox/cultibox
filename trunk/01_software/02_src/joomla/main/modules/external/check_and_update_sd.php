<?php 

    if (!isset($_SESSION)) {
        session_start();
    }

    // Define language using post lang parameter
    $_SESSION['LANG'] = "fr_FR";
    switch($_POST['lang']) {
        case 'fr': 
            $_SESSION['LANG'] = "fr_FR";
            break;
        case 'en': 
            $_SESSION['LANG'] = "en_GB";
            break;
        case 'it': 
            $_SESSION['LANG'] = "it_IT";
            break;
        case 'de': 
            $_SESSION['LANG'] = "de_DE";
            break;
        case 'es': 
            $_SESSION['LANG'] = "es_ES";
            break;
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
    
    // Define languaage
    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
    __('LANG');
    
    $sd_card = $_POST['sd_card'];
    
    $main_error = array();
    $main_info = array();

    // If a cultibox SD card is plugged, manage some administrators operations: check the firmaware and log.txt files, check if 'programs' are up tp date...
    $return=check_and_update_sd_card($sd_card,$main_info,$main_error);
    if($return > 1) {
        $main_error[]=get_error_sd_card_update_message($return);
    }

    // Search and update log information form SD card
    $return=sd_card_update_log_informations($sd_card);
    if( $return > 1 ) {
        $main_error[]=get_error_sd_card_update_message($return);
    }

    // Create output array
    $ret_array = array();
    $ret_array['info'] = $main_info;
    $ret_array['error'] = $main_error;
    
    //return it in JSON format
    echo json_encode($ret_array);
 
?>
