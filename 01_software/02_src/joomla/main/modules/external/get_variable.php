<?php 

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=$_GET['name'];
}


if((!isset($name))||(empty($name))) {
    return 0;
}


switch ($name) {
    case 'load_log':
        if((isset($_SESSION['load_log']))&&(!empty($_SESSION['load_log']))) {
            echo $_SESSION['load_log'];
        }
        break;
}

?>
