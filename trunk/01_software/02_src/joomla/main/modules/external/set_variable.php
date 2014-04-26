<?php 

if (!isset($_SESSION)) {
    session_start();
}


if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=$_GET['name'];
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
}



if((!isset($name))||(empty($name))||(!isset($value))||(empty($value))) {
    echo json_encode("0");
} else {
    $_SESSION[strtoupper($name)]=$value;
    echo json_encode("1");
}

?>
