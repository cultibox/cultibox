<?php

if(isset($_GET['date']) && !empty($_GET['date'])) {
    if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.") !== false) {
    
        // Read actual hour
        exec("date +%s",$output,$err);

        //If we can't get the current timestamp or date is not set (date before 2014), we set the date:
        if(count($output)!=1 || $output[0]<1388530000)
        {

            // Update RPi hour using client hour
            try {
                $ret = exec("sudo /bin/date -s @".$_GET['date'],$output,$err);
            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }

            // Update RTC Hour using RPi hour
            try {
                $ret = exec('tclsh "/opt/cultitime/setDate.tcl"');
            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }

            echo "set date";
        }
    }
}

?>
