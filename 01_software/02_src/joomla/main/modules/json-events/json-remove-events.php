<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_common.php');
require_once('../../../main/libs/utilfunc.php');

if((isset($_POST["id"]))&&(!empty($_POST["id"]))) {
    $id=$_POST["id"];

    $sd_card=$_POST["card"];
    $main_error=array();

    $sql = <<<EOF
DELETE FROM `calendar` WHERE `Id` = {$id} AND `External` = 0
EOF;
}

    $sql_old= <<<EOF
SELECT  * FROM `calendar` WHERE `Id` = {$id}
EOF;

    if($db = db_priv_pdo_start()) {
        $sth=$db->prepare("$sql_old");
        $sth-> execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);

        $db->exec("$sql");
        $db=null;

        if((isset($sd_card))&&(!empty($sd_card))) {
            if(count($res)>0) {
                if((strcmp($res[0]['StartTime'],$res[0]['EndTime'])==0)||(strcmp($res[0]['EndTime'],"")==0)) {
                    clean_calendar($sd_card,$res[0]['StartTime']);
                    $calendar=create_calendar_from_database($main_error,$res[0]['StartTime']);
                    if(count($calendar)>0) {
                        write_calendar($sd_card,$calendar,$main_error,$res[0]['StartTime']);
                    }
                } else {
                    clean_calendar($sd_card,$res[0]['StartTime'],$res[0]['EndTime']);
                    $calendar=create_calendar_from_database($main_error,$res[0]['StartTime'],$res[0]['EndTime']);
                    if(count($calendar)>0) {
                        write_calendar($sd_card,$calendar,$main_error,$res[0]['StartTime'],$res[0]['EndTime']);
                    }
                }
            }
        }
    }
?>
