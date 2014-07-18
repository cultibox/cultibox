<?php 

    if (!isset($_SESSION)) {
        session_start();
    }

    // Define language using post lang parameter
    $_SESSION['LANG'] = "fr_FR";
    switch($_POST['lang']) {
        case 'fr':
            $_SESSION['LANG'] = "fr_FR";
            break;
        case 'en':
            $_SESSION['LANG'] = "en_GB";
            break;
        case 'it':
            $_SESSION['LANG'] = "it_IT";
            break;
        case 'de':
            $_SESSION['LANG'] = "de_DE";
            break;
        case 'es':
            $_SESSION['LANG'] = "es_ES";
            break;
    }

    // Include libraries
    if (file_exists('../../libs/db_get_common.php') === TRUE)
    {
        // Script call by Ajax
        require_once('../../libs/config.php');
        require_once('../../libs/db_get_common.php');
        require_once('../../libs/db_set_common.php');
        require_once('../../libs/utilfunc.php');
        require_once('../../libs/utilfunc_sd_card.php');
        require_once('../../libs/debug.php');
    }

    // Define language
    $_SESSION['SHORTLANG'] = get_short_lang($_SESSION['LANG']);
    __('LANG');


    if(!empty($_POST['name'])) {
           $name=$_POST['name']; 
    } else {
            echo json_encode("");
            return 1;
    }

    if(!empty($_POST['value'])) {
           $value=$_POST['value'];
    } else {
            echo json_encode("");
            return 1;
    }

    if(!empty($_POST['id'])) {
           $id=$_POST['id'];
    } else {
            echo json_encode("");
            return 1;
    }


    if(strcmp("$id","all")==0) {
        for($nb=1;$nb<=get_configuration("NB_PLUGS",$main_error);$nb++) {
            insert_plug_conf(strtoupper($name),$nb,$value,$main_error);
        }
    } else {
        insert_plug_conf(strtoupper($name),$id,$value,$main_error);
    }

    if(count($main_error)>0) {
        foreach($main_error as $error) {
            echo json_encode($error);
        }
    }

    echo json_encode("");
?>
