<?php

require_once('../../../main/libs/db_common.php');

if((isset($_POST["id"]))&&(!empty($_POST["id"]))) {
    $id=$_POST["id"];

    $sql = <<<EOF
DELETE FROM `calendar` WHERE `Id` = {$id} AND `External` = 0
EOF;
}
     if($db=db_priv_pdo_start()) {
        $db->exec("$sql");
        $db=null;
     }
?>
