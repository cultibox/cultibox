<?php


$title=$_POST["title"];
$start=$_POST["start"];
$end=$_POST["end"];



if((isset($title))&&(!empty($title))&&(isset($start))&&(!empty($start))&&(isset($end))&&(!empty($end))) {
            if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
                $description=$_POST["desc"];    
            }
            
            $link = mysql_connect('localhost','cultibox','cultibox');
            if (!$link) { die('Could not connect: ' . mysql_error()); }
            mysql_select_db('cultibox');

            if((isset($description))&&(!empty($description))) {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`,`Description`) VALUES("{$title}", "{$start}", "{$end}", "{$description}");
EOF;

            } else {
            $sql = <<<EOF
INSERT INTO `calendar`(`Title`,`StartTime`, `EndTime`) VALUES("{$title}", "{$start}", "{$end}");
EOF;
            }
        echo $sql;
        $res = mysql_query($sql);
}

?>
