<?php 

if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=$_GET['name'];
}

if((isset($_GET['value']))&&(!empty($_GET['value']))) {
    $value=$_GET['value'];
}

if((isset($_GET['duration']))&&(!empty($_GET['duration']))) {
    $duration=$_GET['duration'];
}

if((!isset($name))||(empty($name))||(!isset($value))||(empty($value))||(!isset($duration))||(empty($duration))) {
    echo json_encode("0");
} else {
    setcookie(strtoupper($name), "$value", time()+$duration,"/",false,false);
    echo json_encode("1");
}

?>
