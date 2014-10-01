<?php

require_once('../../libs/config.php');

if((isset($_GET['page']))&&(!empty($_GET['page']))) {
   $page=$_GET['page'];

    require_once('../../libs/config.php');
    require_once($GLOBALS['BASE_PATH'].'main/libs/db_get_common.php');
    require_once($GLOBALS['BASE_PATH'].'main/libs/db_set_common.php');
    require_once($GLOBALS['BASE_PATH'].'main/libs/debug.php');
    require_once $GLOBALS['BASE_PATH'].'main/libs/utilfunc.php';
    require_once($GLOBALS['BASE_PATH'].'main/libs/utilfunc_sd_card.php');

    if((isset($_GET['get_array']))&&(!empty($_GET['get_array']))) {
        $get_array=json_decode($_GET['get_array'],true);
        foreach(array_keys($get_array) as $get) {
            ${$get}=$get_array[$get];

            if($GLOBALS['DEBUG_TRACE']) {        
                echo $get."-----".$get_array[$get]."<br />";
            }
        }
    }

    ob_start();
    include $GLOBALS['BASE_PATH'].'main/scripts/'.$page.'.php';
    include $GLOBALS['BASE_PATH'].'main/scripts/post_script.php';
    include $GLOBALS['BASE_PATH'].'main/libs/js/page_'.$page.'.js';
    include $GLOBALS['BASE_PATH'].'main/templates/'.$page.'.html';
    $include = ob_get_clean();
    echo $include;
}
?>
