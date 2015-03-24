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
    
    $sd_card = $_GET['sd_card'];
    $force_rtc_offset_value = false;
    if (isset($_GET['force_rtc_offset_value']))
    {
        $force_rtc_offset_value = true;
    }

    
    $main_error = array();
    $main_info = array();

    // If a cultibox SD card is plugged, manage some administrators operations: check the firmware and log.txt files, check if 'programs' are up tp date...
    $return = check_and_update_sd_card($sd_card,$main_info,$main_error,$force_rtc_offset_value);
    if($return > 1) {
        $main_error[] = get_error_sd_card_update_message($return);
    }

    // Search and update log information form SD card
    if(!isset($GLOBALS['MODE']) || $GLOBALS['MODE'] != "cultipi") {
        $return=sd_card_update_log_informations($sd_card);
        if( $return > 1 ) {
            $main_error[]=get_error_sd_card_update_message($return);
        }
    }

    // Create output array
    $ret_array = array();
    $ret_array['info'] = $main_info;
    $ret_array['error'] = $main_error;

    //return it in JSON format
    echo json_encode($ret_array);
 
?>
