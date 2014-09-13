<?php

if(strcmp($_COOKIE["PHPSESSID"],"")==0) {
    unset($_COOKIE["PHPSESSID"]);
}

if(isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
} else {
    $session_id="";
}

if (!isset($_SESSION)) {
   if(strcmp($session_id,"")!=0) {
       session_id($session_id);
        echo "$session_id";
   }
   session_start();
}


if((isset($_GET['page']))&&(!empty($_GET['page']))) {
   $page=$_GET['page'];

    require_once('../../libs/config.php');
    require_once('../../libs/db_get_common.php');
    require_once('../../libs/db_set_common.php');
    require_once('../../libs/debug.php');
    require_once '../../libs/utilfunc.php';
    require_once('../../libs/utilfunc_sd_card.php');

    echo include("../../scripts/{$page}.php");
    echo include("../../libs/js/page_${page}.js");
    echo include("../../templates/{$page}.html");

}
?>
