<?php

    require_once('../../../main/libs/config.php');
    require_once('../../../main/libs/db_get_common.php');
    require_once('../../../main/libs/db_set_common.php');
    require_once('../../../main/libs/utilfunc.php');
    require_once('../../../main/libs/utilfunc_sd_card.php');


    $id=$_POST["id"];

    $sd_card=$_POST["card"];
    
    $main_error=array();

    $sql = "DELETE FROM calendar WHERE Id = \"" . $id . "\";";

    if($db = db_priv_pdo_start()) {
        $sth=$db->prepare($sql);
        $sth-> execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);
        $db=null;

        if((isset($sd_card))&&(!empty($sd_card))) {

            $calendar = array();
            calendar\read_event_from_db($calendar);
            
            // Read event from XML
            foreach (calendar\get_external_calendar_file() as $fileArray)
            {
                if ($fileArray['activ'] == 1)
                    calendar\read_event_from_XML($fileArray['filename'],$calendar);
            }
                
            write_calendar($sd_card,$calendar,$main_error);
        }
    }
    
    print_r($main_error);
    
?>
