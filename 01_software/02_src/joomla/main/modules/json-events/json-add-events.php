<?php

$title=utf8_decode($_POST["title"]);
$start=$_POST["start"];
$end=$_POST["end"];
$color=$_POST["color"];



if((isset($title))&&(!empty($title))&&(isset($start))&&(!empty($start))&&(isset($end))&&(!empty($end))&&(isset($color))&&(!empty($color))) {
            if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
                $description=utf8_decode(($_POST["desc"]));    
            }

            
            $link = mysql_connect('localhost','cultibox','cultibox');
            if (!$link) { die('Could not connect: ' . mysql_error()); }
            mysql_select_db('cultibox');

            if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Description`,`Color`,`External`) VALUES("{$title}", "{$start}", "{$end}", "{$description}", "{$color}","0");
EOF;

            } else {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Color`,`External`) VALUES("{$title}", "{$start}", "{$end}", "{$color}","0");
EOF;
            }
        $res = mysql_query($sql);
}

?>
