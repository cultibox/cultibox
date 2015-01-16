<?php

exec("/var/www/cultibox/run/backup.sh",$output,$err); 

if($err==0) {
    if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
        $file="../../../tmp/import/".$_GET['filename'];
        if(is_file("$file")) {
            exec("mv /home/cultipi/cultibox/backup_cultibox.sql /home/cultipi/cultibox/backup_cultibox.sql.old",$output,$err);
            if($err==0) {
                exec("mv $file /home/cultipi/cultibox/backup_cultibox.sql",$output,$err);
                if($err==0) {
                    exec("/var/www/cultibox/run/load.sh auto",$output,$err);
                    if($err!=0) {
                        exec("mv /home/cultipi/cultibox/backup_cultibox.sql.old /home/cultipi/cultibox/backup_cultibox.sql",$output,$err);             
                        exec("/var/www/cultibox/run/load.sh auto",$output,$err);
                        echo json_encode("1");
                        return 0;
                    } else {
                        echo json_encode("0");
                        return 0;
                    }
                }
            }
        }
    }
}
echo json_encode("1");

?>
