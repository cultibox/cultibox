<?php 

require_once('../../libs/utilfunc.php');


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
        if((isset($_SESSION['LOAD_LOG']))&&(!empty($_SESSION['LOAD_LOG']))) {
            echo $_SESSION['LOAD_LOG'];
        }
        break;
    case 'sd_card':
        echo get_sd_card();
        break;
    case 'important_calendar':
        if((isset($_SESSION['IMPORTANT']))&&(!empty($_SESSION['IMPORTANT']))) {
            echo $_SESSION['IMPORTANT'];
        }
        break;
    case 'tooltip_msg_box':
        if((isset($_SESSION['TOOLTIP_MSG_BOX']))&&(!empty($_SESSION['TOOLTIP_MSG_BOX']))) {
            echo $_SESSION['TOOLTIP_MSG_BOX'];
        }
        break;
}

?>
