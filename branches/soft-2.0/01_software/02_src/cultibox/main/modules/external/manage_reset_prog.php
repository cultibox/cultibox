<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
    $value=explode(",",$value);
    $main_error=array();

    foreach($value as $reset_selected) {
        clean_program($reset_selected,1,$main_error);
    }
}

?>
