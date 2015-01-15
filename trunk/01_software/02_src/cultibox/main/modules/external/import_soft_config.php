<?php

exec("/var/www/cultibox/run/backup.sql",$output,$err); 

echo json_encode($err);

?>
