<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_get_common.php');
require_once('../../../main/libs/db_set_common.php');
require_once('../../../main/libs/utilfunc.php');
require_once('../../../main/libs/utilfunc_sd_card.php');

$title=$_GET["title"];
$start=$_GET["start"];
$end=$_GET["end"];
$color=$_GET["color"];
$sd_card=$_GET["card"];
$important=$_GET["important"];
$main_error=array();


if(    isset($title) && !empty($title)
    && isset($start) && !empty($start)
    && isset($end)   && !empty($end)
    && isset($color) && !empty($color)) {
    
        if($db = db_priv_pdo_start()) {
        
            if((isset($_GET["desc"]))&&(!empty($_GET["desc"]))) {
                $description=$db->quote($_GET["desc"]);    
            } else {
                // Waring '' are very important !
                $description = "''";
            }


            $sql = "INSERT INTO calendar (Title, StartTime, EndTime, Description, Color, Important)"
                . " VALUES ({$db->quote($title)}, '{$start}', '{$end}', {$description}, '{$color}', '${important}');";

            try {
                $db->exec($sql);
            } catch(PDOException $e) {
                print_r($e->getMessage());
            }

            $db=null;

            $calendar = array();
            
            if((isset($sd_card))&&(!empty($sd_card))) {
                if ($start == $end)
                    $end = "";
            
                // Read event from DB
                calendar\read_event_from_db($calendar,strtotime($start), strtotime($end));
                
                // Read event from XML
                foreach (calendar\get_external_calendar_file() as $fileArray)
                {
                    if ($fileArray['activ'] == 1)
                        calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start)-7200, strtotime($end));
                }
                    
                // Write event into SD card
                write_calendar($sd_card,$calendar,$main_error,strtotime($start), strtotime($end));
            }

        }
}
echo "1";
?>
