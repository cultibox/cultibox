<?php

exec("/var/www/cultibox/run/backup.sh",$output,$err);
$file="../../../tmp/export/backup_cultibox.sql";

if(is_file("$file")) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
