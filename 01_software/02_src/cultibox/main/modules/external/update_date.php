<?php

if((isset($_GET['date']))&&(!empty($_GET['date']))) {
    if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.")!==false) {
        exec("sudo /bin/date -s @".$_GET['date'],$output,$err);
    }
}

?>
