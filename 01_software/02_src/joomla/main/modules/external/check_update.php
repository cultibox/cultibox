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

    $_SESSION['UPDATE_CHECKED'] = "";
    
    // Check for update availables. If an update is availabe, the link to this update is displayed with the informations div
    if(strcmp(get_configuration("CHECK_UPDATE",$main_error),"True")==0) {

        // If check has not been tested
        if(!isset($_SESSION['UPDATE_CHECKED']) || empty($_SESSION['UPDATE_CHECKED'])) {
            if($sock=@fsockopen("${GLOBALS['REMOTE_SITE']}", 80)) {
            
                // Check versiuns
                if(check_update_available($version,$main_error)) {
                    $_SESSION['UPDATE_CHECKED'] = "True";
                    echo "ok" . __('INFO_UPDATE_AVAILABLE')." <a target='_blank' href=".$GLOBALS['WEBSITE'].">".__('HERE')."</a>";
                } else {
                    $_SESSION['UPDATE_CHECKED'] = "False";
                    echo "na";
                }
            } else {
                // If website is not available don't retry an other time during session
                $_SESSION['UPDATE_CHECKED'] = "NA";
                echo "fl" . __('ERROR_REMOTE_SITE');
            }
        } else {
            echo "na";
        }
    }
?>