<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_common.php');
require_once('../../../main/libs/utilfunc.php');


if((isset($_POST["title"]))&&(!empty($_POST["title"]))&&(isset($_POST["start"]))&&(!empty($_POST["start"]))&&(isset($_POST["end"]))&&(!empty($_POST["end"]))&&(isset($_POST["id"]))&&(!empty($_POST["id"]))&&(isset($_POST["color"]))&&(!empty($_POST["color"]))) {

    $title=$_POST["title"];
    $start=$_POST["start"];
    $end=$_POST["end"];
    $id=$_POST["id"];
    $color=$_POST["color"];

    $sd_card=$_POST["card"];
    $main_error=array();
    

    if($db = db_priv_pdo_start()) {

        if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
            $description=$db->quote($_POST["desc"]);
        }


        if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
UPDATE `calendar` SET `Title`={$db->quote($title)},`StartTime`="{$start}",`EndTime`="{$end}",`Color`="{$color}", `Description`={$description} WHERE `Id` = {$id}
EOF;
        } else {
            $sql = <<<EOF
UPDATE `calendar` SET `Title`={$db->quote($title)},`StartTime`="{$start}",`EndTime`="{$end}", `Color`="{$color}" WHERE `Id` = {$id}
EOF;
        }

        $sql_old= <<<EOF
SELECT  * FROM `calendar` WHERE `Id` = {$id}
EOF;

        $sth=$db->prepare("$sql_old");
        $sth-> execute();
        $res=$sth->fetchAll(PDO::FETCH_ASSOC);

        $db->exec("$sql");
        $db=null;


        if((strcmp("$start",$res[0]['StartTime'])==0)&&(strcmp("$end",$res[0]['EndTime'])==0)) {
            if((isset($sd_card))&&(!empty($sd_card))) {
                if((strcmp("$start","$end")==0)||(!isset($end))||(empty($end))) {
                    clean_calendar($sd_card,$start);
                    $calendar=create_calendar_from_database($main_error,$start);
                    if(count($calendar)>0) {
                        write_calendar($sd_card,$calendar,$main_error,$start);
                    }
                } else {
                    clean_calendar($sd_card,$start,$end);
                    $calendar=create_calendar_from_database($main_error,$start,$end);
                    if(count($calendar)>0) {
                        write_calendar($sd_card,$calendar,$main_error,$start,$end);
                    }
                }
            }
        } else {
            $timestart=date("U",strtotime($start));
            $timestartbis=date("U",strtotime($res[0]['StartTime']));
            $timeend=date("U",strtotime($end));
            $timeendbis=date("U",strtotime($res[0]['EndTime']));


            if($timestart>$timestartbis) {
                $start=$res[0]['StartTime'];
            }

            if($timeendbis>$timeend) {
                $end=$res[0]['EndTime'];
            }


            clean_calendar($sd_card,$start,$end);
            $calendar=create_calendar_from_database($main_error,$start,$end);
            if(count($calendar)>0) {
                    write_calendar($sd_card,$calendar,$main_error,$start,$end);
            }
        }
    }
}

?>
