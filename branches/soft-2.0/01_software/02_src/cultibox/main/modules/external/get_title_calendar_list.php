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
