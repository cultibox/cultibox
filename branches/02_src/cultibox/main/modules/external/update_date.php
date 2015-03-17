<?php

if((isset($_GET['date']))&&(!empty($_GET['date']))) {
    if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.")!==false) {
        exec("date +%s",$output,$err);

        //If we can't get the current timestamp or date is not set (date before 2014), we set the date:
        if((count($output)!=1)||($output[0]<1388530000)) {
            exec("sudo /bin/date -s @".$_GET['date'],$output,$err);
            echo "set date";
        }
    }
}

?>
