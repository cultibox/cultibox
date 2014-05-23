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
    
    // Define language
    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
    __('LANG');
    
    
    // delete program and index
    insert_configuration(strtoupper($_POST['variable']),$_POST['value'],$main_error);
    
    // If update conf is defined, update sd configuration
    if ($_POST['updateConf'] != "undefined")
    {
        // search sd card
        $sd_card = get_sd_card();
        
        // Update conf file
        update_sd_conf_file($sd_card, $_POST['variable'],$_POST['value'],$main_error);
    }
    
    print_r($main_error);
   
 
?>