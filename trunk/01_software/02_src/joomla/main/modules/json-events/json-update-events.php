<?php


if((isset($_POST["title"]))&&(!empty($_POST["title"]))&&(isset($_POST["start"]))&&(!empty($_POST["start"]))&&(isset($_POST["end"]))&&(!empty($_POST["end"]))&&(isset($_POST["id"]))&&(!empty($_POST["id"]))) {

    $title=$_POST["title"];
    $start=$_POST["start"];
    $end=$_POST["end"];
    $id=$_POST["id"];


    $link = mysql_connect('localhost','cultibox','cultibox');
    if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db('cultibox');

        $sql = <<<EOF
UPDATE `calendar` SET `Title`="{$title}",`StartTime`="{$start}",`EndTime`="{$end}" WHERE `Id` = {$id}
EOF;

        echo $sql;
        
        $res = mysql_query($sql);
}

?>
