<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

if((isset($_GET['value']))&&(!empty($_GET['value']))&&(isset($_GET['program_index']))&&(!empty($_GET['program_index']))) {
    $value=$_GET['value'];
    $program_index_id=$_GET['program_index'];
    $program_index = program\get_field_from_program_index ("program_idx",$program_index_id);
    $value=explode(",",$value);
    $main_error=array();

    foreach($value as $reset_selected) {
        clean_program($reset_selected,$program_index,$main_error);
    }
}

?>
