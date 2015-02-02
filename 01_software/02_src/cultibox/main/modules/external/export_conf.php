<?php

require_once('../../libs/config.php');

if(isset($GLOBALS['MODE']) && $GLOBALS['MODE'] == "cultipi") {
    exec("/var/www/cultibox/run/backup.sh",$output,$err);
else {
    $os=php_uname('s');
    //Retrieve SD path depending of the current OS:
    switch($os) {
        case 'Linux':
            exec("/opt/cultibox/run/backup.sh",$output,$err);
            break;
        case 'Mac':
        case 'Darwin':
            exec("/Applications/cultibox/run/backup.sh",$output,$err);
            break;
        case 'Windows NT':
            exec("C:\cultibox\run\backup.sh",$output,$err);
            break;
    }
}
$file="../../../tmp/export/backup_cultibox.sql";

if(is_file("$file")) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
