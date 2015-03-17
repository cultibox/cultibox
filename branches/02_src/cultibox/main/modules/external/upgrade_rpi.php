<?php 

$err="";
$output=array();
exec("sudo apt-get install -y  --force-yes --only-upgrade -o Dpkg::Options::=\"--force-confdef\" -o Dpkg::Options::=\"--force-confold\" cultibox cultitime cultiraz culticonf cultipi",$output,$err);


?>
