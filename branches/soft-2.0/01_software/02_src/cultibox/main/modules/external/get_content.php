<?php

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
