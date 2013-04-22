<?php

require_once('../../../main/libs/db_common.php');


if((isset($_POST["title"]))&&(!empty($_POST["title"]))&&(isset($_POST["start"]))&&(!empty($_POST["start"]))&&(isset($_POST["end"]))&&(!empty($_POST["end"]))&&(isset($_POST["id"]))&&(!empty($_POST["id"]))&&(isset($_POST["color"]))&&(!empty($_POST["color"]))) {

    $title=$_POST["title"];
    $start=$_POST["start"];
    $end=$_POST["end"];
    $id=$_POST["id"];
    $color=$_POST["color"];
    

    if((isset($_POST["desc"]))&&(!empty($_POST["desc"]))) {
            $description=$_POST["desc"];
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

    if($db = db_priv_pdo_start()) {
        $db->exec("$sql");
        $db=null;
    }
}

?>
