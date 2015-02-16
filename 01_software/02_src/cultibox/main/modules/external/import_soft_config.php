<?php

require_once('../../libs/config.php');


if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
    $file="../../../tmp/import/".$_GET['filename'];
    if(is_file("$file")) {
        if(isset($GLOBALS['MODE']) && $GLOBALS['MODE'] == "cultipi") {
                  $finfo = finfo_open(FILEINFO_MIME_TYPE); 
                  $extension=finfo_file($finfo, "$file");
                  finfo_close($finfo);
       
                  if(strpos("$extension","zip")!==false) {
                        exec("/usr/bin/unzip -o \"$file\" -d ../../../tmp/import/|/bin/grep inflating|/usr/bin/awk -F\": \" '{print $2}'",$output,$err);
                        if(trim($output[0])!="") {
                            $file=trim($output[0]);
                        } else {
                            echo json_encode("1");
                            return 0;
                        }
                  }
                  exec("/usr/bin/mysql --defaults-extra-file=/var/www/cultibox/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < \"$file\"",$output,$err);
        } else {
            $os=php_uname('s');
            switch($os) {
                case 'Linux':
                    exec("/opt/cultibox/bin/mysql --defaults-extra-file=/opt/cultibox/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < \"$file\"",$output,$err);
                    break;
                case 'Mac':
                case 'Darwin':
                    exec("/Applications/cultibox/xamppfiles/bin/mysql --defaults-extra-file=/Applications/cultibox/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox < \"$file\"",$output,$err);
                    break;
                case 'Windows NT':
                    $file="c:\\cultibox\\xampp\\htdocs\\cultibox\\tmp\\import\\".$_GET['filename'];
                    exec("c:\\cultibox\\xampp\\mysql\\bin\\mysql.exe --defaults-extra-file=C:\\cultibox\\xampp\\mysql\\bin\\my-extra.cnf -h 127.0.0.1 --port=3891  cultibox < \"$file\"",$output,$err);
                    break;
            }
        }

        if($err!=0) {
             echo json_encode("1");
             return 0;
         } else {
             echo json_encode("0");
             return 0;
         }
    }
}
echo json_encode("1");

?>
