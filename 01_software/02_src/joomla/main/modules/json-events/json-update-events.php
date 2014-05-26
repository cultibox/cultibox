<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_get_common.php');
require_once('../../../main/libs/db_set_common.php');
require_once('../../../main/libs/utilfunc.php');
require_once('../../../main/libs/utilfunc_sd_card.php');


if (  isset($_POST["title"]) && !empty($_POST["title"])
   && isset($_POST["start"]) && !empty($_POST["start"])
   && isset($_POST["end"])   && !empty($_POST["end"])
   && isset($_POST["id"])    && !empty($_POST["id"])
   && isset($_POST["color"]) && !empty($_POST["color"])) {

    $title   = $_POST["title"];
    $start   = $_POST["start"];
    $end     = $_POST["end"];
    $id      = $_POST["id"];
    $color   = $_POST["color"];
    $sd_card = $_POST["card"];

    if (!isset($_POST["important"]) || empty($_POST["important"])) {  
        $important=0;
    } else {
        $important=1;
    }

    $main_error=array();
    

    if($db = db_priv_pdo_start()) {
        if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
            $description=$db->quote($_POST["desc"]);
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
            if ((isset($sd_card))&&(!empty($sd_card))) {
                if ($start == $end || !isset($end) ||empty($end))
                {
                    $calendar = array();
                    calendar\read_event_from_db($calendar,$start);
                    
                    // Read event from XML
                    foreach (calendar\get_external_calendar_file() as $fileArray)
                    {
                        if ($fileArray['activ'] == 1)
                            calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start));
                    }
                
                    write_calendar($sd_card,$calendar,$main_error,strtotime($start));
                    calendar\write_plgidx($sd_card,$calendar);

                } else {
                    $calendar = array();
                    calendar\read_event_from_db($calendar,$start,$end);
                    
                    // Read event from XML
                    foreach (calendar\get_external_calendar_file() as $fileArray)
                    {
                        if ($fileArray['activ'] == 1)
                            calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start),strtotime($end));
                    }
                
                    write_calendar($sd_card,$calendar,$main_error,strtotime($start),strtotime($end));
                    calendar\write_plgidx($sd_card,$calendar);

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
            calendar\read_event_from_db($calendar,$start,$end);

            foreach (calendar\get_external_calendar_file() as $fileArray)
            {
                if ($fileArray['activ'] == 1)
                {
                    calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start),strtotime($end));
                }
            }

            write_calendar($sd_card,$calendar,$main_error,strtotime($start),strtotime($end));
            calendar\write_plgidx($sd_card,$calendar);

        }
    }
} else {
    echo "-1";
}

?>
