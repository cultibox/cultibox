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
    
    // Get a programm index not used
    $program_idx = program\get_programm_number_empty();

    // create programm line
    program\add_row_program_idx($_POST['name'], $_POST['version'], $program_idx, "","Programme " . $_POST['name']);
    
    // Save programm
    program\copy($_POST['input'],$program_idx);
    
    // Create return array
    $ret_array = array();
    
    $ret_array['name'] = $_POST['name'];
    $ret_array['version'] = $_POST['version'];
    $ret_array['program_idx'] = $program_idx;
    
    // Return the array
    echo json_encode($ret_array);
 
?>