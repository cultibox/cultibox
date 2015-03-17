<?php 

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


    if(!empty($_GET['name'])) {
       $name=$_GET['name']; 
    } else {
        echo json_encode("");
        return 1;
    }

    if(!empty($_GET['value'])) {
           $value=$_GET['value'];
    } else {
        echo json_encode("");
        return 1;
    }

    if(!empty($_GET['id'])) {
       $id=$_GET['id'];
    } else {
        echo json_encode("");
        return 1;
    }


    if($id == "all") {
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
    } else {
        echo json_encode("");
    }
?>
