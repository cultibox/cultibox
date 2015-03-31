<?php

if((isset($_GET['action']))&&(!empty($_GET['action']))) {
   $action=$_GET['action'];

    if(strcmp("$action","enable")==0) {
       if(!is_file("/var/lock/culticam_snapshot")) {
        exec("touch /var/lock/culticam_snapshot",$output,$err); 
       }
    } 

    if(strcmp("$action","disable")==0) {
        if(is_file("/var/lock/culticam_snapshot")) {
            exec("sudo mv /var/lock/culticam_snapshot /tmp/",$output,$err);
        }
    }
}






?>
