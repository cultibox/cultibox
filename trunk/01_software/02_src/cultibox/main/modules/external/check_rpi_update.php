<?php 

$output=array();
$upgrade=array();
$err="";

exec("ping -c 4 8.8.8.8 >/dev/null",$output,$err);

if($err==0) {
    exec("sudo apt-get -u upgrade --assume-no|grep cultibox",$upgrade,$err);
    echo json_encode($upgrade);
} else {
    echo json_encode("1");
}

?>
