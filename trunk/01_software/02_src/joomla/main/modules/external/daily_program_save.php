<?php 

    $session_id = $_GET['session_id'];
    if (!isset($_SESSION)) {
        session_id($session_id);
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
    
    // Get a programm index not used
    $program_idx = program\get_programm_number_empty();

    // create programm line
    program\add_row_program_idx($_GET['name'], $_GET['version'], $program_idx, "","Programme " . $_GET['name']);
    
    // Save programm
    program\copy($_GET['input'],$program_idx);
    
    // Create return array
    $ret_array = array();
    
    $ret_array['name'] = $_GET['name'];
    $ret_array['version'] = $_GET['version'];
    $ret_array['program_idx'] = $program_idx;
    
    // Return the array
    echo json_encode($ret_array);
 
?>
