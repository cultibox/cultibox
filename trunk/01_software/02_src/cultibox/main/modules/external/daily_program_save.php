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
    
    // Get a programm index not used
    $program_idx = program\get_programm_number_empty();

    // create programm line
    $id = program\add_row_program_idx($_GET['name'], $_GET['version'], $program_idx, "","Programme " . $_GET['name']);
    $program_index = program\get_field_from_program_index ("program_idx",$_GET['input']);
    
    // Save programm
    program\copy($program_index,$program_idx);
    
    // Create return array
    $ret_array = array();
    
    $ret_array['name'] = $_GET['name'];
    $ret_array['version'] = $_GET['version'];
    $ret_array['program_idx'] = $program_idx;
    $ret_array['id'] = $id;

    
    // Return the array
    echo json_encode($ret_array);
 
?>
