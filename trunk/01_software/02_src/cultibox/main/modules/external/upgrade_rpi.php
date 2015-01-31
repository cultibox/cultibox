<?php 

$err="";
$output=array();
exec("sudo /etc/cron.daily/cultipi norestart",$output,$err);

?>
