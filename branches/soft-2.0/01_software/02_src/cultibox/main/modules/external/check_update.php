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

    
    // Create info and error array
    $main_error = array();
    $main_info = array();
    
    // Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
    // If check has not been tested
    if(!isset($_COOKIE['UPDATE_CHECKED']) || empty($_COOKIE['UPDATE_CHECKED'])) {
        
        // Try to connect, allow a timeout of 3 seconds
        if($sock=@fsockopen("${GLOBALS['REMOTE_SITE']}", 80, $errno, $errstr, 3)) {
            
            // Check version
            $version = get_configuration("VERSION",$main_error); //Current version of the software
                
            if(check_update_available($version,$main_error)) {
                $_COOKIE['UPDATE_CHECKED'] = "True";
                $main_info[] = __('INFO_UPDATE_AVAILABLE') . " <a target='_blank' href=" . $GLOBALS['WEBSITE'] . ">" .__('HERE'). "</a>";
            } else {
                //5mn before retrying to get the VERSION file:
                setcookie("UPDATE_CHECKED", "False", time()+300,"/",false,false);
            }
        } else {
            // If website is not available don't retry an other time during session
            // Wait 4hours before retrying:
            setcookie("UPDATE_CHECKED", "NA", time()+14400,"/",false,false);
            $main_error[] = __('ERROR_REMOTE_SITE');
        }
    }
    
    // Create output array
    $ret_array = array();
    $ret_array['info'] = $main_info;
    $ret_array['error'] = $main_error;
    
    //return it in JSON format
    echo json_encode($ret_array);
    
?>
