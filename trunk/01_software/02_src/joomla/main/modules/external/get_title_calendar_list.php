<?php

if (!isset($_SESSION)) {
    session_start();
}

if((isset($_POST['lang']))&&(!empty($_POST['lang']))) {
    $lang=$_POST['lang'];
    $_SESSION['LANG'] = $lang;

    require_once('../../libs/utilfunc.php');
    require_once('../../libs/db_get_common.php');
    require_once('../../libs/config.php');

    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
    __('LANG');

    $title=get_title_list();

    if(count($title)>0) {
        echo json_encode($title);
    } else {
        echo "-1";
    }
} else {
    echo "-1";
}

?>
