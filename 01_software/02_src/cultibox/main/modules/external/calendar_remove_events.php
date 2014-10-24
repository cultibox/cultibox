<?php

    require_once('../../../main/libs/config.php');
    require_once('../../../main/libs/db_get_common.php');
    require_once('../../../main/libs/db_set_common.php');
    require_once('../../../main/libs/utilfunc.php');
    require_once('../../../main/libs/utilfunc_sd_card.php');


    $id=$_GET["id"];

    $sd_card=$_GET["card"];
    
    $main_error=array();
    
    // Init connexion to DB
    $db = db_priv_pdo_start();

    // Read days to update
    $sql = "SELECT * FROM calendar WHERE Id = '{$id}' ;";
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }
    
    $start = $res['StartTime'];
    $end = $res['EndTime'];

    // Delete event from calendar
    $sql = "DELETE FROM calendar WHERE Id = '{$id}' ;";

    $sth=$db->prepare($sql);
    $sth-> execute();
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    $db=null;

    if(isset($sd_card) && !empty($sd_card)) {

        $calendar = array();
        calendar\read_event_from_db($calendar,strtotime($start),strtotime($end));
        
        // Read event from XML
        foreach (calendar\get_external_calendar_file() as $fileArray)
        {
            if ($fileArray['activ'] == 1)
                calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start)-7200,strtotime($end));
        }
           
        write_calendar($sd_card,$calendar,$main_error,strtotime($start)-7200,strtotime($end));
    }
    
?>
