<?php

if((isset($_POST["id"]))&&(!empty($_POST["id"]))) {
    $id=$_POST["id"];

    $link = mysql_connect('localhost','cultibox','cultibox');
    if (!$link) { die('Could not connect: ' . mysql_error()); }
        mysql_select_db('cultibox');

        $sql = <<<EOF
DELETE FROM `calendar` WHERE `Id` = {$id} AND `External` = 0
EOF;
        $res = mysql_query($sql);
}

?>
