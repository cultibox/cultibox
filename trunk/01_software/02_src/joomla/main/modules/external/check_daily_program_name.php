<?php

require_once('../../libs/db_get_common.php');
require_once('../../libs/config.php');

if((isset($_GET['name']))&&(!empty($_GET['name']))) {
    $name=$_GET['name'];
} else {
    echo json_encode("");
    return 0;
}

  $sql = <<<EOF
SELECT name FROM program_index WHERE name LIKE '{$name}' LIMIT 1;
EOF;

  $db=db_priv_pdo_start();
  try {
      $sth=$db->prepare("$sql");
      $sth->execute();
      $res=$sth->fetchAll(PDO::FETCH_ASSOC);
  } catch(PDOException $e) {
      $ret=$e->getMessage();
  }
  $db=null;

  if(count($res)>0) {
    echo json_encode("NOK");
    return 0;
  }
  echo json_encode("");

?>
