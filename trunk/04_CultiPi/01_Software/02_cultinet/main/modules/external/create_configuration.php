<?php

    require_once('../../libs/utilfunc.php');

    $return=create_network_file($_GET);
    echo json_encode($return);

?>
