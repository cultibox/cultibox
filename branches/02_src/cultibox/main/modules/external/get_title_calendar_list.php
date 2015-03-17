<?php

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/config.php');

$title = calendar\get_title_list();

if(count($title)>0) {
    echo json_encode($title);
} else {
    echo "-1";
}

?>
