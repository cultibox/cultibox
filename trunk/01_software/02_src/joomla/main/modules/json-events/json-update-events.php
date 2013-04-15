<?php


if((isset($_POST["title"]))&&(!empty($_POST["title"]))&&(isset($_POST["start"]))&&(!empty($_POST["start"]))&&(isset($_POST["end"]))&&(!empty($_POST["end"]))&&(isset($_POST["id"]))&&(!empty($_POST["id"]))&&(isset($_POST["color"]))&&(!empty($_POST["color"]))) {

    $title=utf8_encode($_POST["title"]);
    $start=$_POST["start"];
    $end=$_POST["end"];
    $id=$_POST["id"];
    $color=$_POST["color"];
    

    if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
            $description=utf8_encode($_POST["desc"]);
    }

    if((isset($description))&&(!empty($description))) {
        $sql = <<<EOF
UPDATE `calendar` SET `Title`="{$title}",`StartTime`="{$start}",`EndTime`="{$end}",`Color`="{$color}", `Description`="{$description}" WHERE `Id` = {$id}
EOF;
    } else {
        $sql = <<<EOF
UPDATE `calendar` SET `Title`="{$title}",`StartTime`="{$start}",`EndTime`="{$end}", `Color`="{$color}" WHERE `Id` = {$id}
EOF;
    }

    $link = mysql_connect('localhost','cultibox','cultibox');
    if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db('cultibox');

        echo $sql;
        
        $res = mysql_query($sql);
}

?>
