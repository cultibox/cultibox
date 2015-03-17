<?php

require_once('../../libs/config.php');

$file="../../../tmp/export/backup_cultibox.sql";


if(isset($GLOBALS['MODE']) && $GLOBALS['MODE'] == "cultipi") {
    if(is_file("$file")) exec("mv $file /tmp/",$output,$err);
    if(is_file("$file.zip")) exec("mv $file.zip /tmp/",$output,$err);

    exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > /var/www/cultibox/tmp/export/backup_cultibox.sql",$output,$err);
    exec("zip -j ../../../tmp/export/backup_cultibox.sql.zip $file",$output,$err);
} else {
    $os=php_uname('s');
    switch($os) {
        case 'Linux':
            exec("/opt/cultibox/bin/mysqldump --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > /opt/cultibox/htdocs/cultibox/tmp/export/backup_cultibox.sql",$output,$err);
            break;
        case 'Mac':
        case 'Darwin':
            if(is_file("$file.zip")) exec("mv $file.zip /tmp/",$output,$err);
            exec("/Applications/cultibox/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > /Applications/cultibox/xamppfiles/htdocs/cultibox/tmp/export/backup_cultibox.sql",$output,$err);
            exec("zip -j ../../../tmp/export/backup_cultibox.sql.zip $file",$output,$err);
            break;
        case 'Windows NT':
            exec("C:\\cultibox\\xampp\\mysql\\bin\\mysqldump.exe --defaults-extra-file=C:\\cultibox\\xampp\\mysql\\bin\\my-extra.cnf -h 127.0.0.1 --port=3891 cultibox > C:\\cultibox\\xampp\\htdocs\\cultibox\\tmp\\export\\backup_cultibox.sql",$output,$err);
            break;
    }
}




if(is_file("$file.zip")) {
    echo json_encode("2");
} else if(is_file("$file")) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
