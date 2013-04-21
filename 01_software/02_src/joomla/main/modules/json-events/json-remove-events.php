<?php

if((isset($_POST["id"]))&&(!empty($_POST["id"]))) {
    $id=$_POST["id"];

    $db = new PDO('mysql:host=localhost;dbname=cultibox;charset=utf8', 'cultibox', 'cultibox');
    $sql = <<<EOF
DELETE FROM `calendar` WHERE `Id` = {$id} AND `External` = 0
EOF;
}
     $db->exec("$sql");
     $db=null;
?>
