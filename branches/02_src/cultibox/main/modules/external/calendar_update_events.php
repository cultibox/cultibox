<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_get_common.php');
require_once('../../../main/libs/db_set_common.php');
require_once('../../../main/libs/utilfunc.php');
require_once('../../../main/libs/utilfunc_sd_card.php');


if (  isset($_GET["title"]) && !empty($_GET["title"])
   && isset($_GET["start"]) && !empty($_GET["start"])
   && isset($_GET["end"])   && !empty($_GET["end"])
   && isset($_GET["id"])    && !empty($_GET["id"])
   && isset($_GET["color"]) && !empty($_GET["color"])) {

    $title   = $_GET["title"];
    $start   = $_GET["start"];
    $end     = $_GET["end"];
    $id      = $_GET["id"];
    $color   = $_GET["color"];
    $sd_card = $_GET["card"];


    if (!isset($_GET["important"]) || empty($_GET["important"])) {  
        $important=0;
    } else {
        $important=1;
    }

    $main_error=array();
    

    if($db = db_priv_pdo_start()) {
        if((isset($_GET["desc"]))&&(!empty($_GET["desc"]))) {
            $description=$db->quote($_GET["desc"]);
        } 


        if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
UPDATE `calendar` SET `Title`={$db->quote($title)},`StartTime`="{$start}",`EndTime`="{$end}",`Color`="{$color}", `Description`={$description}, `Important`={$important} WHERE `Id` = {$id}
EOF;
        } else {
            $sql = <<<EOF
UPDATE `calendar` SET `Title`={$db->quote($title)},`StartTime`="{$start}",`EndTime`="{$end}", `Color`="{$color}", `Description`= NULL, `Important`={$important} WHERE `Id` = {$id}
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


        if ((strcmp("$start",$res[0]['StartTime'])==0)&&(strcmp("$end",$res[0]['EndTime'])==0)) {
            echo "$start - $end";
            if ((isset($sd_card))&&(!empty($sd_card))) {
                $calendar = array();
                calendar\read_event_from_db($calendar,strtotime($start), strtotime($end));
                
                // Read event from XML
                foreach (calendar\get_external_calendar_file() as $fileArray)
                {
                    if ($fileArray['activ'] == 1)
                        calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start)-7200,strtotime($end));
                }
            
                write_calendar($sd_card,$calendar,$main_error,strtotime($start),strtotime($end));
                $plgidx=create_plgidx($calendar);
                if(count($plgidx)>0) {
                    write_plgidx($plgidx,$sd_card);
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

            $calendar = array();
            calendar\read_event_from_db($calendar,strtotime($start), strtotime($end));

            foreach (calendar\get_external_calendar_file() as $fileArray)
            {
                if ($fileArray['activ'] == 1)
                {
                    calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start)-7200,strtotime($end));
                }
            }

            write_calendar($sd_card,$calendar,$main_error,strtotime($start),strtotime($end));
            $plgidx=create_plgidx($calendar);
            if(count($plgidx)>0) {
                write_plgidx($plgidx,$sd_card);
            }
        }
    }
} else {
    echo "-1";
}

?>
