<?php 

$output=array();
$upgrade=array();
$err="";

exec("sudo apt-get update -o Dir::Etc::sourcelist=\"sources.list.d/cultibox.list\"  -o Dir::Etc::sourceparts=\"-\" -o APT::Get::List-Cleanup=\"0\"",$output,$err);
exec("sudo apt-get -u upgrade --assume-no|grep cultibox",$upgrade,$err);

echo json_encode($upgrade);

?>
