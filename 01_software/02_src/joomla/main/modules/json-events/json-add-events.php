<?php

$title=utf8_decode($_POST["title"]);
$start=$_POST["start"];
$end=$_POST["end"];
$color=$_POST["color"];



if((isset($title))&&(!empty($title))&&(isset($start))&&(!empty($start))&&(isset($end))&&(!empty($end))&&(isset($color))&&(!empty($color))) {
            if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
                $description=utf8_decode(($_POST["desc"]));    
            }

            $db = new PDO('mysql:host=localhost;dbname=cultibox;charset=utf8', 'cultibox', 'cultibox');
            if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Description`,`Color`,`External`) VALUES("{$title}", "{$start}", "{$end}", "{$description}", "{$color}","0");
EOF;

            } else {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Color`,`External`) VALUES("{$title}", "{$start}", "{$end}", "{$color}","0");
EOF;
            }
            $db->exec("$sql");
            $db=null;
}

?>
