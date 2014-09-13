<?php 

if(strcmp($_COOKIE["PHPSESSID"],"")==0) {
    unset($_COOKIE["PHPSESSID"]);
}


$session_id = $_GET['session_id'];
if (!isset($_SESSION)) {
   if(strcmp($session_id,"")!=0) {
       session_id($session_id);
   }
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
