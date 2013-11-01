<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_common.php');
require_once('../../../main/libs/utilfunc.php');

$title=$_POST["title"];
$start=$_POST["start"];
$end=$_POST["end"];
$color=$_POST["color"];
$sd_card=$_POST["card"];
$main_error=array();







if((isset($title))&&(!empty($title))&&(isset($start))&&(!empty($start))&&(isset($end))&&(!empty($end))&&(isset($color))&&(!empty($color))) {
        if($db = db_priv_pdo_start()) {
            if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
                $description=$db->quote($_POST["desc"]);    
            }

            if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Description`,`Color`,`External`) VALUES({$db->quote($title)}, "{$start}", "{$end}", {$description}, "{$color}","0");
EOF;

            } else {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Color`,`External`) VALUES("{$title}", "{$start}", "{$end}", "{$color}","0");
EOF;
            }

            $db->exec("$sql");
            $db=null;

            if((isset($sd_card))&&(!empty($sd_card))) {
                if((strcmp("$start","$end")==0)||(!isset($end))||(empty($end))) {
                    $calendar=create_calendar_from_database($main_error,$start);
                    if(count($calendar)>0) {
                        clean_calendar($sd_card,$start);
                        write_calendar($sd_card,$calendar,$main_error,$start);
                    }
                } else {
                    $calendar=create_calendar_from_database($main_error,$start,$end);
                    if(count($calendar)>0) {
                        clean_calendar($sd_card,$start,$end);
                        write_calendar($sd_card,$calendar,$main_error,$start,$end); 
                    }
                }
            }

        }
}
echo "1";
?>
