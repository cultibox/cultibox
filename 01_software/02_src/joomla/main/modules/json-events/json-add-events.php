<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_get_common.php');
require_once('../../../main/libs/db_set_common.php');
require_once('../../../main/libs/utilfunc.php');
require_once('../../../main/libs/utilfunc_sd_card.php');

$title=$_POST["title"];
$start=$_POST["start"];
$end=$_POST["end"];
$color=$_POST["color"];
$sd_card=$_POST["card"];
$important=$_POST["important"];
$main_error=array();




if((isset($title))&&(!empty($title))&&(isset($start))&&(!empty($start))&&(isset($end))&&(!empty($end))&&(isset($color))&&(!empty($color))) {
        if($db = db_priv_pdo_start()) {
            if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
                $description=$db->quote($_POST["desc"]);    
            }

            if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Description`,`Color`,`Important`) VALUES({$db->quote($title)}, "{$start}", "{$end}", {$description}, "{$color}","${important}");
EOF;

            } else {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Color`,`Important`) VALUES("{$title}", "{$start}", "{$end}", "{$color}","${important}");
EOF;
            }

            $db->exec("$sql");
            $db=null;

            $calendar = array();
            
            if((isset($sd_card))&&(!empty($sd_card))) {
            
                if ($start == $end)
                    $end = "";
            
                // Read event from DB
                calendar\read_event_from_db($calendar,$start,$end);
                
                // Read event from XML
                foreach (calendar\get_external_calendar_file() as $fileArray)
                {
                    if ($fileArray['activ'] == 1)
                        calendar\read_event_from_XML($fileArray['filename'],$calendar,0,strtotime($start));
                }
                    
                // Write event into SD card
                write_calendar($sd_card,$calendar,$main_error,strtotime($start));

            }

        }
}
echo "1";
?>
